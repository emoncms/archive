<?php
// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

class Sap
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function save($userid, $docid, $data)
    {
        $userid = (int) $userid;
        $docid = (int) $docid;
        $data = preg_replace('/[^\w\s-.",:{}\[\]]/','',$data);

        $data = json_decode($data);

        // Dont save if json_Decode fails
        if ($data!=null) {

          $data = json_encode($data);
          $data = $this->mysqli->real_escape_string($data);

          $result = $this->mysqli->query("SELECT `docid` FROM sap WHERE `userid` = '$userid' AND `docid` = '$docid'");
          $row = $result->fetch_object();

          if (!$row)
          {
              $this->mysqli->query("INSERT INTO sap (`userid`, `docid`, `data`) VALUES ('$userid','$docid','$data')");
          }
          else
          {
              $this->mysqli->query("UPDATE sap SET `data` = '$data' WHERE `userid` = '$userid' AND `docid` = '$docid'");
          }
          return true;
        }
        else
        {
          return false;
        }
    }

    public function get($userid,$docid)
    {
        $userid = (int) $userid;
        $docid = (int) $docid;
        $result = $this->mysqli->query("SELECT `data` FROM sap WHERE `userid` = '$userid' AND `docid` = '$docid'");
        $row = $result->fetch_array();
        if ($row) return $row['data']; else return '0';
    }
}
?>
