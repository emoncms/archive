<?php
    global $path, $session;
    $apikey = $session['apikey_read'];
?>

<script type="text/javascript" src="<?php echo $path; ?>lib/feed.js"></script>   
<script type="text/javascript" src="<?php echo $path; ?>lib/configbasic.js"></script> 

<script type="text/javascript" src="<?php echo $path; ?>lib/flot/jquery.flot.min.js"></script> 
<script type="text/javascript" src="<?php echo $path; ?>lib/flot/jquery.flot.time.min.js"></script> 
<script type="text/javascript" src="<?php echo $path; ?>lib/flot/jquery.flot.selection.min.js"></script> 
<script type="text/javascript" src="<?php echo $path; ?>lib/flot/date.format.js"></script> 
<script type="text/javascript" src="<?php echo $path; ?>vis.helper.js"></script>

<div id="config-error"></div>

<div id="dashboard">
  <div style="background-color:#ccc; padding:10px;">
    <b>MY HEATPUMP</b>
  </div>

  <div style="background-color:#fff; color:#333">
    <br>
    <table style="width:100%">
      <tr>
        <td style="width:25%; text-align:center" valign="top">
          <b>COP 30 mins</b>
          <h1><span id="COP_30m"></span></h1>
        </td>
        
        <td style="width:25%; text-align:center" valign="top">
          <b>Heatpump Power</b>
          <h1><span id="heatpump_elec"></span>W</h1>
        </td>
        
        <td style="width:25%; text-align:center" valign="top">
          <b>Heat Output</b>
          <h1><span id="heatpump_heat"></span>W</h1>
        </td>

        <td style="width:25%; text-align:center" valign="top">
          <b>Flow Temperature</b>
          <h1><span id="heatpump_flowT"></span>&deg;C</h1>
        </td>
      </tr>
    </table>
  </div>
  <br>

  <div style="background-color:#ccc;">
  
    <div class="bargraph-navigation">
      <div class="bluenav bargraph-alltime">ALL TIME</div>
      <div class="bluenav bargraph-month">MONTH</div>
      <div class="bluenav bargraph-week">WEEK</div>
    </div>
      
    <div style="padding:10px;">
      <b>HISTORY</b>
    </div>
       
  </div>
  
  <div style="background-color:#fff; padding:10px;">
    <div id="placeholder_bound" style="width:100%; height:500px;">
      <div id="placeholder" style="height:500px"></div>
    </div>
  </div>
        
  <div id="advanced-block" style="background-color:#fff; padding:10px; display:none">
    <div style="color:#000">
      <table class="table">
        <tr>
        <th></th>
        <th style="text-align:center">Min</th>
        <th style="text-align:center">Max</th>
        <th style="text-align:center">Diff</th>
        <th style="text-align:center">Mean</th>
        <th style="text-align:center">StDev</th>
        </tr>
        <tbody id="stats"></tbody>
      </table>
    </div>
  </div>
  <br>
       
  <div style="background-color:#ccc;">  
    <div style="padding:10px;">
      <b>HISTORY</b>
    </div>
  </div>
        
  <div style="background-color:#fff; padding:10px;">
    <table style="width:100%; color:#333;">
    <tr>
      <td style="width:33.3%; text-align:center" valign="top">
        Total Electricity in
        <h1><span id="total_elec"></span> kWh</h1>
      </td>
      
      <td style="width:33.3%; text-align:center" valign="top">
        Total Heat output
        <h1><span id="total_heat"></span> kWh</h1>
      </td>
      
      <td style="width:33.3%; text-align:center" valign="top">
        All-time COP
        <h1><span id="total_cop"></span></h1>
      </td>
    </tr>
    </table>
  </div>
</div>
  
<script>
// Path and apikey
var path = "<?php print $path; ?>";
var apikey = "<?php print $apikey; ?>";
    
// Enable sidebar, set body background
$(".sidenav").show();
$(".container").css({margin:"0 0 0 250px"});
$("body").css('background-color','WhiteSmoke');

// App config
var config = {
    "heatpump_elec":{"type":"feed", "autoname":"heatpump_elec", "engine":"5"},
    "heatpump_elec_kwh":{"type":"feed", "autoname":"heatpump_elec_kwh", "engine":5},
    "heatpump_heat":{"type":"feed", "autoname":"heatpump_heat", "engine":"5"},
    "heatpump_heat_kwh":{"type":"feed", "autoname":"heatpump_heat_kwh", "engine":5},
    "heatpump_flowT":{"type":"feed", "autoname":"heatpump_flowT", "engine":5},
    "heatpump_returnT":{"type":"feed", "autoname":"heatpump_returnT", "engine":5}
};

// Get feed list
var feeds = feed.listbyname();
// Check config and exit if error
if (!check_config()) throw new Error("Configuration not valid");

// -------------------------------------------------------------------------------
// INIT
// -------------------------------------------------------------------------------
var meta = {};
var data = {};
var bargraph_series = [];
var previousPoint = false;

meta["heatpump_elec_kwh"] = feed.getmeta(feeds["heatpump_elec_kwh"].id);
meta["heatpump_heat_kwh"] = feed.getmeta(feeds["heatpump_heat_kwh"].id);

var start_time = 0;
if (meta["heatpump_elec_kwh"].start_time>start_time) start_time = meta["heatpump_elec_kwh"].start_time;
if (meta["heatpump_heat_kwh"].start_time>start_time) start_time = meta["heatpump_heat_kwh"].start_time;

var heatpump_elec_start = feed.getvalue(feeds["heatpump_elec_kwh"].id, start_time*1000)[1];
var heatpump_heat_start = feed.getvalue(feeds["heatpump_heat_kwh"].id, start_time*1000)[1];

resize();

var timeWindow = (3600000*24.0*30);
var end = (new Date()).getTime();
var start = end - timeWindow;
bargraph_load(start,end);
bargraph_draw();

// -------------------------------------------------------------------------------
// LOOP
// -------------------------------------------------------------------------------
var progtime = 0;
updater();
setInterval(updater,5000);

function updater()
{
    feed.listbynameasync(function(result){
        feeds = result;
        $("#heatpump_elec").html(Math.round(feeds["heatpump_elec"].value));
        $("#heatpump_heat").html(Math.round(feeds["heatpump_heat"].value));
        $("#heatpump_flowT").html(feeds["heatpump_flowT"].value.toFixed(1));
        
        // Update all-time values
        var total_elec = feeds["heatpump_elec_kwh"].value - heatpump_elec_start;
        var total_heat = feeds["heatpump_heat_kwh"].value - heatpump_heat_start;
        var total_cop = total_heat / total_elec;
        
        $("#total_elec").html(Math.round(total_elec));
        $("#total_heat").html(Math.round(total_heat));
        $("#total_cop").html(total_cop.toFixed(2));
        
        // Updates every 60 seconds
        if (progtime%60==0) {
            
            var min30 = feeds["heatpump_elec"].time - (60*30);
            var min60 = feeds["heatpump_elec"].time - (60*60);
            
            var elec = feeds["heatpump_elec_kwh"].value - feed.getvalue(feeds["heatpump_elec_kwh"].id, min30*1000)[1];
            var heat = feeds["heatpump_heat_kwh"].value - feed.getvalue(feeds["heatpump_heat_kwh"].id, min30*1000)[1];
            var COP = heat / elec;
            $("#COP_30m").html(COP.toFixed(2));
        }
        progtime += 5;
    });
}

// -------------------------------------------------------------------------------
// EVENTS
// -------------------------------------------------------------------------------

$('#placeholder').bind("plothover", function (event, pos, item) {
    if (item) {
        var z = item.dataIndex;
        
        if (previousPoint != item.datapoint) {
            previousPoint = item.datapoint;

            $("#tooltip").remove();
            var itemTime = item.datapoint[0];
            var elec_kwh = data["heatpump_elec_kwhd"][z][1];
            var heat_kwh = data["heatpump_heat_kwhd"][z][1];
            var COP = heat_kwh / elec_kwh;

            var d = new Date(itemTime);
            var days = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
            var months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
            var date = days[d.getDay()]+", "+months[d.getMonth()]+" "+d.getDate();
            tooltip(item.pageX, item.pageY, date+"<br>Electric: "+(elec_kwh).toFixed(1)+" kWh<br>Heat: "+(heat_kwh).toFixed(1)+" kWh<br>COP: "+(COP).toFixed(1), "#fff");
        }
    } else $("#tooltip").remove();
});

$('#placeholder').bind("plotselected", function (event, ranges) {
    var start = ranges.xaxis.from;
    var end = ranges.xaxis.to;
    bargraph_load(start,end);
    bargraph_draw();
});

$('.bargraph-alltime').click(function () {
    var start = start_time * 1000;
    var end = +new Date;
    bargraph_load(start,end);
    bargraph_draw();
});

$('.bargraph-week').click(function () {
    var timeWindow = (3600000*24.0*7);
    var td = new Date();
    var end = td.getTime();
    var start = end - timeWindow;
    bargraph_load(start,end);
    bargraph_draw();
});

$('.bargraph-month').click(function () {
    var timeWindow = (3600000*24.0*30);
    var td = new Date();
    var end = td.getTime();
    var start = end - timeWindow;
    bargraph_load(start,end);
    bargraph_draw();
});

// -------------------------------------------------------------------------------
// FUNCTIONS
// -------------------------------------------------------------------------------

function bargraph_load(start,end) 
{   
    var interval = 3600*24;
    var intervalms = interval * 1000;
    end = Math.ceil(end/intervalms)*intervalms;
    start = Math.floor(start/intervalms)*intervalms;
    
    var elec_result = feed.getdataDMY(feeds["heatpump_elec_kwh"].id,start,end,"daily");
    var heat_result = feed.getdataDMY(feeds["heatpump_heat_kwh"].id,start,end,"daily");
    
    var elec_data = [];
    var heat_data = [];
    
    // remove nan values from the end.
    for (var z in elec_result) {
      if (elec_result[z][1]!=null) elec_data.push(elec_result[z]);
      if (heat_result[z][1]!=null) heat_data.push(heat_result[z]);
    }
    
    data["heatpump_elec_kwhd"] = [];
    data["heatpump_heat_kwhd"] = [];
    
    if (elec_data.length>0) {
        var lastday = elec_data[elec_data.length-1][0];
        
        var d = new Date();
        d.setHours(0,0,0,0);
        if (lastday==d.getTime()) {
            // last day in kwh data matches start of today from the browser's perspective
            // which means its safe to append today kwh value
            var next = elec_data[elec_data.length-1][0] + (interval*1000);
            elec_data.push([next,feeds["heatpump_elec_kwh"].value]);
            heat_data.push([next,feeds["heatpump_heat_kwh"].value]);
        }
 
        // Calculate the daily totals by subtracting each day from the day before
        for (var z=1; z<elec_data.length; z++)
        {
            var time = elec_data[z-1][0];
            var elec_kwh = (elec_data[z][1]-elec_data[z-1][1]);
            var heat_kwh = (heat_data[z][1]-heat_data[z-1][1]);
            data["heatpump_elec_kwhd"].push([time,elec_kwh]);
            data["heatpump_heat_kwhd"].push([time,heat_kwh]);
        }
    }

    bargraph_series = [];

    bargraph_series.push({
        data: data["heatpump_heat_kwhd"], color: 0,
        bars: { show: true, align: "center", barWidth: 0.75*3600*24*1000, fill: 1.0, lineWidth:0}
    });
    
    bargraph_series.push({
        data: data["heatpump_elec_kwhd"], color: 1,
        bars: { show: true, align: "center", barWidth: 0.75*3600*24*1000, fill: 1.0, lineWidth:0}
    });
}

function bargraph_draw() 
{
    var options = {
        xaxis: { mode: "time", timezone: "browser"},
        grid: {hoverable: true, clickable: true},
        selection: { mode: "x" }
    }

    var plot = $.plot($('#placeholder'),bargraph_series,options);
    $('#placeholder').append("<div id='bargraph-label' style='position:absolute;left:50px;top:30px;color:#666;font-size:12px'></div>");
}

function resize() {
    var top_offset = 0;
    var placeholder_bound = $('#placeholder_bound');
    var placeholder = $('#placeholder');

    var width = placeholder_bound.width();
    var height = $(window).height()*0.4;

    if (height>width) height = width;

    placeholder.width(width);
    placeholder_bound.height(height);
    placeholder.height(height-top_offset);
}

</script>
