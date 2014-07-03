<?php

  $domain = "messages";
  bindtextdomain($domain, "Modules/feed/locale");
  bind_textdomain_codeset($domain, 'UTF-8');

  $menu_dropdown[] = array('name'=> dgettext($domain, "Convert to timestore"), 'path'=>"converttotimestore" , 'session'=>"write", 'order' => 2 );

?>
