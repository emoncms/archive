<?php

// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

class Energy
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function item_set($userid, $tag, $year, $data)
    {
      $data = json_encode($data);

      $result = $this->mysqli->query("SELECT `id` FROM energy WHERE `userid` = '$userid' AND `tag` = '$tag' AND `year` = '$year' ");
      $row = $result->fetch_array();

      if (!$row)
      {
        //echo $tag." does not exist\n";
        $this->mysqli->query("INSERT INTO energy (`userid`, `tag`, `year`, `data`) VALUES ('$userid','$tag','$year','$data')");
      }
      else
      {
        $id = $row['id'];
        $this->mysqli->query("UPDATE energy SET `data` = '$data' WHERE `id` = '$id'");
      }
    }

    public function item_remove($userid, $tag, $year)
    {
      $result = $this->mysqli->query("DELETE FROM energy WHERE `userid` = '$userid' AND `tag` = '$tag' AND `year` = '$year' ");
    }

    public function get_year($userid, $year)
    {
      $entries = array();
      $result = $this->mysqli->query("SELECT `id`, `tag`, `year`, `data` FROM energy WHERE `userid` = '$userid' AND `year` = '$year' ");
      while ($row = $result->fetch_object()) 
      {
        $entries[] = $row;
      }
      return $entries;
    }
}

?>
