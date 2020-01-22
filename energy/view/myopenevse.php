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
<script type="text/javascript" src="<?php echo $path; ?>lib/pie.js"></script>

<style>
.electric-title {
    font-weight:bold;
    font-size:22px;
    color:#44b3e2;
}

.power-value {
    font-weight:bold; 
    font-size:52px; 
    color:#44b3e2;
}
</style>

<div id="config-error"></div>

<div id="dashboard">

  <div style="background-color:rgba(68,179,226,1.0)">

    <div class="bluenav cost">Cost</div>
    <div class="bluenav energy">Energy</div>
    
    <div style="padding:10px;">
      <b>MY OPENEVSE</b>
    </div>

  </div>

  <div style="background-color:#fff; color:#333; padding:10px;">
    <table style="width:100%">
      <tr>
        <td style="width:33%">
            <div class="electric-title">POWER NOW</div>
            <div class="power-value"><span id="power_now">0</span>W</div>
        </td>
        <td style="text-align:center; width:33%"></td>
        <td style="text-align:right; width:33%">
            <div class="electric-title">USE TODAY</div>
            <div class="power-value"><span id="kwh_today">0</span> kWh</div>
        </td>
      </tr>
    </table>
  </div>
  <br>

  <div style="background-color:rgba(68,179,226,1.0);">
  
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
  
  <div style="background-color:rgba(68,179,226,0.1); padding:10px;">
    <div id="placeholder_bound" style="width:100%; height:500px;">
      <div id="placeholder" style="height:500px"></div>
    </div>
  </div>
        
  <div style="background-color:#eee; color:#333;">
    <div id='advanced-toggle' class='bluenav' >SHOW DETAIL</div>
    <div style="clear:both"></div>
  </div>
        
  <div id="advanced-block" style="background-color:#eee; padding:10px; display:none">
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
  
  <div style="background-color:rgba(68,179,226,1.0); padding:10px;">
    <b>TIME OF USE</b>
  </div>
  <div style="background-color:rgba(68,179,226,0.1); padding:10px; color:#333">
    <div style="width:400px; text-align:center; float:right; padding-top:100px;">
        <p><b><span id="prclocal">--</span>%</b> Off-peak power<br><span style="font-size:12px">In the last 7 days</span></p>
        <img id="star1" src="<?php echo $path; ?>files/star20.png" style="width:45px">
        <img id="star2" src="<?php echo $path; ?>files/star20.png" style="width:45px">
        <img id="star3" src="<?php echo $path; ?>files/star20.png" style="width:45px">
        <img id="star4" src="<?php echo $path; ?>files/star20.png" style="width:45px">
        <img id="star5" src="<?php echo $path; ?>files/star20.png" style="width:45px">
    </div>
    
    <canvas id="piegraph" width=420 height=400 ></canvas>
  </div>
  <br>    
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
    usename:{"type":"feed", "autoname":"OpenEVSE_POWER", "engine":"5"},
    "use_kwh":{"type":"feed", "autoname":"OpenEVSE_KWH", "engine":5}
};

var usename = "OpenEVSE_POWER";
var kwhname = "OpenEVSE_KWH";

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
var period_text = "month";
var period_average = 0;
var comparison_heating = false;
var comparison_transport = false;

meta[kwhname] = feed.getmeta(feeds[kwhname].id);

var start_time = 0;
if (meta[kwhname].start_time>start_time) start_time = meta[kwhname].start_time;

var use_start = feed.getvalue(feeds[kwhname].id, start_time*1000)[1];

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

/*
var timeWindow = (3600000*24.0*30);
var end = (new Date()).getTime();
var start = end - timeWindow;
bargraph_load(start,end);
bargraph_draw();
timeofuse_load();
*/
// -------------------------------------------------------------------------------
// LOOP
// -------------------------------------------------------------------------------
updater();
setInterval(updater,5000);

function updater()
{
    feed.listbynameasync(function(result){
        feeds = result;
        $("#power_now").html(Math.round(feeds[usename].value));
        
        // Update all-time values
        var total_elec = feeds[kwhname].value - use_start;
        
        $("#total_elec").html(Math.round(total_elec));
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
            var itemTime = item.datapoint[0];
            var elec_kwh = data["use_kwhd"][z][1];

            var d = new Date(itemTime);
            var days = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
            var months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
            var date = days[d.getDay()]+", "+months[d.getMonth()]+" "+d.getDate();
            tooltip(item.pageX, item.pageY, date+"<br>"+(elec_kwh).toFixed(1)+" kWh", "#fff");
        }
    } else $("#tooltip").remove();
});

// Auto click through to power graph
$('#placeholder').bind("plotclick", function (event, pos, item)
{
    if (item && !panning && viewmode=="bargraph") {
        var z = item.dataIndex;
        view.start = data["use_kwhd"][z][0];
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
    period_text = "period";
    timeofuse_load();
    energystacks_draw();
});

$('.bargraph-week').click(function () {
    var timeWindow = (3600000*24.0*7);
    var end = (new Date()).getTime();
    var start = end - timeWindow;
    bargraph_load(start,end);
    bargraph_draw();
    period_text = "week";
    timeofuse_load();
    energystacks_draw();
});

$('.bargraph-month').click(function () {
    var timeWindow = (3600000*24.0*30);
    var end = (new Date()).getTime();
    var start = end - timeWindow;
    bargraph_load(start,end);
    bargraph_draw();
    period_text = "month";
    timeofuse_load();
    energystacks_draw();
});

$("#heating").click(function() {
    comparison_heating = 0;
    if ($(this)[0].checked) comparison_heating = 1;
    energystacks_draw();
});

$("#transport").click(function() {
    comparison_transport = 0;
    if ($(this)[0].checked) comparison_transport = 1;
    energystacks_draw();
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

    data[usename] = feed.getdata(feeds[usename].id,start,end,interval,1,1);
    
    powergraph_series = [];
    powergraph_series.push({data:data[usename], yaxis:1, color:"#44b3e2", lines:{show:true, fill:0.8, lineWidth:0}});
    
    var feedstats = {};
    feedstats[usename] = stats(data[usename]);
    
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
    
    var elec_result = feed.getdataDMY(feeds[kwhname].id,start,end,"daily");
    
    var elec_data = [];
    
    // remove nan values from the end.
    for (var z in elec_result) {
      if (elec_result[z][1]!=null) elec_data.push(elec_result[z]);
    }
    
    data["use_kwhd"] = [];
    
    if (elec_data.length>0) {
        var lastday = elec_data[elec_data.length-1][0];
        
        var d = new Date();
        d.setHours(0,0,0,0);
        if (lastday==d.getTime()) {
            // last day in kwh data matches start of today from the browser's perspective
            // which means its safe to append today kwh value
            var next = elec_data[elec_data.length-1][0] + (interval*1000);
            elec_data.push([next,feeds[kwhname].value]);
        }
 
        var total_kwh = 0; 
        var n = 0;
        // Calculate the daily totals by subtracting each day from the day before
        for (var z=1; z<elec_data.length; z++)
        {
            var time = elec_data[z-1][0];
            var elec_kwh = (elec_data[z][1]-elec_data[z-1][1]);
            data["use_kwhd"].push([time,elec_kwh]);
            total_kwh += elec_kwh;
            n++;
        }
        period_average = total_kwh / n;
    }

    bargraph_series = [];
    
    bargraph_series.push({
        data: data["use_kwhd"], color: "#44b3e2",
        bars: { show: true, align: "center", barWidth: 0.75*3600*24*1000, fill: 1.0, lineWidth:0}
    });
    
    var kwh_today = feeds[kwhname].value*1.0;
    if (data["use_kwhd"].length>0)
        kwh_today = data["use_kwhd"][data["use_kwhd"].length-1][1];
    $("#kwh_today").html(kwh_today.toFixed(1));
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

function timeofuse_load() 
{
  $.ajax({                                      
      url: path+"household/data?id="+feeds[usename].id,
      dataType: 'json',                  
      success: function(result) {
          console.log("here...");
          var prc = Math.round(100*((result.overnightkwh + result.middaykwh) / result.totalkwh));
          $("#prclocal").html(prc);
          
          if (prc>20) $("#star1").attr("src",path+"files/star.png");
          if (prc>40) setTimeout(function() { $("#star2").attr("src",path+"files/star.png"); }, 100);
          if (prc>60) setTimeout(function() { $("#star3").attr("src",path+"files/star.png"); }, 200);
          if (prc>80) setTimeout(function() { $("#star4").attr("src",path+"files/star.png"); }, 300);
          if (prc>90) setTimeout(function() { $("#star5").attr("src",path+"files/star.png"); }, 400);
          
          var data = [
            {name:"AM PEAK", value: result.morningkwh, color:"rgba(68,179,226,0.8)"},
            {name:"DAYTIME", value: result.middaykwh, color:"rgba(68,179,226,0.6)"},
            {name:"PM PEAK", value: result.eveningkwh, color:"rgba(68,179,226,0.9)"},
            {name:"NIGHT", value: result.overnightkwh, color:"rgba(68,179,226,0.4)"},
            // {name:"HYDRO", value: 2.0, color:"rgba(255,255,255,0.2)"}   
          ];
          
          var options = {
            "color": "#333",
            "centertext": "THIS "+period_text.toUpperCase()
          }; 
          
          piegraph("piegraph",data,options);
      } 
  });
}

function resize() {
    var top_offset = 0;
    var placeholder_bound = $('#placeholder_bound');
    var placeholder = $('#placeholder');

    var width = placeholder_bound.width();
    var height = width*0.5;

    if (height>width) height = width;
    
    console.log(width+" "+height);

    placeholder.width(width);
    placeholder_bound.height(height);
    placeholder.height(height-top_offset);
}

</script>
