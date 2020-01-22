<?php global $path; 

  if (!isset($_GET['apikey'])) $apikey = ""; else $apikey = $_GET['apikey'];

?>

<link rel="stylesheet" href="<?php echo $path; ?>Modules/scheduler/timepicker/bootstrap-timepicker.min.css" type="text/css" />

<script type="text/javascript" src="<?php echo $path; ?>Modules/scheduler/timepicker/bootstrap-timepicker.js"></script>

<script language="javascript" type="text/javascript" src="<?php echo $path; ?>Modules/packetgen/packetgen.js"></script>

<script type="text/javascript" src="<?php echo $path; ?>Modules/scheduler/scheduler.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/feed/feed.js"></script>
<style>

  h1 {color:#aaa}
  
  h3 {color:#aaa}
  
  p {color:#aaa}
  
  th {color:#aaa}
  
  .day {
    padding:8px;
  }
  
  .zone-heading {
    color:#aaa;
    font-size:20px;
    font-weight:bold;
    padding-top:10px;
    padding-bottom:10px;
  
  }
  
  .temperature {
    color:#ddd;
  }
  
  .add-on {
    color:#222;
  
  }

.control-title {
	font-weight:bold;
	font-size:20px;
	color:#e1544f;
	padding-top:40px;
  }

	.date-value {
	font-weight:bold;
	font-size:18.5px;
	color:#ccc;
	padding-top:10px;
  }

	.OnOfftime  {
	font-weight:normal;
	font-size:15px;
	color:#0699fa;
	padding-top:10px;
  }
  
  input[type=text] {
  }

</style>

<!-- Make the button and label center aligned -->
  
  <!-- style and output the label -->
 <div style="margin: 0px auto; max-width:300px; padding:0px;">
  <div class="control-title">OEM Heating Controller</div>
  
  
	<div class="date-value" style="float:left"><span id="time" style="color:#fff"></span></div>
	<div class="date-value" style="float:right"><span id="outsidetemp" style="color:#fff"></span></div>
    <div style="clear:both"></div>
  <br>
  <div class="btn-group">
    <button day="Sun" class="day btn">Sun</button>
    <button day="Mon" class="day btn">Mon</button>
    <button day="Tue" class="day btn">Tue</button>
    <button day="Wed" class="day btn">Wed</button>
    <button day="Thu" class="day btn">Thu</button>
    <button day="Fri" class="day btn">Fri</button>
    <button day="Sat" class="day btn">Sat</button>
  </div>

  <div id="zones">
  
    <div class='zone'>
   
    <div class="zone-heading">
      <div style="float:left; padding-top:10px"><span class='zone-name'></span>: <span class="zone-temperature" style="color:#fff"></span></div>

    </div>
    
    <table>
       
      <tr>
        <!--<td>
	        <button class="day btn btn-large btn-success"  style="padding: 0px; padding-top:25px; padding-bottom:25px; margin:0px; width:50px;"></button>
        </td>-->
        <td style="padding-top:12px; padding-left:8px">
		
          <div class="input-append bootstrap-timepicker">         
               
              <input tpkey="on-0" class="timepicker input-small" style="width:45px" type="text"/><span class="add-on"><i class="icon-time"></i></span>
          </div><br>
          <div class="input-append bootstrap-timepicker">
              <input tpkey="on-1" class="timepicker input-small" style="width:45px" type="text"/><span class="add-on"><i class="icon-time"></i></span>
          </div>
        </td>
        <td style="padding-top:12px; padding-left:8px">
          <div class="input-append bootstrap-timepicker">
              <input tpkey="off-0" class="timepicker input-small"  style="width:45px" type="text"/><span class="add-on"><i class="icon-time"></i></span>
          </div><br>
          <div class="input-append bootstrap-timepicker">
              <input tpkey="off-1" class="timepicker input-small" style="width:45px" type="text"/><span class="add-on"><i class="icon-time"></i></span>
          </div>
        </td>
        
        <td style="padding-top:12px; padding-left:8px;">
        
          <div class="input-append">
          <input class='target-temperature-0' type="number" style="width:25px;">
          <span class="add-on" >°C</span>
          </div>
          
          <br>
          
          <div class="input-append">
          <input class='target-temperature-1' type="number" style="width:25px;">
          <span class="add-on">°C</span>
          </div>
          
        </td>
        <td style="padding-left:8px">
          <button class="boost btn btn-large btn-success" style="padding:0px; padding-top:25px; padding-bottom:25px; width:50px" status=1>Boost</button>
        </td>
      </tr>
    </table>
    
    </div>
  
  </div>
  
  <div style="clear:both"></div>
 
  </div>



<script>
var overwritedb = false;

// Call these zones by the same name's as your associated feeds to pick them up
// and show the temperature next to the zone
var zones = ['Kitchen','Room1','Zone3','Zone4','hotwater'];

// Call one of your feeds outside to show outside temperature
var outsidetempname = 'outside';
    
var days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

var date = new Date();
var day = days[date.getDay()];
$(".day[day='"+day+"']").addClass('btn-danger');

var path = "<?php echo $path; ?>";
var apikey = "<?php echo $apikey ?>";

var feeds = feed.list_by_name();
// display day and time

schedule = scheduler.get();

if (!schedule || overwritedb==true)
{
    // Generate default data for new user
    
    var default_heatingperiods = [
      {'setpoint':18, 'on':7.5,'off':10.5},
      {'setpoint':18, 'on':15.5,'off':20.5}
    ];

    var schedule = [];
    for (zoneid in zones)
    {
        // Create zones
        if (schedule[zoneid] == undefined) schedule[zoneid] = {
          name: zones[zoneid],
          boost: false,
          boost_off: 0
        };

        // Add in default heating periods
        
        for (z in days) {
            schedule[zoneid][days[z]] = default_heatingperiods;
        }
    }

    scheduler.set(schedule);
}

$("body").css('background-color','#222');

var zone_template = $(".zone").html();

$("#zones").html("");
for (zoneid in schedule)
{
    $("#zones").append("<div class='zone' zoneid='"+zoneid+"'>"+zone_template+"</div>");

    $(".zone[zoneid="+zoneid+"] .zone-name").html(schedule[zoneid].name);
    $(".zone[zoneid="+zoneid+"] .target-temperature-0").val(schedule[zoneid][day][0].setpoint);
    $(".zone[zoneid="+zoneid+"] .target-temperature-1").val(schedule[zoneid][day][1].setpoint);
    var heatingperiods = schedule[zoneid][day];
}

$('.timepicker').timepicker({showMeridian:false});

var updating_timepickers = false;

update_timepickers();

updateClock();
setInterval(updateClock,10000);   // update clock every 10 seconds

$('.boost').each(function(){    
    var zoneid = parseInt($(this).closest(".zone").attr('zoneid'));

    if (schedule[zoneid].boost==true)
    {
        $(this).removeClass('btn-success');
        $(this).addClass('btn-danger');
    }
});

if (feeds[outsidetempname]!=undefined) $("#outsidetemp").html("Outside: "+feeds[outsidetempname]+"°C");
$(".zone-temperature").each(function()
{
  var zoneid = parseInt($(this).closest(".zone").attr('zoneid'));
  // Fetch the zone name from the scheduler object
  var zonename = schedule[zoneid].name;
  if (feeds[zonename]!=undefined) $(this).html(feeds[zonename]+"°C");
});

function update_timepickers()
{   
    updating_timepickers = true;
    $('.timepicker').each(function(){
        var zoneid = $(this).closest(".zone").attr('zoneid');
        var tpkey = $(this).attr('tpkey');
        tpkey = tpkey.split('-');

        var heatingperiodState = tpkey[0];
        var heatingperiodId = tpkey[1];

        var time = schedule[zoneid][day][heatingperiodId][heatingperiodState];
        $(this).timepicker('setTime',hour_min(time));
    });
    updating_timepickers = false;
}


$('.timepicker').timepicker().on('changeTime.timepicker', function(e) {

    // Dont handle the timepicker change event if we are updating the 
    // timepickers via the program vs user input.
    if (updating_timepickers) return;
    
    var zoneid = $(this).closest(".zone").attr('zoneid');
    var tpkey = $(this).attr('tpkey');
    tpkey = tpkey.split('-');

    var heatingperiodState = tpkey[0];
    var heatingperiodId = parseInt(tpkey[1]);

    var current_time = schedule[zoneid][day][heatingperiodId][heatingperiodState];
    var new_time = e.time.hours + (e.time.minutes / 60.0);
       
    if (heatingperiodState=='on') 
    {
        if (heatingperiodId>0 && new_time<schedule[zoneid][day][heatingperiodId-1]['off']) {
            alert("On time is earlier than previous heating period off time!");
            $(this).timepicker('setTime',hour_min(current_time));
        }
        else if (new_time>schedule[zoneid][day][heatingperiodId]['off']) {
            alert("On time is later than off time!");
            $(this).timepicker('setTime',hour_min(current_time));
        } else {
            schedule[zoneid][day][heatingperiodId][heatingperiodState] = new_time;
            scheduler.set(schedule);
        }
    }

    if (heatingperiodState=='off') 
    {
        if (schedule[zoneid][day][heatingperiodId+1]!=undefined && new_time>schedule[zoneid][day][heatingperiodId+1]['on']) {
            alert("On time is later than next heating period off time!");
            $(this).timepicker('setTime',hour_min(current_time));
        }
        else if (new_time<schedule[zoneid][day][heatingperiodId]['on']) {
            alert("Off time is earlier than on time!");
            $(this).timepicker('setTime',hour_min(current_time));
        } else {
            schedule[zoneid][day][heatingperiodId][heatingperiodState] = new_time;
            scheduler.set(schedule);
        }
    }
});

$('.day').click(function() {

    day = $(this).html();
    update_timepickers();
    
    $('.day').removeClass('btn-danger');
    $(this).addClass('btn-danger');
    
    $(".target-temperature-0").each(function(){
      var zoneid = $(this).closest(".zone").attr('zoneid');
      $(this).val(schedule[zoneid][day][0].setpoint);
    });
    
    $(".target-temperature-1").each(function(){
      var zoneid = $(this).closest(".zone").attr('zoneid');
      $(this).val(schedule[zoneid][day][1].setpoint);
    });
}); 

$('.boost').click(function(){
  
    var zoneid = $(this).closest(".zone").attr('zoneid');
    var date = new Date();
    
    if (schedule[zoneid].boost==false) {
      var unixtimestamp = parseInt(date.getTime()/1000);
    } else {
      var unixtimestamp = schedule[zoneid].boost_off;
    }
    
    var now = parseInt(date.getTime()/1000);
    if ((unixtimestamp - now)<2*3600)
    {
        unixtimestamp += 3600;
        
        schedule[zoneid].boost = true;
        schedule[zoneid].boost_off = unixtimestamp;
        $(this).removeClass('btn-success');
        $(this).addClass('btn-danger');  
        scheduler.set(schedule);
    }
    else
    {
        schedule[zoneid].boost = false;
        schedule[zoneid].boost_off = 0;
        $(this).removeClass('btn-danger');
        $(this).addClass('btn-success');
        $(this).html("Boost");
        scheduler.set(schedule);
    }
    
    updateClock();
    
});

$(".target-temperature-0").change(function(){
    var zoneid = $(this).closest(".zone").attr('zoneid');
    var temperature = $(this).val();
    schedule[zoneid][day][0].setpoint = temperature; 
    scheduler.set(schedule);
});

$(".target-temperature-1").change(function(){
    var zoneid = $(this).closest(".zone").attr('zoneid');
    var temperature = $(this).val();
    schedule[zoneid][day][1].setpoint = temperature; 
    scheduler.set(schedule);
});

function hour_min(time)
{
    var hour = Math.floor(time);
    var minutes = parseInt((time-hour) * 60);
    return hour+':'+minutes;
}

function updateClock()
{
    var date = new Date();

    var minutes = date.getMinutes()*1;
    if (minutes<10) minutes = "0"+minutes;
    time = date.getHours() + ':' + minutes;

    var weekday = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
    var d = weekday[date.getDay()];

    $("#time").html("Time: "+time);
    if (feeds[outsidetempname]!=undefined) $("#outsidetemp").html("Outside: "+feeds[outsidetempname]+"°C");
    $('.boost').each(function(){    
        var zoneid = parseInt($(this).closest(".zone").attr('zoneid'));
        
        if (schedule[zoneid].boost==true)
        {
            var date = new Date();
            var now = parseInt(date.getTime()/1000);

            var seconds_remaining = schedule[zoneid].boost_off - now;

            if (seconds_remaining>0) {
                var hours = Math.floor(seconds_remaining/3600);
                var minutes = parseInt((seconds_remaining - hours*3600)/60);

                if (minutes<10) minutes = "0"+minutes;
                $(this).html(hours+":"+minutes);
            } else {
                schedule[zoneid].boost = false;
                schedule[zoneid].boost_off = 0;
                $(this).removeClass('btn-danger');
                $(this).addClass('btn-success');
                $(this).html("Boost");
                scheduler.set(schedule);
            }
        }
    });
    
    $(".zone-temperature").each(function()
    {
      var zoneid = parseInt($(this).closest(".zone").attr('zoneid'));
      // Fetch the zone name from the scheduler object
      var zonename = schedule[zoneid].name;
      if (feeds[zonename]!=undefined) $(this).html(feeds[zonename]+"°C");
    });
    
}
  
  
</script>
