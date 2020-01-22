<?php
    global $path, $session;
    $apikey = $session['apikey_read'];
?>
  
<script type="text/javascript" src="<?php echo $path; ?>lib/configbasic.js"></script> 
<script type="text/javascript" src="<?php echo $path; ?>lib/flot/jquery.flot.min.js"></script> 
<script type="text/javascript" src="<?php echo $path; ?>lib/flot/jquery.flot.time.min.js"></script> 
<script type="text/javascript" src="<?php echo $path; ?>lib/flot/jquery.flot.selection.min.js"></script> 
<script type="text/javascript" src="<?php echo $path; ?>lib/flot/date.format.js"></script> 
<script type="text/javascript" src="<?php echo $path; ?>lib/vis.helper.js"></script>

<div id="config-error"></div>

<div id="dashboard">
  <div style="background-color:#ccc; padding:10px;">
    <b>MY HEATPUMP</b>
  </div>

  <div style="background-color:#fff; color:#333">
    <br>
    <table style="width:100%; color:#333">
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
      <div class="bluenav bargraph-other">OTHER</div>
      <div class="bluenav bargraph-alltime">ALL TIME</div>
      <div class="bluenav bargraph-month">MONTH</div>
      <div class="bluenav bargraph-week">WEEK</div>
    </div>
    
    <div class="powergraph-navigation" style="display:none">
      <div class="bluenav viewhistory">VIEW HISTORY</div>
      <span class="bluenav" id="right" >></span>
      <span class="bluenav" id="left" ><</span>
      <span class="bluenav" id="zoomout" >-</span>
      <span class="bluenav" id="zoomin" >+</span>
      <span class="bluenav time" time='720'>M</span>
      <span class="bluenav time" time='168'>W</span>
      <span class="bluenav time" time='24'>D</span>
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
        
  <div style="background-color:#eee; color:#333">
    <div id='advanced-toggle' class='bluenav' >SHOW DETAIL</div>
    
    <div style="padding:10px;">
      COP in window: <b id="window-cop"></b>
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
    "heatpump_elec":{"type":"feed", "autoname":"heatpump_elec", "engine":5, "optional":true},
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
var powergraph_series = [];
var previousPoint = false;
var viewmode = "bargraph";
var panning = false;

meta["heatpump_elec_kwh"] = feed.getmeta(feeds["heatpump_elec_kwh"].id);
meta["heatpump_heat_kwh"] = feed.getmeta(feeds["heatpump_heat_kwh"].id);

var start_time = 0;
if (meta["heatpump_elec_kwh"].start_time>start_time) start_time = meta["heatpump_elec_kwh"].start_time;
if (meta["heatpump_heat_kwh"].start_time>start_time) start_time = meta["heatpump_heat_kwh"].start_time;

var heatpump_elec_start = feed.getvalue(feeds["heatpump_elec_kwh"].id, start_time*1000)[1];
var heatpump_heat_start = feed.getvalue(feeds["heatpump_heat_kwh"].id, start_time*1000)[1];

resize();

var end = (new Date()).getTime();

// If this is a new dashboard there will be less than a days data 
// show power graph directly in this case
if (((end*0.001)-start_time)<86400*1) {
  var timeWindow = (end - start_time*1000);
  var start = end - timeWindow;
  view.start = start;
  view.end = end;
  viewmode = "powergraph";
  $(".bargraph-navigation").hide();
  powergraph_load();
  $(".powergraph-navigation").show();
  powergraph_draw();
} else {
  var timeWindow = (3600000*24.0*30);
  var start = end - timeWindow;
  bargraph_load(start,end);
  bargraph_draw();
}

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
        if (feeds["heatpump_elec"]!=undefined) $("#heatpump_elec").html(Math.round(feeds["heatpump_elec"].value));
        if (feeds["heatpump_heat"]!=undefined) $("#heatpump_heat").html(Math.round(feeds["heatpump_heat"].value));
        $("#heatpump_flowT").html(feeds["heatpump_flowT"].value.toFixed(1));
        
        // Update all-time values
        var total_elec = feeds["heatpump_elec_kwh"].value - heatpump_elec_start;
        var total_heat = feeds["heatpump_heat_kwh"].value - heatpump_heat_start;
        var total_cop = 0;
        if (total_elec>0) total_cop = total_heat / total_elec;
        
        $("#total_elec").html(Math.round(total_elec));
        $("#total_heat").html(Math.round(total_heat));
        $("#total_cop").html(total_cop.toFixed(2));
        
        // Updates every 60 seconds
        if (progtime%60==0) {
        
            if (feeds["heatpump_elec"]!=undefined) {
                var min30 = feeds["heatpump_elec"].time - (60*30);
                var min60 = feeds["heatpump_elec"].time - (60*60);
            } else {
                var min30 = feeds["heatpump_elec_kwh"].time - (60*30);
                var min60 = feeds["heatpump_elec_kwh"].time - (60*60);
            }
            var elec = feeds["heatpump_elec_kwh"].value - feed.getvalue(feeds["heatpump_elec_kwh"].id, min30*1000)[1];
            var heat = feeds["heatpump_heat_kwh"].value - feed.getvalue(feeds["heatpump_heat_kwh"].id, min30*1000)[1];
            var COP = 0;
            if (elec>0) COP = heat / elec;
            $("#COP_30m").html(COP.toFixed(2));
        }
        progtime += 5;
    });
}

// -------------------------------------------------------------------------------
// EVENTS
// -------------------------------------------------------------------------------
// The buttons for these powergraph events are hidden when in historic mode 
// The events are loaded at the start here and dont need to be unbinded and binded again.
$("#zoomout").click(function () {view.zoomout(); powergraph_load(); powergraph_draw(); });
$("#zoomin").click(function () {view.zoomin(); powergraph_load(); powergraph_draw(); });
$('#right').click(function () {view.panright(); powergraph_load(); powergraph_draw(); });
$('#left').click(function () {view.panleft(); powergraph_load(); powergraph_draw(); });

$('.time').click(function () {
    view.timewindow($(this).attr("time")/24.0);
    powergraph_load(); powergraph_draw(); 
});

$(".viewhistory").click(function () {
    $(".powergraph-navigation").hide();
    var timeWindow = (3600000*24.0*30);
    var end = (new Date()).getTime();
    var start = end - timeWindow;
    viewmode = "bargraph";
    bargraph_load(start,end);
    bargraph_draw();
    $(".bargraph-navigation").show();
});

$("#advanced-toggle").click(function () { 
    var mode = $(this).html();
    if (mode=="SHOW DETAIL") {
        $("#advanced-block").show();
        $(this).html("HIDE DETAIL");
        
    } else {
        $("#advanced-block").hide();
        $(this).html("SHOW DETAIL");
    }
});

$('#placeholder').bind("plothover", function (event, pos, item) {
    if (item) {
        var z = item.dataIndex;
        
        if (previousPoint != item.datapoint) {
            previousPoint = item.datapoint;

            $("#tooltip").remove();
            if (viewmode=="bargraph")
            {
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
        }
    } else $("#tooltip").remove();
});

// Auto click through to power graph
$('#placeholder').bind("plotclick", function (event, pos, item)
{
    if (item && !panning && viewmode=="bargraph") {
        var z = item.dataIndex;
        view.start = data["heatpump_elec_kwhd"][z][0];
        view.end = view.start + 86400*1000;
        $(".bargraph-navigation").hide();
        viewmode = "powergraph";
        powergraph_load();
        powergraph_draw();
        $(".powergraph-navigation").show();
    }
});

$('#placeholder').bind("plotselected", function (event, ranges) {
    var start = ranges.xaxis.from;
    var end = ranges.xaxis.to;
    panning = true; 

    if (viewmode=="bargraph") {
        bargraph_load(start,end);
        bargraph_draw();
    } else {
        view.start = start; view.end = end;
        powergraph_load();
        powergraph_draw();
    }
    setTimeout(function() { panning = false; }, 100);
});

$('.bargraph-alltime').click(function () {
    var start = start_time * 1000;
    var end = (new Date()).getTime();
    bargraph_load(start,end);
    bargraph_draw();
});

$('.bargraph-week').click(function () {
    var timeWindow = (3600000*24.0*7);
    var end = (new Date()).getTime();
    var start = end - timeWindow;
    bargraph_load(start,end);
    bargraph_draw();
});

$('.bargraph-month').click(function () {
    var timeWindow = (3600000*24.0*30);
    var end = (new Date()).getTime();
    var start = end - timeWindow;
    bargraph_load(start,end);
    bargraph_draw();
});

// -------------------------------------------------------------------------------
// FUNCTIONS
// -------------------------------------------------------------------------------
// - powergraph_load
// - powergraph_draw
// - bargraph_load
// - bargraph_draw
// - resize

function powergraph_load() 
{
    var start = view.start; var end = view.end;
    var npoints = 800;
    var interval = ((end-start)*0.001) / npoints;
    interval = view.round_interval(interval);
    var intervalms = interval * 1000;
    start = Math.ceil(start/intervalms)*intervalms;
    end = Math.ceil(end/intervalms)*intervalms;
    
    powergraph_series = [];

    data["heatpump_flowT"] = feed.getdata(feeds["heatpump_flowT"].id,start,end,interval,1,1);
    data["heatpump_returnT"] = feed.getdata(feeds["heatpump_returnT"].id,start,end,interval,1,1);

    if (feeds["heatpump_elec"]!=undefined) {
        // Where power feed is available
        data["heatpump_elec"] = feed.getdata(feeds["heatpump_elec"].id,start,end,interval,1,1);
        data["heatpump_heat"] = feed.getdata(feeds["heatpump_heat"].id,start,end,interval,1,1);
        powergraph_series.push({label:"Heat", data:data["heatpump_heat"], yaxis:1, color:0, lines:{show:true, fill:0.2, lineWidth:0.5}});
        powergraph_series.push({label:"Elec", data:data["heatpump_elec"], yaxis:1, color:1, lines:{show:true, fill:0.3, lineWidth:0.5}});
    } else {
        // Where no power feed available
        var npoints = 50;
        var interval = ((end-start)*0.001) / npoints;
        interval = view.round_interval(interval);
        if (interval<120) interval = 120;
        var intervalms = interval * 1000;
        start = Math.ceil(start/intervalms)*intervalms;
        end = Math.ceil(end/intervalms)*intervalms;
        
        var tmp = feed.getdata(feeds["heatpump_elec_kwh"].id,start,end,interval,0,0);
        data["heatpump_elec"] = [];
        for (var z=1; z<tmp.length; z++) {
            var time = tmp[z-1][0];
            var diff = tmp[z][1] - tmp[z-1][1];  // diff in kWh
            var power = (diff * 3600000) / interval;
            data["heatpump_elec"].push([time,power]);
        }
        
        var tmp = feed.getdata(feeds["heatpump_heat_kwh"].id,start,end,interval,0,0);
        data["heatpump_heat"] = [];
        for (var z=1; z<tmp.length; z++) {
            var time = tmp[z-1][0];
            var diff = tmp[z][1] - tmp[z-1][1];
            var power = (diff * 3600000) / interval;
            data["heatpump_heat"].push([time,power]);
        }
        powergraph_series.push({label:"Heat", data:data["heatpump_heat"], yaxis:1, color:0, bars:{show:true, barWidth: intervalms * 0.8, fill:0.2}});
        powergraph_series.push({label:"Elec", data:data["heatpump_elec"], yaxis:1, color:1, bars:{show:true, barWidth: intervalms * 0.8, fill:0.3}});
    }
    
    powergraph_series.push({label:"Flow T", data:data["heatpump_flowT"], yaxis:2, color:2});
    powergraph_series.push({label:"Return T", data:data["heatpump_returnT"], yaxis:2, color:3});
    
    var feedstats = {};
    feedstats["heatpump_elec"] = stats(data["heatpump_elec"]);
    feedstats["heatpump_heat"] = stats(data["heatpump_heat"]);
    feedstats["heatpump_flowT"] = stats(data["heatpump_flowT"]);
    feedstats["heatpump_returnT"] = stats(data["heatpump_returnT"]);
    
    if (feedstats["heatpump_elec"].mean>0) 
        $("#window-cop").html((feedstats["heatpump_heat"].mean / feedstats["heatpump_elec"].mean).toFixed(1));
    
    var out = "";
    for (var z in feedstats) {
        out += "<tr>";
        out += "<td style='text-align:left'>"+z+"</td>";
        out += "<td style='text-align:center'>"+feedstats[z].minval.toFixed(2)+"</td>";
        out += "<td style='text-align:center'>"+feedstats[z].maxval.toFixed(2)+"</td>";
        out += "<td style='text-align:center'>"+feedstats[z].diff.toFixed(2)+"</td>";
        out += "<td style='text-align:center'>"+feedstats[z].mean.toFixed(2)+"</td>";
        out += "<td style='text-align:center'>"+feedstats[z].stdev.toFixed(2)+"</td>";
        out += "</tr>";
    }
    $("#stats").html(out);
}

function powergraph_draw() 
{
    var options = {
        lines: { fill: false },
        xaxis: { mode: "time", timezone: "browser", min: view.start, max: view.end},
        yaxes: [{ min: 0 }],
        grid: {hoverable: true, clickable: true},
        selection: { mode: "x" }
    }
    $.plot($('#placeholder'),powergraph_series,options);
}

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
    var height = width*0.5;

    if (height>width) height = width;

    placeholder.width(width);
    placeholder_bound.height(height);
    placeholder.height(height-top_offset);
}

</script>
