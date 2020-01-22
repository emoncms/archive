<?php

  $energytypes = array();

  $energytypes['greenelec'] = array(
    'name'=>"100% Green Electric", 
    'units'=>"kWh", 
    'kwh'=>1, 
    'unitcost'=>0.15,
    'carbon'=>0, 
    'options'=>array(

      'quantity' => array(
        'name'=>"Annual use", 
        'default'=>0,
        'units'=>'kWh',
      )
    ),
    'procfn'=>1,
    'order'=>0
  );

  $energytypes['electric'] = array(
    'name'=>"Non-Green Electric", 
    'units'=>"kWh", 
    'kwh'=>1, 
    'unitcost'=>0.15,
    'carbon'=>1, 
    'options'=>array(

      'quantity' => array(
        'name'=>"Annual use", 
        'default'=>0,
        'units'=>'kWh',
      )
    ),
    'procfn'=>1,
    'order'=>1
  );

  $energytypes['stor'] = array(
    'name'=>"Storage", 
    'units'=>"kWh", 
    'kwh'=>1, 
    'unitcost'=>0.15,
    'carbon'=>0, 
    'options'=>array(

      'quantity' => array(
        'name'=>"Annual use", 
        'default'=>0,
        'units'=>'kWh',
      )
    ),
    'procfn'=>1,
    'order'=>2
  );

  $energytypes['hp'] = array(
    'name'=>"Heatpump", 
    'units'=>"kWh", 
    'kwh'=>1, 
    'unitcost'=>0.15,
    'carbon'=>0, 
    'options'=>array(

      'quantity' => array(
        'name'=>"Annual use", 
        'default'=>0,
        'units'=>'kWh',
      ),
      'efficiency' => array(
        'name'=>"Efficiency", 
        'default'=>250,
        'units'=>'%',
      )
    ),
    'procfn'=>2,
    'order'=>3
  );

  $energytypes['wood'] = array(
    'name'=>"Wood", 
    'units'=>"m3", 
    'kwh'=>1380, 
    'unitcost'=>63.5,
    'carbon'=>0, 
    'options'=>array(

      'quantity' => array(
        'name'=>"Annual use", 
        'default'=>0,
        'units'=>'m3',
      ),
      'efficiency' => array(
        'name'=>"Efficiency", 
        'default'=>100,
        'units'=>'%',
      )
    ),
    'procfn'=>2,
    'order'=>4
  );

  $energytypes['woodpellet'] = array(
    'name'=>"Wood Pellet", 
    'units'=>"m3", 
    'kwh'=>4800, 
    'unitcost'=>240,
    'carbon'=>0, 
    'options'=>array(

      'quantity' => array(
        'name'=>"Annual use", 
        'default'=>0,
        'units'=>'m3',
      ),
      'efficiency' => array(
        'name'=>"Efficiency", 
        'default'=>100,
        'units'=>'%',
      )
    ),
    'procfn'=>2,
    'order'=>5
  );

  $energytypes['oil'] = array(
    'name'=>"Oil", 
    'units'=>"L", 
    'kwh'=>10.27, 
    'unitcost'=>0.6,
    'carbon'=>0.1, 
    'options'=>array(

      'quantity' => array(
        'name'=>"Annual use", 
        'default'=>0,
        'units'=>'L',
      ),
      'efficiency' => array(
        'name'=>"Efficiency", 
        'default'=>100,
        'units'=>'%',
      )
    ),
    'procfn'=>2,
    'order'=>6
  );

  $energytypes['mainsgas'] = array(
    'name'=>"Gas", 
    'units'=>"m3", 
    'kwh'=>9.8, 
    'unitcost'=>0.42,
    'carbon'=>0.1, 
    'options'=>array(

      'quantity' => array(
        'name'=>"Annual use", 
        'default'=>0,
        'units'=>'m3',
      ),
      'efficiency' => array(
        'name'=>"Efficiency", 
        'default'=>100,
        'units'=>'%',
      )
    ),
    'procfn'=>2,
    'order'=>7
  );

  $energytypes['lpg'] = array(
    'name'=>"LPG", 
    'units'=>"L", 
    'kwh'=>11, 
    'unitcost'=>0.5,
    'carbon'=>0.1, 
    'options'=>array(

      'quantity' => array(
        'name'=>"Annual use", 
        'default'=>0,
        'units'=>'L',
      ),
      'efficiency' => array(
        'name'=>"Efficiency", 
        'default'=>100,
        'units'=>'%',
      )
    ),
    'procfn'=>2,
    'order'=>8
  );

  $energytypes['botgas'] = array(
    'name'=>"Gas", 
    'units'=>"kg", 
    'kwh'=>13.9, 
    'unitcost'=>1.8,
    'carbon'=>0.1, 
    'options'=>array(

      'quantity' => array(
        'name'=>"Annual use", 
        'default'=>0,
        'units'=>'kg',
      ),
      'efficiency' => array(
        'name'=>"Efficiency", 
        'default'=>100,
        'units'=>'%',
      )
    ),
    'procfn'=>2,
    'order'=>9
  );

  $energytypes['coal'] = array(
    'name'=>"Coal", 
    'units'=>"kg", 
    'kwh'=>6.67, 
    'unitcost'=>0.49,
    'carbon'=>0.1, 
    'options'=>array(

      'quantity' => array(
        'name'=>"Annual use", 
        'default'=>0,
        'units'=>'kg',
      ),
      'efficiency' => array(
        'name'=>"Efficiency", 
        'default'=>100,
        'units'=>'%',
      )
    ),
    'procfn'=>2,
    'order'=>10
  );

  $energytypes['ecar'] = array(
    'name'=>"E Car", 
    'units'=>"miles", 
    'kwh'=>0.5, 
    'unitcost'=>0.02,
    'carbon'=>0.0, 
    'options'=>array(

      'quantity' => array(
        'name'=>"Annual use", 
        'default'=>0,
        'units'=>'miles',
      )
    ),
    'procfn'=>2,
    'order'=>11
  );

  $energytypes['car1'] = array(
    'name'=>"Car 1", 
    'units'=>"miles", 
    'kwh'=>45.5, 
    'unitcost'=>0.16,
    'carbon'=>0.1, 
    'options'=>array(

      'miles' => array(
        'name'=>"Annual use", 
        'default'=>0,
        'units'=>'miles',
      ),
      'mpg' => array(
        'name'=>"MPG", 
        'default'=>40,
        'units'=>'mpg',
      )
    ),
    'procfn'=>3,
    'order'=>12
  );

  $energytypes['car2'] = array(
    'name'=>"Car 2", 
    'units'=>"miles", 
    'kwh'=>45.5, 
    'unitcost'=>0.16,
    'carbon'=>0.1, 
    'options'=>array(

      'miles' => array(
        'name'=>"Annual use", 
        'default'=>0,
        'units'=>'miles',
      ),
      'mpg' => array(
        'name'=>"MPG", 
        'default'=>40,
        'units'=>'mpg',
      )
    ),
    'procfn'=>3,
    'order'=>13
  );

  $energytypes['car3'] = array(
    'name'=>"Car 3", 
    'units'=>"miles", 
    'kwh'=>45.5, 
    'unitcost'=>0.16,
    'carbon'=>0.1, 
    'options'=>array(

      'miles' => array(
        'name'=>"Annual use", 
        'default'=>0,
        'units'=>'miles',
      ),
      'mpg' => array(
        'name'=>"MPG", 
        'default'=>40,
        'units'=>'mpg',
      )
    ),
    'procfn'=>3,
    'order'=>14
  );

  $energytypes['mbike'] = array(
    'name'=>"MotorBike", 
    'units'=>"miles", 
    'kwh'=>45.5, 
    'unitcost'=>0.16,
    'carbon'=>0.1, 
    'options'=>array(

      'miles' => array(
        'name'=>"Annual use", 
        'default'=>0,
        'units'=>'miles',
      ),
      'mpg' => array(
        'name'=>"MPG", 
        'default'=>40,
        'units'=>'mpg',
      )
    ),
    'procfn'=>3,
    'order'=>14
  );

  $energytypes['bus'] = array(
    'name'=>"Bus", 
    'units'=>"miles", 
    'kwh'=>0.53, 
    'unitcost'=>0.0,
    'carbon'=>0.1, 
    'options'=>array(

      'quantity' => array(
        'name'=>"Annual use", 
        'default'=>0,
        'units'=>'miles',
      )
    ),
    'procfn'=>1,
    'order'=>16
  );

  $energytypes['train'] = array(
    'name'=>"Train", 
    'units'=>"miles", 
    'kwh'=>0.096, 
    'unitcost'=>0.0,
    'carbon'=>0.1, 
    'options'=>array(

      'quantity' => array(
        'name'=>"Annual use", 
        'default'=>0,
        'units'=>'miles',
      )
    ),
    'procfn'=>1,
    'order'=>17
  );

  $energytypes['boat'] = array(
    'name'=>"Boat", 
    'units'=>"miles", 
    'kwh'=>1.0, 
    'unitcost'=>0.0,
    'carbon'=>0.1, 
    'options'=>array(

      'quantity' => array(
        'name'=>"Annual use", 
        'default'=>0,
        'units'=>'miles',
      )
    ),
    'procfn'=>1,
    'order'=>18
  );

  $energytypes['plane'] = array(
    'name'=>"Plane", 
    'units'=>"miles", 
    'kwh'=>0.69, 
    'unitcost'=>0.0,
    'carbon'=>0.1, 
    'options'=>array(

      'quantity' => array(
        'name'=>"Annual use", 
        'default'=>0,
        'units'=>'miles',
      )
    ),
    'procfn'=>1,
    'order'=>19
  );


?>
