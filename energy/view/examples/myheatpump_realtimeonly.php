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
        
        <td style="width:33%; text-align:center" valign="top">
          <b>Heatpump Power</b>
          <h1><span id="heatpump_elec"></span>W</h1>
        </td>
        
        <td style="width:33%; text-align:center" valign="top">
          <b>Heat Output</b>
          <h1><span id="heatpump_heat"></span>W</h1>
        </td>

        <td style="width:33%; text-align:center" valign="top">
          <b>Flow Temperature</b>
          <h1><span id="heatpump_flowT"></span>&deg;C</h1>
        </td>
      </tr>
    </table>
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

// -------------------------------------------------------------------------------
// LOOP
// -------------------------------------------------------------------------------

updater();
setInterval(updater,5000);

function updater()
{
    feed.listbynameasync(function(result){
        feeds = result;
        $("#heatpump_elec").html(Math.round(feeds["heatpump_elec"].value));
        $("#heatpump_heat").html(Math.round(feeds["heatpump_heat"].value));
        $("#heatpump_flowT").html(feeds["heatpump_flowT"].value.toFixed(1));
    });
}

// -------------------------------------------------------------------------------
// EVENTS
// -------------------------------------------------------------------------------

// -------------------------------------------------------------------------------
// FUNCTIONS
// -------------------------------------------------------------------------------

</script>
