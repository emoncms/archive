<?php 
  global $path; 
?>

<script type="text/javascript" src="<?php echo $path; ?>Modules/sync/sync.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Lib/tablejs/table.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Lib/tablejs/custom-table-fields.js"></script>
<style>
input[type="text"] {
     width: 88%; 
}
</style>

<br><div style="float:right;"><a href="api">Sync API</a></div>

<div class="container">
    <h2><?php echo _('Sync'); ?></h2>
    <p>Imports feeds from a remote server to a local emoncms installation</p>

    <p><b>Remote server details:</b></p>
    <div class="input-prepend input-append">
      <span class="add-on">Remote URL</span>
      <input id="remoteurl" type="text" placeholder="Ex: http://emoncms.org" style="width:250px">

      <span class="add-on">Remote write apikey:</span>
      <input id="remotekey" type="text" placeholder="" style="width:250px">
      <button id="savesettings" class="btn btn-info">Save</button>
      <span id="saved" class="add-on" style="display:none">Saved</span>
    </div>
    <br>

    <p>The following table lists all the remote server feeds, how far behind the local feeds are and the position of feeds being imported in the import queue. To start the import process run the import script directly via command line: <b>$ php import.php</b></p> 

    <div id="table"></div>

    <div id="nofeeds" class="alert alert-block hide">
        <p>Enter remote server details above to display a list of remote server feeds.</p>
    </div>
</div>

<script>

  var path = "<?php echo $path; ?>";

  var settings = sync.getsettings();
  $("#remoteurl").val(settings.remoteurl);
  $("#remotekey").val(settings.remotekey);

  // Extemd table library field types
  for (z in customtablefields) table.fieldtypes[z] = customtablefields[z];

  table.element = "#table";

  table.fields = {
    'id':{'title':"<?php echo _('Id'); ?>", 'type':"fixed"},
    'name':{'title':"<?php echo _('Name'); ?>", 'type':"fixed"},
    'datatype':{'title':"<?php echo _('Datatype'); ?>", 'type':"select", 'options':['','REALTIME','DAILY','HISTOGRAM']},
    'behindby':{'title':"<?php echo _('Behind by'); ?>", 'type':"fixed"},
    'status':{'title':"<?php echo _('Status'); ?>", 'type':"fixed"},
    // Actions
    'sync-action':{'title':'Add', 'type':"iconbasic", 'icon':"icon-plus"}

  }

  table.deletedata = false;

  // 1) Fetch remote feed list from remote server  
  table.data = sync.get_remote_feeds();
  if (table.data) {
    var updater = setInterval(update, 5000);
    $("#nofeeds").hide(); 
  } else {
    $("#nofeeds").show();
  }

  update();

  function update()
  {
    var feeds = sync.get_local_feeds();
    var importqueue = sync.get_importqueue();

    console.log(importqueue);
    for (z in table.data)
    {
      table.data[z]['behindby'] = "No local feed";
      for (a in feeds)
      {
        if (feeds[a]['name'] == table.data[z]['name']) 
        {
          if (feeds[a]['time']!=null)
          {
            var hours = (table.data[z]['time'] - feeds[a]['time'])/(3600*1000); 
            var days = hours / 24;
            if (hours>48) { 
              table.data[z]['behindby'] = parseInt(days)+" days"; 
            } else {
              table.data[z]['behindby'] = parseInt(hours)+" hours";
            } 
          } else { 
            table.data[z]['behindby'] = "Local feed empty";
          }
          break;
        }
      }
      
      table.data[z]['status'] = "";
      if (importqueue[table.data[z]['id']]!=undefined) {

        // Waiting in queue
        if (importqueue[table.data[z]['id']]['queid']>1) table.data[z]['status'] = "<span class='label label-warning'>Waiting, queue position "+importqueue[table.data[z]['id']]['queid']+"</span>";

        // Show processing if first in line
        if (importqueue[table.data[z]['id']]['queid']==1) table.data[z]['status'] = "<span class='label label-success'>Processing</span>";
      }
    }
    table.draw();
  }

  $(table.element).on('click', 'i[type=icon]', function() {
    var row = $(this).attr('row');
    console.log(table.data[row]['datatype']);
    sync.feed(table.data[row]['id'],table.data[row]['name'],table.data[row]['datatype']);
    update();
  });

  $("#savesettings").click(function(){
    var remoteurl = $("#remoteurl").val();
    var remotekey = $("#remotekey").val();
    var result = sync.setsettings(remoteurl,remotekey);
    console.log(result);
    if (result['success']==true) {
      $("#savesettings").hide(); 
      $("#saved").show();
      table.data = sync.get_remote_feeds();
      if (table.data) {
        var updater = setInterval(update, 5000);
        $("#nofeeds").hide(); 
      } else {
        $("#nofeeds").show();
      }
      update();
    }
  });

  $("#remotekey").keyup(function(){
    $("#savesettings").show(); $("#saved").hide();
  });

  $("#remoteurl").keyup(function(){
    $("#savesettings").show(); $("#saved").hide();
  });

</script>
