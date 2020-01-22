<?php

  $schema['energy'] = array(
    'id' => array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Extra'=>'auto_increment'),
    'userid'=> array('type'=>'int(11)','Null'=>'NO'),
    'tag'=> array('type'=>'text'),
    'year'=> array('type'=>'int(11)'),
    'data'=> array('type'=>'text')
  );

?>
