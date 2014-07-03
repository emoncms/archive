<?php

/*
   All Emoncms code is released under the GNU Affero General Public License.
   See COPYRIGHT.txt and LICENSE.txt.

   ---------------------------------------------------------------------
   Emoncms - open source energy visualisation
   Part of the OpenEnergyMonitor project:
   http://openenergymonitor.org

   test commit
*/

// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

class FeedConvert
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function convert_power_to_histogram($powerfeed,$histogramfeed,$pot,$start,$end)
    {
        set_time_limit (500);

        $powerfeed = intval($powerfeed);
        $histogramfeed = intval($histogramfeed);
        $pot = intval($pot);

        if ($pot<1) $pot = 1;
        if ($powerfeed==0 || $histogramfeed==0) return false;

        $powerfeed = "feed_".trim($powerfeed)."";
        $histogramfeed = "feed_".trim($histogramfeed)."";

        $this->mysqli->query("TRUNCATE `$histogramfeed`");

        $result = $this->mysqli->query("SELECT * FROM $powerfeed WHERE `time`>'$start' AND `time`<'$end' order by time Asc"); 
	
        $lasttime = 0; 
        $kwh = 0;
        $savetime = 0;

        $time = time();
        $reftime = mktime(0, 0, 0, date("m",$time), date("d",$time), date("Y",$time));

        $data = array();
        $starttime = microtime(true);
        // We start by going through each datapoint starting from the oldest and advancing towards the most recent
        while($row = $result->fetch_array())
        {

          // As we advance forward in time, this part is used to detect if the time has advanced into a new day
          // if it has and its not the very first itteration then this means were at the point where we need to
          // save the histogram data calculated for that day in the histogram feed.
          $lastsavetime = $savetime;
          $savetime = $reftime - (floor(($reftime - $row['time'])/86400)*86400);

          if ($lastsavetime != $savetime && $lastsavetime!=0) 
          {
            // Each day may have several hundred entries in the feed table, each entry corresponds to the energy
            // used at that power value. We start here by itterating through the power values:
            foreach ($data as $item) 
            {
              $power = $item[0]; $kwh = $item[1];
              $this->mysqli->query("INSERT INTO $histogramfeed (time,data,data2) VALUES ('$lastsavetime','$kwh','$power')");
            }
            // Now that we have saved this day we reset the histogram data array ready for the next day
            $data = array();
          }  

          // Calculate energy used at different power values

          // If its not the first time as we need two datapoints to get the time between them 
          if ($lasttime!=0)
          {
            $value = $row['data'];

            // Filter for values greater than 0 and with an acceptable gap of time between datapoints
            if (($row['time'] - $lasttime)<(60*10) && $value>=0 )
            {
              // Power (J.s) x time (s), 3000 * 10 = 30000, 
              $kwhinc = ($value * ($row['time'] - $lasttime)) / 3600000.0;
             
              $value = round($value / $pot, 0, PHP_ROUND_HALF_UP) * $pot;

              if (!isset($data[$value][0])) $data[$value] = array(0,0);

              $data[$value][0] = $value;
              $data[$value][1] += $kwhinc;
            }
          }
          $lasttime = $row['time'];
        }

        return microtime(true)-$starttime;
    }
}
