<?php

$schema['notify'] = array(
    'userid' => array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'PRI'),
    'email' => array('type' => 'varchar(30)'),
    'enabled' => array('type' => 'int(11)', 'Null'=>'NO', 'default'=>0)
);

