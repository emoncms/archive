<?php

 // no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

class ConvertToTimestore
{
  private $mysqli;
  
  public function __construct($mysqli)
  {
    $this->mysqli = $mysqli;
  }
        
  public function scan($userid)
  {       
    $feedlist = $this->mysqli->query("SELECT id,userid FROM feeds WHERE `engine` = '0' AND `datatype` = '1' AND `userid` = '$userid'");
    
    echo "\nfeed\t1st\t\t2nd\t\taverage\n";
    echo "------------------------------------------------\n";
    
    while($feed = $feedlist->fetch_object())
    {
      $feedid = $feed->id;
      $userid = $feed->userid;
      
      $result = $this->mysqli->query("SHOW TABLE STATUS LIKE 'feed_$feedid'");
      $row = $result->fetch_array();

      $size = ($row['Data_length'] + $row['Index_length']);
      $datapoints = ($row['Data_length'] / 9);
      
      if ($datapoints>0)
      {
      
        $currenttime = time();
        $start_limit = $currenttime - 157680000;
        $end_limit = $currenttime + 172800;
      
        $total_result = $this->mysqli->query("SELECT * FROM feed_$feedid WHERE time>'$start_limit' ORDER BY time Asc LIMIT 1");
        $total_row = $total_result->fetch_array();
        $total_start = $total_row['time'];

        $total_result = $this->mysqli->query("SELECT * FROM feed_$feedid WHERE time<'$end_limit' ORDER BY time Desc LIMIT 1");
        $total_row = $total_result->fetch_array();
        $total_end = $total_row['time'];

        $totaltime = $total_end-$total_start;
        $totalinterval = round($totaltime / $datapoints);
          
        
        $feeddata = $this->mysqli->query("SELECT * FROM feed_$feedid ORDER BY `time` DESC LIMIT 2000");
        $time = 0; $ndp = 0;
        $intervals = array();
        while($dp = $feeddata->fetch_object())
        {
          $last = $time;
          $time = $dp->time;
          if ($last>0)
          {
            $diff = ($last - $time);
            if (!isset($intervals[$diff])) $intervals[$diff] = 0;
            $intervals[$diff] ++;
          }
          else $end = $time;
          $ndp ++;
        }
        $start = $time;
        
        $average = round(($end - $start) / $ndp);
        
        arsort($intervals);
        
        $intA = key($intervals);
        $prcA = round(100*$intervals[$intA]/2000);
        next($intervals);
        $intB = key($intervals);
        $prcB = round(100*$intervals[$intB]/2000);
        
        echo "$feedid\t$intA"."s ($prcA%)\t$intB"."s ($prcB%)\t$average\t | $totalinterval\t$size\n";
        
        $convertto = 0;
        $result_convertto = $this->mysqli->query("SELECT convertto FROM converttotimestore WHERE `feedid` = '$feedid'");
        $row_convertto = $result_convertto->fetch_array();

        if ($row_convertto)
        {
          $convertto = $row_convertto['convertto'];
          echo "Row exists convertto set to: $convertto\n";
          $this->mysqli->query("DELETE FROM converttotimestore WHERE `feedid` = '$feedid'");
        }
          
        $this->mysqli->query("INSERT INTO converttotimestore (`feedid`,`userid`,`intA`,`prcA`,`intB`,`prcB`,`average`,`totalaverage`,`convertto`) VALUES ('$feedid','$userid','$intA','$prcA','$intB','$prcB','$average','$totalinterval','$convertto');");
        
      }
    }
    return "success";
  }
  
  public function scan_phptimeseries($userid)
  {       
    $feedlist = $this->mysqli->query("SELECT id,userid FROM feeds WHERE `engine` = '2' AND `datatype` = '1' AND `userid` = '$userid'");
    
    echo "\nfeed\t1st\t\t2nd\t\taverage\n";
    echo "------------------------------------------------\n";
    
    while($feed = $feedlist->fetch_object())
    {
      $feedid = $feed->id;
      $userid = $feed->userid;
      
      $feedname = "/var/lib/phptimeseries/feed_$feedid.MYD";
      $size = filesize($feedname);
      $datapoints = (int) ($size / 9);

      if ($datapoints>0)
      {
      
        $fh = fopen($feedname, 'rb');
          
        $currenttime = time();
        $start_limit = $currenttime - 157680000;
        $end_limit = $currenttime + 172800;
      
        $pos = $this->binarysearch($fh,$start_limit,$size);
        fseek($fh,$pos); $d = fread($fh,9);
        $array = unpack("x/Itime/fvalue",$d);
        $total_start = $array['time'];

        $pos = $this->binarysearch($fh,$end_limit,$size);
        fseek($fh,$pos); $d = fread($fh,9);
        $array = unpack("x/Itime/fvalue",$d);
        $total_end = $array['time'];

        $totaltime = $total_end-$total_start;
        $totalinterval = round($totaltime / $datapoints);
        
        if ($size<2000*9) {
          $pos = 0;
          $snapshot_end = $datapoints;
        } else {
          $pos = $size - (2000*9);
          $snapshot_end = 2000;
        }
          
        fseek($fh,$pos);
        $time = 0; $ndp = 0;
        $intervals = array();
        for ($i=0; $i<$snapshot_end; $i++)
        {
          $d = fread($fh,9);
          $array = unpack("x/Itime/fvalue",$d);

          $last = $time;
          $time = $array['time'];
          if ($last>0)
          {
            $diff = ($time-$last);
            if (!isset($intervals[$diff])) $intervals[$diff] = 0;
            $intervals[$diff] ++;
          }
          else $end = $time;
          $ndp ++;
        }
        $start = $time;
        
        $average = -1*round(($end - $start) / $ndp);
        
        arsort($intervals);
        
        $intA = key($intervals);
        $prcA = round(100*$intervals[$intA]/2000);
        next($intervals);
        $intB = key($intervals);
        $prcB = round(100*$intervals[$intB]/2000);
        
        echo "$feedid\t$intA"."s ($prcA%)\t$intB"."s ($prcB%)\t$average\t | $totalinterval\t$size\n";
        
        $convertto = 0;
        $result_convertto = $this->mysqli->query("SELECT convertto FROM converttotimestore WHERE `feedid` = '$feedid'");
        $row_convertto = $result_convertto->fetch_array();

        if ($row_convertto)
        {
          $convertto = $row_convertto['convertto'];
          echo "Row exists convertto set to: $convertto\n";
          $this->mysqli->query("DELETE FROM converttotimestore WHERE `feedid` = '$feedid'");
        }
          
        $this->mysqli->query("INSERT INTO converttotimestore (`feedid`,`userid`,`intA`,`prcA`,`intB`,`prcB`,`average`,`totalaverage`,`convertto`) VALUES ('$feedid','$userid','$intA','$prcA','$intB','$prcB','$average','$totalinterval','$convertto');");
        
      }
    }
    return "success";
  }  
  
  private function binarysearch($fh,$time,$filesize)
  {
    // Binary search works by finding the file midpoint and then asking if
    // the datapoint we want is in the first half or the second half
    // it then finds the mid point of the half it was in and asks which half
    // of this new range its in, until it narrows down on the value.
    // This approach usuall finds the datapoint you want in around 20
    // itterations compared to the brute force method which may need to
    // go through the whole file that may be millions of lines to find a
    // datapoint.
    $start = 0; $end = $filesize-9;

    // 30 here is our max number of itterations
    // the position should usually be found within
    // 20 itterations.
    for ($i=0; $i<30; $i++)
    {
      // Get the value in the middle of our range
      $mid = $start + round(($end-$start)/18)*9;
      fseek($fh,$mid);
      $d = fread($fh,9);
      $array = unpack("x/Itime/fvalue",$d);

      // echo "S:$start E:$end M:$mid $time ".$array['time']." ".($time-$array['time'])."\n";

      // If it is the value we want then exit
      if ($time==$array['time']) return $mid;

      // If the query range is as small as it can be 1 datapoint wide: exit
      if (($end-$start)==9) return ($mid-9);

      // If the time of the last middle of the range is
      // more than our query time then next itteration is lower half
      // less than our query time then nest ittereation is higher half
      if ($time>$array['time']) $start = $mid; else $end = $mid;
    }
  }
  
}
