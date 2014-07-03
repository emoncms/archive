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

  function command_controller()
  {
    require "Modules/command/command_model.php";
    global $session, $route;

    $format = $route['format'];
    $action = $route['action'];

    $output['content'] = "";
    $output['message'] = "";

    if ($action == 'api' && $session['read'])
    {
      $output['content'] = view("command/command_api.php", array());
    }

    elseif ($action == 'get' && $session['write'])
    {
      $output['content'] = command_get($session['userid']);
    }

    elseif ($action == 'insert' && $session['write'])
    {
      $cmd = preg_replace('/[^\w\s-]/','',get('cmd'));
      command_insert($session['userid'], $cmd);
    }
 
    elseif ($action == '' && $session['write'])
    {
      $cmds = command_list($session['userid']);
      $output['content'] = view("command/command_view.php", array('cmds' => $cmds));
    }
 
    return $output;
  }

?>
