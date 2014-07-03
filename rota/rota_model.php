<?php

  /*

    All Emoncms code is released under the GNU Affero General Public License.
    See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org

  */

class Rota
{

  private $mysqli;

  public function __construct($mysqli)
  {
    $this->mysqli = $mysqli;
  }

  public function parse_csv($csv)
  {
    //$csv = "1364331000,GREEN\n1364332000,RED\n1364332000,GREEN\n";
    $csv = str_replace(PHP_EOL,'|', $csv);
    $lines = explode('|',$csv);

    foreach ($lines as $line)
    {
      $line_array = explode(",",$line);
      $time = $line_array[0];
      $shift = $line_array[1];
      if ($time && $shift) $result = $this->mysqli->query("INSERT INTO rota (`time`,`feedid`) VALUES ('$time','$shift')");
    }
    return '';
  }

  public function get_rota_feed()
  {
    $currenttime = time();
    $result = $this->mysqli->query("SELECT * FROM rota WHERE `time`<'$currenttime' ORDER BY time DESC LIMIT 1");
    $row = $result->fetch_array();
    return (int) $row['feedid'];
  }

}
