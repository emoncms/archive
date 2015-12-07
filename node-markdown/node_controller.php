<?php
  /*
   All Emoncms code is released under the GNU Affero General Public License.
   See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org
  */
  defined('EMONCMS_EXEC') or die('Restricted access');
  
  function node_controller()
  {

    require "Modules/node/node_model.php";
    global $session, $route;

    $output['content'] = "";
    $output['message'] = "";

    if ($route['action'] == 'create' && $session['write'])
    {
      $nodeid = create_node($session['userid'],"(new)","");
      $output['message'] = "Node created";
    }

    if ($route['action'] == 'list' && $session['read'])
    {
      $list = get_node_list($session['userid']);
      $output['content'] = view('node/node_list.php', array('list'=>$list));
    }

    if ($route['action'] == 'edit' && $session['write'])
    {
      $nodeid = intval($_GET['id']);
      $title = get_node_title($nodeid);
      $content = get_node_content($nodeid);
      $output['content'] = view('node/node_edit.php', array('id'=>$nodeid,'title'=>$title,'content'=>$content));
    }

    if ($route['action'] == 'save' && $session['write'])
    {
      $nodeid = intval($_POST['id']);
      $title = $_POST['title'];
      $content = $_POST['content'];
      $content = db_real_escape_string($content);
      set_node_title($nodeid, $title);
      set_node_content($nodeid, $content);
      $output['message'] = "Node saved";
    }

    if ($route['action'] == 'view')
    {
      $nodeid = intval($_GET['id']);
      $title = get_node_title($nodeid);
      $content = get_node_content($nodeid);

      include_once "Modules/node/markdown/markdown.php";
      $content = Markdown($content);

      $output['content'] = view('node/node_view.php', array('title'=>$title,'content'=>$content));
    }

    if ($route['action'] == 'delete' && $session['write'])
    {
      $nodeid = intval($_GET['id']);
      delete_node($nodeid);
      $output['message'] = "Node deleted";
    }

    return $output;
  }

?>
