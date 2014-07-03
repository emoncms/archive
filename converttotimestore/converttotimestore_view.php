<?php 
  global $path; 
?>

<script type="text/javascript" src="<?php echo $path; ?>Modules/converttotimestore/converttotimestore.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Lib/tablejs/table.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Lib/tablejs/custom-table-fields.js"></script>
<style>
input[type="text"] {
     width: 88%; 
}
</style>

<br>

<div class="container">

    <div id="localheading"><h2><?php echo _('Convert feeds to timestore'); ?></h2></div>
    
    <p>Tests so far have shown timestore to be <a src="http://openenergymonitor.blogspot.co.uk/2013/07/from-13-minutes-to-196ms-timestore-on.html" >several magnitudes faster</a> while also using significantly less disk space than the mysql based storage of time series data that has been used by emoncms up to this point.</p>
    
    <p>One of the central principles behind the <a src="mikestirling.co.uk/redmine/projects/timestore" >Timestore data storage</a> approach is that datapoints are stored at a fixed interval. Because you know that there is a datapoint every 10s you dont need to store the timestamp for each datapoint, you only need to store the timestamp for the first datapoint. The timestamp for every other datapoint can be worked out i.e:</p>

    <p><pre>timestamp = start + position * interval.</pre></p>

    <p>Storing time series data in this way makes it really easy and very fast to query. </p>

    <p>The following interface provides an opportunity to review and select your preferred interval rate for each realtime feed that your logging.</p>

    <p>The table below details the 1st and 2nd most common datapoint interval found in the last 2000 datapoints of your feeds, it also shows the average interval in the last 2000 datapoints and the average interval in the whole feed. Usually the interval you will want to select is somewhere between the average in the last 2000 datapoints and the average in the whole feed, however sometimes these averages can be skewed by the monitor going off for extended periods of time and so the 1st most common interval may be a better guide.</p>

    <p>You may wish to change your interval rate, if your logging temperature data at 5s intervals and 60s is enough to see the changes in temperature you want to see then select 60s as this reduces the disk use of the feed considerably.</p> 

    <p>To set the interval rate you wish your feed to be converted to, click on the <b>pencil button</b> to bring up in-line editing, select the interval rate from the dropdown menu and then click on the tick to save.</p>

    <button id="recalculate" class="btn btn-large" style="float:right" >Re-calculate feed intervals <i class="icon-refresh"></i></button>
    <h3>1) Select timestore feed intervals <span id="calculating" class="alert">Calculating feed intervals..</span></h3>
    <br>

    <div id="table"></div>

    <div id="nofeeds" class="alert alert-block hide">
        <h4 class="alert-heading"><?php echo _('No feeds to convert'); ?></h4>

    </div>
    
    <hr> 

    <h3>2) Run the conversion script</h3>
    <p>The run the conversion process the conversion script needs to be run via terminal, these are the steps to do this:</p>
    <p>1) Start-up terminal, if you use a raspberry-pi ssh into it ie:</p>
    <pre>ssh pi@192.168.1.75</pre>
    <p>2) Run the conversion script:</p>
    <pre>php /var/www/emoncms/Modules/converttotimestore/convert.php</pre>
    <p>You should now see the conversion script working through the feeds you selected for conversion, it may take a while depending on the size of your feeds. Once done check that the feed data has converted correctly, that the interval is sufficient. When your happy you can delete your mysql feed data by running the delete mysql copy script:</p>
    <pre>php /var/www/emoncms/Modules/converttotimestore/deletemysqlcopy.php</pre>

</div>

<script>


  var path = "<?php echo $path; ?>";

  var updater = false;

  scan();
  
  function scan(){
    $("#calculating").show();
    $("#recalculate").hide();
    updater = setInterval(update, 2000);
    
    $.ajax({ url: path+"converttotimestore/scan.json", data: "", async: true, success: function(data){
      $("#calculating").hide();
      $("#recalculate").show();
      clearInterval(updater);
      update();
    } });
  }
  
  $("#recalculate").click(function() { scan(); });
  
  
  // Extemd table library field types
  for (z in customtablefields) table.fieldtypes[z] = customtablefields[z];

  table.element = "#table";

  table.fields = {
    'id':{'title':"<?php echo _('FeedID'); ?>", 'type':"fixed"},
    'name':{'title':"<?php echo _('Name'); ?>", 'type':"fixed"},
        
    'intA':{'title':"Most common<br>interval in last 2000<br>datapoints", 'type':"fixed"},
    'prcA':{'title':"% of<br>datapoints", 'type':"fixed"},
    
    'intB':{'title':"2nd most common<br>interval in last 2000<br>datapoints", 'type':"fixed"},
    'prcB':{'title':"% of<br>datapoints", 'type':"fixed"},
    
    'average':{'title':"Average interval<br>last 2000 dps", 'type':"fixed"},
    'totalaverage':{'title':"Total average<br>all time", 'type':"fixed"},
    
    'convertto':{'title':"<?php echo _('Convert to fixed<br>timestore interval of:'); ?>", 'type':"select", 'options':{0:'not set',5:'5s',10:'10s',15:'15s',20:'20s',25:'25s',30:'30s',60:'60s',120:'2 mins',300:'5 mins',600:'10 mins',1800:'30 mins',3600:'1 hour',21600:'6 hours',43200:'12 hours',86400:'24 hours'}},
    
    // Actions
    'edit-action':{'title':'', 'type':"edit"}

  }

  table.groupby = 'tag';
  table.deletedata = false;

  table.draw();
  update();

  function update()
  {
    table.data = converttotimestore.get();
    table.draw();
    if (table.data.length != 0) {
      $("#nofeeds").hide();         
    } else {
      $("#nofeeds").show();
    }
  }

  $("#table").bind("onEdit", function(e){

  });

  $("#table").bind("onSave", function(e,id,fields_to_update){
    var converttime = fields_to_update['convertto'];
    console.log(id+" "+converttime);
    converttotimestore.set(id,converttime); 
  });

</script>
