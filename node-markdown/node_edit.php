<?php
  /*
   All Emoncms code is released under the GNU Affero General Public License.
   See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org
  */
?>

<h2>Node edit: <?php echo $id; ?></h2><br>
<form action="save" method="POST" >
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<p><b>Title:</b></p>
<input type="text" style="width:100%" name="title" value="<?php echo $title; ?>" />
<br>
<p><b>Content:</b></p>
<textarea id='editarea' style="width:100%" name="content" rows='10' cols='50'><?php echo $content; ?></textarea>
<br><input type="submit" value="Save" />
</form>
