<?php

  error_reporting(E_ALL);
  ini_set('display_errors', 'on');


  define('EMONCMS_EXEC', 1);

  $fp = fopen("importlock", "w");
  if (! flock($fp, LOCK_EX | LOCK_NB)) { echo "Already running\n"; die; }
  
  chdir(dirname(__FILE__));

  require "../../process_settings.php";
  $mysqli = @new mysqli($server,$username,$password,$database);
  
  require "../feed/engine/TimestoreApi.php";
  $timestore = new TimestoreAPI($timestore_adminkey);

  $feedlistq = $mysqli->query("SELECT feedid,convertto FROM converttotimestore");

  while ($row = $feedlistq->fetch_array())
  {
    $feedid = $row['feedid'];
    
    $interval = (int) $row['convertto'];
    if ($interval>0)
    {
      $now = time();
      // IMPORTANT: This puts a limit on the range of timestamps that can be accepted
      // if you need to import datapoints that are older than 5 years and newer than the current time + 48 hours in the future
      // then change these values:
      $start = time()-(3600*24*365*5); // 5 years in past
      $end = time()+(3600*48);         // 48 hours in future

      echo "\n$feedid Creating timestore node ";
      print $timestore->create_node($feedid,$interval);
      echo "ok\n";

      $benchstart = microtime(true);
      
      $engine_result = $mysqli->query("SELECT engine FROM feeds WHERE `id` = '$feedid'");
      $engine_row = $engine_result->fetch_array();
      $engine = $engine_row['engine']; 

      if ($engine==Engine::MYSQL)
      {     

        do
        {
          $result = $mysqli->query("SELECT * FROM feed_$feedid WHERE time>$start AND time<$end ORDER BY time Asc LIMIT 100000");
          $rows = $result->num_rows;

          if ($rows>0)
          {
            $csv = array();
            while($row = $result->fetch_array())
            {
              $csv[] = $row['time'].",".$row['data'];
              $start = $row['time'];
            }
            $csv = implode($csv,"\n");
            $timestore->post_csv($feedid,$csv,null);

            $days = round(($now - $start) / (3600*24));

            echo "Start: ".$start." ".$days." days ".($result->num_rows)."\n";
          }
        } while ($rows!=0);
        
      } 
      elseif ($engine==Engine::PHPTIMESERIES)
      {
        $chunksize = 100000;
        $dpread = 0;
        
        $feedname = "/var/lib/phptimeseries/feed_$feedid.MYD";
        $fh = fopen($feedname, 'rb');
        $size = filesize($feedname);
        $left_to_read = $size;
        
        $csv = array();
        do
        {
          $d = fread($fh,9);
          $array = unpack("x/Itime/fvalue",$d);
          $csv[] = $array['time'].",".$array['value'];
          $dpread++;
          
          if ($dpread>$chunksize)
          {
            $csv = implode($csv,"\n");
            $timestore->post_csv($feedid,$csv,null);
            $csv = array();
            $dpread = 0;
          }
          
          $left_to_read -= 9;
          echo "Left to read: ".$left_to_read."\n";
        }
        while ($left_to_read>0);

        fclose($fh);
      }
      

      echo microtime(true)-$benchstart;

      $mysqli->query("UPDATE feeds SET `engine`='1' WHERE `id`='$feedid'");
      $mysqli->query("DELETE FROM converttotimestore WHERE `feedid` = '$feedid'");
    }
  }
