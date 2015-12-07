<?php
  /*
   All Emoncms code is released under the GNU Affero General Public License.
   See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org
  */

  function create_node($userid,$title, $content)
  {
    db_query("INSERT INTO node ( userid, title, content ) VALUES ( '$userid', '$title', '$content' );");
    $nodeid = db_insert_id();
    return $nodeid;
  }

  function get_node_list($userid)
  {
    $result = db_query("SELECT id,title FROM node WHERE userid = '$userid'");
    $list = array();
    while ($row = db_fetch_array($result)) { $list[] = $row; }
    return $list;
  }

  function set_node_title($nodeid,$title)
  {
    db_query("UPDATE node SET title = '$title' WHERE id='$nodeid'");
  }

  function set_node_content($nodeid,$content)
  {
    db_query("UPDATE `node` SET `content` = '$content' WHERE `id`='$nodeid'");
  }

  function get_node_title($nodeid)
  {
    $result = db_query("SELECT title FROM node WHERE id='$nodeid'");
    $row = db_fetch_array($result);
    return $row['title'];
  }

  function get_node_content($nodeid)
  {
    $result = db_query("SELECT content FROM node WHERE id='$nodeid'");
    $row = db_fetch_array($result);
    return $row['content'];
  }

  function delete_node($nodeid)
  {
    db_query("DELETE FROM node WHERE id = '$nodeid'");
  }

?>
