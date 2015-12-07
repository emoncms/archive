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

<?php global $path; ?>
<script type="text/javascript" src="<?php echo $path; ?>Lib/flot/jquery.min.js"></script>

<h2>Node list</h2>

<div style="float:right;">
  <b>New </b><a id="addnode" href="#" ><i class="icon-plus-sign"></i></a>
</div>

<table class="catlist">
  <tr>
    <th>id</th>
    <th>Title</th>
    <th></th>
    <th></th>
    <th></th>
  </tr>
  <?php $i=0; foreach ($list as $item) { $i++; ?>
  <tr class="d<?php echo ($i & 1); ?>" >
    <td><?php echo $item['id']; ?></td>
    <td><?php echo $item['title']; ?></td>
    <td><a href="<?php echo $path; ?>node/view?id=<?php echo $item['id']; ?>"><i class='icon-eye-open'></i></a></td>
    <td><a href="<?php echo $path; ?>node/edit?id=<?php echo $item['id']; ?>"><i class='icon-edit'></i></a></td>
    <td><a class="deletenode" node="<?php echo $item['id']; ?>" href="#" ><i class='icon-trash'></i></a></td>
  </tr>
  <?php } ?>
</table>

<script type="application/javascript">
  var path =   "<?php echo $path; ?>";

  $("#addnode").click(function() {
    $.ajax({type:'POST',url:path+'node/create.json',data:'',dataType:'json',success:location.reload()});
  });

  $(".deletenode").click(function() {
    var node = $(this).attr("node");
    $.ajax({type:'GET',url:path+'node/delete.json',data:'id='+node,dataType:'json',success:location.reload()});
  });
</script>


