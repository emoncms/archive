<?php

function command_insert($userid, $cmd)
{
  $time = time();
  db_query("INSERT INTO command (`userid`,`time`,`cmd`) VALUES ('$userid','$time','$cmd')");
}

function command_list($userid)
{
  $result = db_query("SELECT time,cmd FROM command WHERE `userid`='$userid'");

  $cmds = array();
  while ($row = db_fetch_array($result))
  {
    $cmds[] = array('time'=>$row['time'], 'cmd'=>$row['cmd']);
  }

  return $cmds;
}

function command_get($userid)
{
  $result = db_query("SELECT time,cmd FROM command ORDER BY time Asc LIMIT 1");

  $row = db_fetch_array($result);
  $time = $row['time'];
  $cmd = $row['cmd'];
 
  db_query("DELETE FROM command WHERE `time` = '$time' AND `cmd` = '$cmd'");

  return $cmd;
}

?>
