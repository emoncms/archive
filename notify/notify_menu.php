<?php

  $domain = "messages";
  bindtextdomain($domain, "Modules/user/locale");
  bind_textdomain_codeset($domain, 'UTF-8');

  $menu_dropdown[] = array('name'=> dgettext($domain, _("Notify")), 'path'=>"notify" , 'session'=>"write");

?>
