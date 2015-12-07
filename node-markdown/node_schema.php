<?php

$schema['node'] = array(
  'id' => array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Extra'=>'auto_increment'),
  'userid' => array('type' => 'int(11)'),
  'title' => array('type' => 'text'),
  'content' => array('type' => 'text')
);

?>
