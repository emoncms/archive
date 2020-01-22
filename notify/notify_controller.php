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

function notify_controller()
{
  global $route, $session, $mysqli;
  $result = false;
  
  include "Modules/notify/notify_model.php";
  $notify = new Notify($mysqli);


  if ($route->format == 'html')
  {
    if ($session['write']) $result = view("Modules/notify/notify_view.php",array());
  }
  
  
  if ($route->format == 'json')
  {
    if ($session['write'] && $route->action=='status') $result = $notify->status($session['userid']);
    if ($session['write'] && $route->action=='enable') $result = $notify->enable($session['userid'],get('email'));
    if ($session['write'] && $route->action=='disable') $result = $notify->disable($session['userid']);
  }
  
  return array('content'=>$result);
}
