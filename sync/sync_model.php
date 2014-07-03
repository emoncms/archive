<?php

class Sync
{

  private $remoteurl = "";
  private $remotekey = "";

  private $mysqli;
  private $feed;

  public function __construct($mysqli,$feed,$userid)
  {
    $this->mysqli = $mysqli;
    $this->feed = $feed;
    $this->load_settings($userid);
  }

  public function set_settings($userid,$remoteurl,$remotekey)
  {
    $remoteurl = urldecode($remoteurl);
    $remotekey = $this->mysqli->real_escape_string(preg_replace('/[^.\/A-Za-z0-9]/','',$remotekey));

    $result = $this->mysqli->query("SELECT * FROM syncsettings WHERE `userid` = '$userid'");
    $row = $result->fetch_array();

    $success = false;

    if ($row) {
      $result = $this->mysqli->query("UPDATE syncsettings SET `remoteurl`='$remoteurl', `remotekey`='$remotekey' WHERE `userid`='$userid'");
      $success = true;
    } else {
      $result = $this->mysqli->query("INSERT INTO syncsettings (userid,remoteurl,remotekey) VALUES ('$userid','$remoteurl','$remotekey')");
      $success = true;
    }

    if ($success) {
      $this->remoteurl = $remoteurl;
      $this->remotekey = $remotekey;
    }

    return array('success'=>$success);
  }

  public function load_settings($userid)
  {
    $result = $this->mysqli->query("SELECT * FROM syncsettings WHERE `userid` = '$userid'");
    $row = $result->fetch_array();
    if ($row) {
      $this->remoteurl = $row['remoteurl'];
      $this->remotekey = $row['remotekey'];
    } else {
      $this->remoteurl = "";
      $this->remotekey = "";
    }
  }

  public function get_settings($userid)
  {
    $result = $this->mysqli->query("SELECT remoteurl,remotekey FROM syncsettings WHERE `userid` = '$userid'");
    $row = $result->fetch_array();
    if (!$row) return array('remoteurl'=>"", 'remotekey'=>"");
    return $row;
  }

  public function get_remote_feeds($userid)
  {
    if ($this->remoteurl && $this->remotekey) {
      $fh = @fopen($this->remoteurl."/feed/list.json?apikey=".$this->remotekey, 'r' );
      $data = ""; while (($buffer = fgets($fh)) !== false) {$data .= $buffer;}
      fclose($fh);
      return json_decode($data);
    } else {
      return false;
    }

  }

  public function get_importqueue($userid)
  {
    $result = $this->mysqli->query("SELECT queid FROM importqueue ORDER BY queid Asc");
    $row = $result->fetch_array();
    if ($row) $startqueid = $row['queid']; else $startqueid = 0;

    $importqueue = array();
    $result = $this->mysqli->query("SELECT * FROM importqueue WHERE `userid`='$userid' ORDER BY queid Asc");
    while($row = $result->fetch_object()) {
      $row->queid = ($row->queid - $startqueid) + 1;
      $importqueue[$row->remotefeedid] = $row;
    }
    return $importqueue;
  }

  public function add_feed($userid,$feedid,$name,$datatype)
  {
    $feedid = intval($feedid);
    $name = preg_replace('/[^\w\s-.]/','',$name);

    $localfeedid = $this->feed->get_id($userid,$name);
    
    if (!$localfeedid) {
      $result = $this->feed->create($userid,$name,$datatype);
      $localfeedid = $result['feedid'];
    }
    // Make sure feed is not already in que
    $remoteurl = $this->remoteurl;
    $remotekey = $this->remotekey;

    $result = $this->mysqli->query("SELECT * FROM importqueue WHERE remoteurl='$remoteurl' AND remotekey='$remotekey' AND remotefeedid='$feedid' AND localfeedid='$localfeedid'");

    if (!$result->num_rows)
    {
      $this->mysqli->query("INSERT INTO importqueue (`userid`,`remoteurl`,`remotekey`,`remotefeedid`,`localfeedid`) VALUES ('$userid','$remoteurl','$remotekey','$feedid','$localfeedid')");
      return true;
    } 
    else
    {
      return false;
    }
  }

  // Get a list of local feeds but need to get time last updated from data table rather than feeds table
  // as the feeds table is not updated and may not be updated if importing was stopped mid process

  public function get_local_feeds($userid)
  {
    $userid = intval($userid);

    $result = $this->mysqli->query("SELECT id,name,datatype FROM feeds WHERE userid = $userid");
    if (!$result) return 0;

    $feeds = array();
    while ($row = $result->fetch_object()) 
    { 
      
      $feedtable = "feed_".trim($row->id)."";
      $timeresult = $this->mysqli->query("SELECT * FROM $feedtable ORDER BY time Desc LIMIT 1");
      $timerow = $timeresult->fetch_object();

      if ($timerow) {
        $row->time = $timerow->time * 1000; 
      } else {
        $row->time = null;
      }

      $feeds[] = $row; 
    }
    return $feeds;
  }
}
