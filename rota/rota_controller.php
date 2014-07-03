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

  function rota_controller()
  {
    global $mysqli, $session, $route;

    include "Modules/rota/rota_model.php";
    $rota = new Rota($mysqli);

    if ($route->format=='html')
    {
      if ($route->action=="view" && $session['write']) $result = view("Modules/rota/rota_view.php", array());
    }

    if ($route->format=='json')
    {
      if ($route->action=="parsecsv" && $session['write']) $result = $rota->parse_csv(post('csv'));
      if ($route->action=="getrotafeed" && $session['write']) $result = $rota->get_rota_feed();
      if ($route->action=="unixtime" && $session['write']) $result = time();
    }

    return array('content'=>$result);
  }

?>
