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

  function energy_controller()
  {
    global $mysqli, $session, $route;

    include "Modules/energy/energy_model.php";
    $energy = new Energy($mysqli);

    include "Modules/energy/energytypes.php";

    $result = false;

    if ($route->action == "list" && $session['read'])
    {
      $energyitems = $energy->get_year($session['userid'], 2013);
      $result = view("Modules/energy/list_view.php", array('energyitems' => $energyitems, 'energytypes'=>$energytypes));
    }

    if ($route->action == "items" && $session['read'])
    {
      $energyitems = $energy->get_year($session['userid'], 2013);
      $result = json_encode($energyitems);
    }

    if ($route->action == "types" && $session['read'])
    {
      $result = json_encode($energytypes);
    }

    if ($route->action == "item" && $route->subaction == "add" && $session['write'])
    {
      $tag = preg_replace('/[^\w\s-]/','',get('tag'));
      $data = preg_replace('/[^\w\s-.",:{}\[\]]/','',get('data'));
      
      $energyitems = $energy->add_item($session['userid'], $tag, 2013, $data);
    }

    if ($route->action == "item" && $route->subaction == "remove" && $session['write'])
    {
      $tag = preg_replace('/[^\w\s-]/','',get('tag'));
      $energyitems = $energy->item_remove($session['userid'], $tag, 2013);
    }

    if ($route->action == "save" && $session['write'])
    {
      $data = preg_replace('/[^\w\s-.",:{}\[\]]/','',get('data'));
      $data = json_decode($data);
      
      foreach ($data as $item)
      {
        $energy->item_set($session['userid'], $item->tag, $item->year, $item->data);
      }

      $result = "saved";
    }

    return array('content'=>$result);
  }
