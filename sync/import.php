<?php

  /*

    SEE: http://openenergymonitor.blogspot.co.uk/2012/09/data-portability-importing-feed-data.html

    All Emoncms code is released under the GNU Affero General Public License.
    See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org

  */

  // IMPORTANT SET THIS TO A UNIQUE PASSWORD OF YOUR CHOICE
  //if ($_GET['key'] != "xTC7005d") die;

  set_time_limit (120);

  // Ensure only one instance of the script can run at any one time.
  $fp = fopen("importlock", "w");
  if (! flock($fp, LOCK_EX | LOCK_NB)) { echo "Already running\n"; die; }

  // Connect to the database
  define('EMONCMS_EXEC', 1);

  require "../../process_settings.php";
  $mysqli = new mysqli($server,$username,$password,$database);

  // Fetch the import queue
  $result = $mysqli->query("SELECT * FROM importqueue ORDER BY `queid` Asc");

  // For each item in the queue
  while($row = $result->fetch_array())
  {
    $queid = $row['queid'];
    $feedname = "feed_".trim($row['localfeedid'])."";

    // Check if we have already downloaded part of the feed and get the last 
    // value entered so that we dont download and insert data that has already 
    // been inserted this makes this utility useful for syncing in general 
    // and in particlar backup that only downloads the latest changes.
    $start = 0;  
    $feed_result = $mysqli->query("SELECT * FROM $feedname ORDER BY time Desc LIMIT 1");
    if ($feed_result)
    {
      $feed_row = $feed_result->fetch_array();
      if ($feed_row[0]) $start = $feed_row[0];
    }

    // Open the file served from the export page on the remote server
    $url = $row['remoteurl'].'/feed/export.json?apikey='.$row['remotekey'].'&id='
    .$row['remotefeedid'].'&start='.$start;

    echo "Opening file $url\n";
    $fh = @fopen( $url, 'r' );

    $histogram = false;

    if ($fh)
    {
      // Read through the file
      $i = 0; $vals = "";
      while (($data = fgetcsv($fh, 0, ",")) !== FALSE) 
      {
        $feedtime = $data[0]; 
        $value = $data[1];

        if (count($data)==3) $histogram = true;

        if ($feedtime!='' && $value!='')
        {
          $i++;
          //Contruct values part of the query
          if ($i!=1) $vals .= ',';

          if (!$histogram) {
            $vals .= "('$feedtime','$value')";
          } else {
            $value2 = $data[2];
            $vals .= "('$feedtime','$value','$value2')";
          }

          // Execute query every 400 rows (same block size as export script)
          if ($i>400)
          {
            $i = 0;
            if ($vals && !$histogram) $mysqli->query("INSERT INTO $feedname (`time`,`data`) VALUES ".$vals);
            if ($vals && $histogram) $mysqli->query("INSERT INTO $feedname (`time`,`data`,`data2`) VALUES ".$vals);
            $vals = "";
          }
        }
      }

      // If there are lines to be inserted left over insert them here at the end
      if ($vals && !$histogram) $mysqli->query("INSERT INTO $feedname (`time`,`data`) VALUES ".$vals);
      if ($vals && $histogram) $mysqli->query("INSERT INTO $feedname (`time`,`data`,`data2`) VALUES ".$vals);
      $vals = "";
      fclose($fh);
    }

    echo "Transfer complete\n";
    echo "Deleting item $queid from queue\n";
    $mysqli->query("DELETE FROM importqueue WHERE queid = $queid");
  }

?>
