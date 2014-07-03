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

function feedconvert_controller()
{
    global $mysqli, $session, $route;
    $result = false;

    // include "Modules/feed/feed_model.php";
    // $feed = new Feed($mysqli);

    include "Modules/feedconvert/feedconvert_model.php";
    $feedconvert = new FeedConvert($mysqli);

    if ($route->action == "convert" && $session['write']) $result = $feedconvert->convert_power_to_histogram(get('power'),get('histogram'),get('bucket'),time()-(3600*24*365),time());

    return array('content'=>$result);
}
