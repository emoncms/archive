<?php
  /*
   All Emoncms code is released under the GNU Affero General Public License.
   See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org
  */

  // no direct access
  defined('EMONCMS_EXEC') or die('Restricted access');

  function scheduler_controller()
  {
    global $mysqli,$session, $route;

    $result = false;
    
    include "Modules/scheduler/scheduler_model.php";
    $scheduler = new Scheduler($mysqli);

    if ($route->format == 'html')
    {
      if ($route->action == "" && $session['write']) $result = view("Modules/scheduler/scheduler_view.php",array());
    }
    
    if ($route->format == 'json')
    {
      if ($route->action == "set" && $session['write']) $result = $scheduler->save_schedule($session['userid'],post('schedule'));
      if ($route->action == "get" && $session['write']) $result = $scheduler->get_schedule($session['userid']);
    }

    return array('content'=>$result);
  }

?>
