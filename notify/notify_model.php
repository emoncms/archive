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

class Notify
{

    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }


    public function status($userid)
    {
      $userid = (int) $userid;
      $result = $this->mysqli->query("SELECT enabled,email FROM notify WHERE userid = '$userid';");
      
      if ($result->num_rows) {
        return $result->fetch_object();
      } else {
        $this->mysqli->query("INSERT INTO notify (`userid`,`enabled`,`email`) VALUES ('$userid','0','');");
        return array('enabled'=>0, 'email'=>'');
      }

    }
    
    public function enable($userid,$email)
    {
      $userid = (int) $userid;
      
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return array('success'=>false, 'message'=>_("Email address format error"));

      $result = $this->mysqli->query("UPDATE notify SET `enabled` = '1', `email` = '$email' WHERE `userid` = '$userid'");
      
      return true;
    }
    
    public function disable($userid)
    {
      $userid = (int) $userid;
      $result = $this->mysqli->query("UPDATE notify SET `enabled` = '0' WHERE `userid` = '$userid'");
      
      return true;
    }
}
