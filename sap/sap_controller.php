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

  function sap_controller()
  {
    global $mysqli, $session, $route;

    require "Modules/sap/sap_model.php";
    $sap = new Sap($mysqli);

    if ($route->action == 'save')
    {
      $result = false;
      $data = null;
      if (isset($_POST['data'])) $data = $_POST['data'];
      if (!isset($_POST['data']) && isset($_GET['data'])) $data = $_GET['data'];
      //echo $data;
      if ($data && $data!=null) {

        if ($session['write']) {
          $result = $sap->save($session['userid'],1,$data);
        } else {
          $result = true;
          $data = preg_replace('/[^\w\s-.",:{}\[\]]/','',$data);
          $_SESSION['sapdata'] = $data;
        }
 
      }
    }
    elseif ($route->action == 'get' && $session['write'])
    {
      $result = json_decode($sap->get($session['userid'], 1)); 
    } 
    else
    {
      if (!$route->action) $route->action = 1;

      $example = false;
      $datafromsession = false;

      if ($session['write']) {
        $data = $sap->get($session['userid'], 1); 
        if (!$data && isset($_SESSION['sapdata'])) {
          $data = $_SESSION['sapdata'];
          $sap->save($session['userid'],1,$data);
        }
      } else { 
        if (isset($_SESSION['sapdata'])) {
          $data = $_SESSION['sapdata'];
          $datafromsession = true;
        } else {
          $example = true;
          $data = file_get_contents('Modules/sap/example.data');
        }
      }

      $result = view("Modules/sap/sap_view.php",array('data'=>$data, 'page'=>$route->action, 'example'=>$example, 'datafromsession'=>$datafromsession));
    }
  
    return array('content'=>$result, 'fullwidth'=>true);
  }

?>
