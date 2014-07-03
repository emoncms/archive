<?php
  /*
    All Emoncms code is released under the GNU Affero General Public License.
    See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org

    list.js - is a javascript dynamic user interface list creator.

  */

  global $path;
?>

<script type="text/javascript" src="<?php print $path; ?>Lib/flot/jquery.min.js"></script>

<div style="float:right;"><a href="<?php echo $path; ?>command/api">Command API Help</a></div>
<h2>Command Module</h2>
<p>The command module is a basic way of serving commands to a connected basestation which can then relay the commands to wireless listening nodes. Which could be part of a system to turn on and off lighting, heating or other things. This is a module in an early stage of development.</p>
<p>Start by adding a command to the command queue below, then fetch the command with your basestation by pointing it to the URL: emoncms.org/command/get.json</p>

<p>Send command: <input type="text" id="cmdtext" /> <button id="newcmd" class="button05" href="#" >Add</button></p>

<p>Commands waiting in queue for request from basestation:</p>
<table class='catlist' style="width:380px;">
<tr><th>Time</th><th>Command</th></tr>
<?php foreach ($cmds as $cmd) { ?>
<tr><td><?php echo date("Y-n-j H:i:s", $cmd['time']); ?></td><td><?php echo $cmd['cmd']; ?></td></tr>
<?php } ?>
</table>

<script>
var path = "<?php echo $path; ?>";

$('#newcmd').click(function()
{
  var cmd = $('#cmdtext').val();
  
  $.ajax({                                      
    type: "GET",
    url: path+"command/insert.json?cmd="+cmd,
    dataType: 'json',
    async: false,
    success: function(data){}
  });

  location.reload();
});

</script>
