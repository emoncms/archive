<?php
/*

All Emoncms code is released under the GNU Affero General Public License.
See COPYRIGHT.txt and LICENSE.txt.

---------------------------------------------------------------------
Emoncms - open source energy visualisation
Part of the OpenEnergyMonitor project:
http://openenergymonitor.org

*/
    
error_reporting(E_ALL);
ini_set('display_errors', 'on');
        
require("core.php");
$path = get_application_path();

require "emoncms-api.php";
$emoncmsorg = new Emoncms();
$emoncmsorg->host = "http://emoncms.org";

require("user_model.php");
$user = new User($emoncmsorg);

session_start();
$session = $user->status();

$q = "";
if (isset($_GET['q'])) $q = $_GET['q'];
$q = rtrim($q,"/");

$format = "html";
$content = "Sorry page not found";

switch ($q)
{   
    case "":
        $format = "html";
        if (!$session) {
            $content = view("view/login.php",array());
        } else {
            $content = view("view/home.php",array());
        }
        break;
        
    case "intro":
        if (!$session) break;
        $format = "html";
        $content = view("view/energy.php",array('session'=>$session));
        break;
        
    case "app/myelectric":
        if (!$session) break;
        $format = "html";
        $content = view("view/myelectric.php",array('session'=>$session));
        break;
        
    case "app/myheatpump":
        if (!$session) break;
        $format = "html";
        $content = view("view/myheatpump.php",array('session'=>$session));
        break;

    case "app/myheatpump2":
        if (!$session) break;
        $format = "html";
        $content = view("view/myheatpump2.php",array('session'=>$session));
        break;
        
    case "app/myopenevse":
        if (!$session) break;
        $format = "html";
        $content = view("view/myopenevse.php",array('session'=>$session));
        break;
             
    case "status":
        if (!$session) break;
        $format = "json";
        $content = $session;
        break;

    case "register":
        $format = "json";
        $content = $user->register(get('username'),get('email'),get('password'));
        break;
                
    case "login":
        $format = "json";
        $content = $user->login(get('username'),get('password'));
        break;
        
    case "logout":
        if (!$session) break;
        $format = "text";
        $content = $user->logout();
        break;

    // -----------------------------------------------------------------------------------------------------------------------
    // Auto Configuration
    // ----------------------------------------------------------------------------------------------------------------------
    case "feed/list.json":
        if (!$session) break;
        $format = "json";
        $content = json_decode(file_get_contents($emoncmsorg->host."/feed/list.json?apikey=".$session['apikey_write']));
        break;
        
    case "feed/getmeta.json":
        if (!$session) break;
        $format = "json";
        $content = json_decode(file_get_contents($emoncmsorg->host."/feed/getmeta.json?id=".get('id')."&apikey=".$session['apikey_write']));
        break;
    
    // http://localhost/examples/user_remote_device/feed/data.json?apikey=ccb2869988769ac35756969db067ae5c&id=119331&start=1467522455000&end=1467533263000&interval=7&skipmissing=0&limitinterval=0
    // http://localhost/examples/user_remote_device/feed/data.json?apikey=f58c589fb180d94e5247d4552bfc6cee&id=119338&start=1464770784000&end=1467535584000&mode=daily
    case "feed/data.json":
        if (!$session) break;
        $format = "json";
        $id = get('id');
        $start = get('start');
        $end = get('end');
        
        if (isset($_GET['mode'])) {
            $mode = get('mode');
            $content = json_decode(file_get_contents($emoncmsorg->host."/feed/data.json?id=$id&start=$start&end=$end&mode=$mode&apikey=".$session['apikey_write']));
        } else {
            $interval = get('interval');
            $skipmissing = get('skipmissing');
            $limitinterval = get('limitinterval');
            $content = json_decode(file_get_contents($emoncmsorg->host."/feed/data.json?id=$id&start=$start&end=$end&interval=$interval&skipmissing=$skipmissing&limitinterval=$limitinterval&apikey=".$session['apikey_write']));
        }
        break;
        
    case "household/data":
        if (!$session) break;
        $format = "json";
        $id = get('id');
        include "household_process.php";
        $content = get_household_data($session['apikey_read'],$id);
        break;
    
    // -----------------------------------------------------------------------------------------------------------------------
    // Auto Configuration
    // -----------------------------------------------------------------------------------------------------------------------
    case "autoconfig":
        if (!$session) break;
        $format = "html";
        $content = view("view/autoconfig.php",array('session'=>$session));
        break;
        
    case "autoconfig/devicelist":
        if (!$session) break;
        $format = "json";
        $content = json_decode(file_get_contents($emoncmsorg->host."/autoconfig/devicelist?apikey=".$session['apikey_write']));
        break;
        
    case "autoconfig/configure":
        if (!$session) break;
        $format = "json";
        $content = json_decode(file_get_contents($emoncmsorg->host."/autoconfig/configure?device=".get('device')."&configuration=".get('configuration')."&apikey=".$session['apikey_write']));
        break;
}

if ($content=="Sorry page not found" && !$session) {
    $format = "html";
    $content = view("view/login.php",array());
}

switch ($format) 
{
    case "html":
        header('Content-Type: text/html');
        print view("theme/theme.php", array("content"=>$content));
        break;
    case "text":
        header('Content-Type: text/plain');
        print $content;
        break;
    case "json":
        header('Content-Type: application/json');
        print json_encode($content);
        break;
}
