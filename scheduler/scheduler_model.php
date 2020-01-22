<?php
// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

class Scheduler
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function save_schedule($userid, $schedule)
    {
        $userid = (int) $userid;
        $schedule = preg_replace('/[^\w\s-.",:{}\[\]]/','',$schedule);

        $schedule = json_decode($schedule);

        // Dont save if json_Decode fails
        if ($schedule!=null) {

          $schedule = json_encode($schedule);
          $schedule = $this->mysqli->real_escape_string($schedule);

          $result = $this->mysqli->query("SELECT * FROM scheduler WHERE `userid` = '$userid'");
          $row = $result->fetch_object();

          if (!$row)
          {
              $this->mysqli->query("INSERT INTO scheduler (`userid`, `schedule`) VALUES ('$userid','$schedule')");
          }
          else
          {
              $this->mysqli->query("UPDATE scheduler SET `schedule` = '$schedule' WHERE `userid` = '$userid'");
          }
          return true;
        }
        else
        {
          return false;
        }
    }
    

    public function get_schedule($userid)
    {
        $userid = (int) $userid;
        $result = $this->mysqli->query("SELECT * FROM scheduler WHERE `userid` = '$userid'");
        $row = $result->fetch_array();
        if ($row && $row['schedule']!=null) return json_decode($row['schedule']); else return '0';
    }
}
?>
