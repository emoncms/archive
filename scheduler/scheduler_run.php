<?php

  /*

  All Emoncms code is released under the GNU Affero General Public License.
  See COPYRIGHT.txt and LICENSE.txt.

  ---------------------------------------------------------------------
  Emoncms - open source energy visualisation
  Part of the OpenEnergyMonitor project:
  http://openenergymonitor.org

  */
  
  $userid = 1;
  $start_variable = 4;

  define('EMONCMS_EXEC', 1);

  $fp = fopen("lockfile", "w");
  if (! flock($fp, LOCK_EX | LOCK_NB)) { echo "Already running\n"; die; }

  chdir("/var/www/emoncms");

  // 1) Load settings and core scripts
  require "process_settings.php";
  // 2) Database
  $mysqli = new mysqli($server,$username,$password,$database);

  $redis = new Redis();
  $redis->connect("127.0.0.1");

  include "Modules/packetgen/packetgen_model.php";
  $packetgen = new PacketGen($mysqli,$redis);

  include "Modules/scheduler/scheduler_model.php";
  $scheduler = new Scheduler($mysqli);
  $schedule = $scheduler->get_schedule(1);
  
  while(true)
  {
    $schedule = $scheduler->get_schedule(1);
    
    $timestamp = time();
    $dayofweek = date( "w", $timestamp); // 0 (for Sunday) through 6 (for Saturday)
    $hourofday = date( "H", $timestamp); // 00 through 23
    $minutes = date( "i", $timestamp);   // 00 to 59
    
    $decimaltime = $hourofday + ($minutes/60.0);
    
    if ($dayofweek==0) $dayofweek = 'Sun';
    if ($dayofweek==1) $dayofweek = 'Mon';
    if ($dayofweek==2) $dayofweek = 'Tue';    
    if ($dayofweek==3) $dayofweek = 'Wed';
    if ($dayofweek==4) $dayofweek = 'Thu';
    if ($dayofweek==5) $dayofweek = 'Fri';
    if ($dayofweek==6) $dayofweek = 'Sat';
                
    print "Day of week: ".$dayofweek." Time: ".$hourofday.":".$minutes."\n";
    
    $heatingstate = false;
    $heatingsetpoint = 5;
    
    //-----------------------------------------------------------------------------
    // Heating schedule logic
    //-----------------------------------------------------------------------------
    
    $zones = array();

    foreach ($schedule as $key=>$zone)
    {
      
      $zones[$key]['setpoint'] = $key;
      $zones[$key]['state'] = false;
      $zones[$key]['setpoint'] = 5;
          
      foreach ($zone->$dayofweek as $heatingperiod)
      {
        if ($decimaltime>=$heatingperiod->on && $decimaltime<$heatingperiod->off)
        {
          $zones[$key]['state'] = true;
          $zones[$key]['setpoint'] = $heatingperiod->setpoint;
        }
      }
      
      if (time()>$zone->boost_off) {
        $zone->boost = false;
        // save back to 
        // Turn boost state off
      }
      
      if ($zone->boost==true && time()<$zone->boost_off) {
        $zones[$key]['state'] = true;
        $zones[$key]['setpoint'] = $heatingperiod->setpoint;
      }

      
      if ($zones[$key]['state']) $state = 'ON'; else $state = 'OFF';
      print $key." State: ".$state." Set point: ".$zones[$key]['setpoint']."\n";
    }
    
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------
    
    // Get current state of control packet
    $packet = $packetgen->get($userid);
    $interval = $packetgen->get_interval($userid);
    
    //print json_encode($packet); // Uncommend this to see the contents of packet
    
    // Update the control packet variables for the setpoint and heating state
    $n = $start_variable;
    foreach ($zones as $zoneid=>$zone)
    {
      $name_of_zone = strtolower(str_replace(" ","_",$schedule[$zoneid]->name));
      $packet[$n] = array('name'=>"setpoint_$name_of_zone", 'type'=>1, 'value'=>$zone['setpoint'] * 100);  
      $n++;
      $packet[$n] = array('name'=>"state_$name_of_zone", 'type'=>0, 'value'=>$zone['state']);
      $n++;
    }
    
    // Update the packetgen module with the new state of the packet
    $packetgen->set($userid,json_encode($packet),$interval);
  
    // Script update rate
    sleep(10);
  }
