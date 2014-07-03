<?php

$schema['importqueue'] = array(
  'queid' => array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Extra'=>'auto_increment'),
  'userid' => array('type' => 'int(11)'),
  'remoteurl' => array('type' => 'text'),
  'remotekey' => array('type' => 'text'),
  'remotefeedid' => array('type' => 'int(11)'),
  'localfeedid' => array('type' => 'int(11)')
);

$schema['syncsettings'] = array(
  'userid' => array('type' => 'int(11)', 'Key'=>'PRI'),
  'remoteurl' => array('type' => 'text'),
  'remotekey' => array('type' => 'text')
);

?>
