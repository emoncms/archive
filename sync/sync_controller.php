<?php
  /*
    
    The sync controller and view creates a page that:

    1) Allows you to select a remote emoncms account from which you want to sync from.
    2) Select the individual feeds in the remote account that you want to download.
    3) Provide update of the feeds position in the download queue

    Downloading and importing is handled by the import script.

    All Emoncms code is released under the GNU Affero General Public License.
    See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org

    SYNC CONTROLLER ACTIONS		ACCESS

    Script is a rough first draft - it needs cleaning up!!
  */

  // no direct access
  defined('EMONCMS_EXEC') or die('Restricted access');

  function sync_controller()
  {
    global $mysqli, $session, $route;

    include "Modules/feed/feed_model.php";
    $feed = new Feed($mysqli);

    include "Modules/sync/sync_model.php";
    $sync = new Sync($mysqli,$feed,$session['userid']);

    if ($route->format=='html')
    {
      if ($route->action=="list" && $session['write']) $result = view("Modules/sync/sync_view.php", array());
    }

    if ($route->format=='json')
    {
      // Register a feed to be downloaded
      if ($route->action=="feed" && $session['write']) $result = $sync->add_feed($session['userid'], get('feedid'),get('name'),get('datatype'));

      // Save remote url and apikey
      if ($route->action=="setsettings" && $session['write']) $result = $sync->set_settings($session['userid'],get('remoteurl'),get('remotekey'));

      // Save remote url and apikey
      if ($route->action=="getsettings" && $session['write']) $result = $sync->get_settings($session['userid']);

      // get the remote feed list, we want to load the remote feeds when the page is first loaded
      // but only queue progress updates there after
      if ($route->action=="getremotefeeds" && $session['write']) $result = $sync->get_remote_feeds($session['userid']);
      if ($route->action=="getimportqueue" && $session['write']) $result = $sync->get_importqueue($session['userid']);
      if ($route->action=="getlocalfeeds" && $session['write']) $result = $sync->get_local_feeds($session['userid']);
    }

    return array('content'=>$result);
  }

?>
