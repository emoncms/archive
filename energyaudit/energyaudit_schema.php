<?php

  $schema['energyaudit'] = array(
    'userid'=> array('type'=>'int(11)','Null'=>'NO'),
    'key'=> array('type'=>'text'),
    'value'=> array('type'=>'text')
  );

  $schema['energydata'] = array(
    'userid'=> array('type'=>'int(11)','Null'=>'NO'),
    'data'=> array('type'=>'text','Null'=>'NO')
  );

?>
