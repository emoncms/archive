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

function converttotimestore_controller()
{
  global $mysqli, $session, $route, $timestore_adminkey;
  $result = false;
  
  include "Modules/converttotimestore/converttotimestore_model.php";
  $convert = new ConvertToTimestore($mysqli);
    
  $result = false;
  
  if ($route->format == 'html' && $session['write'])
  {
      if ($session['write']) $result = view("Modules/converttotimestore/converttotimestore_view.php",array());
 

  }
  
  if ($route->format == 'json')
  {
    if ($route->action == "get" && $session['write'])
    { 
      $userid = $session['userid'];

      $feedconvertstats = array();
      $result = $mysqli->query("SELECT * FROM converttotimestore WHERE `userid` = '$userid';");
      while ($row = $result->fetch_object())
      {
        $feedid = $row->feedid;
        $feedresult = $mysqli->query("SELECT name FROM feeds WHERE `id` = '$feedid';");
        $feedrow = $feedresult->fetch_object();

        
        $row->id = $feedid;
        $row->name = $feedrow->name;
        $feedconvertstats[] = $row;
      }
      $result = $feedconvertstats;
    }
    
    if ($route->action == "set" && $session['write'])
    { 
      $userid = $session['userid'];
      $feedid = (int) get('feedid');
      $convert = (int) get('convert');
      
      $mysqli->query("UPDATE converttotimestore SET `convertto` = '$convert' WHERE `feedid` = '$feedid' AND `userid` = '$userid';");
      
      $result = array('userid'=>$userid, 'feedid'=>$feedid, 'convertto'=>$convert);
    }
    
    
    if ($route->action == "scan" && $session['write'])
    { 
      $userid = $session['userid'];
      $result = $convert->scan($userid);
      $result = $convert->scan_phptimeseries($userid);
    }
  }
  
  return array('content'=>$result);
}



