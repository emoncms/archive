<?php
    global $path, $session;
    $apikey = $session['apikey_read'];
?>

<script type="text/javascript" src="<?php echo $path; ?>lib/feed.js"></script>   
<script type="text/javascript" src="<?php echo $path; ?>lib/configbasic.js"></script> 

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

meta["heatpump_elec_kwh"] = feed.getmeta(feeds["heatpump_elec_kwh"].id);
meta["heatpump_heat_kwh"] = feed.getmeta(feeds["heatpump_heat_kwh"].id);

var start_time = 0;
if (meta["heatpump_elec_kwh"].start_time>start_time) start_time = meta["heatpump_elec_kwh"].start_time;
if (meta["heatpump_heat_kwh"].start_time>start_time) start_time = meta["heatpump_heat_kwh"].start_time;

var heatpump_elec_start = feed.getvalue(feeds["heatpump_elec_kwh"].id, start_time*1000)[1];
var heatpump_heat_start = feed.getvalue(feeds["heatpump_heat_kwh"].id, start_time*1000)[1];

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

// -------------------------------------------------------------------------------
// FUNCTIONS
// -------------------------------------------------------------------------------

</script>
