<?php

  global $mysqli, $session, $path;

  // energyaudit module needs to be installed:
  echo "<script type='text/javascript' src='".$path."Modules/energy/stack_lib/stacks.js'></script>"; 
  echo "<script type='text/javascript' src='".$path."Modules/energy/stack_lib/stack_prepare.js'></script>";
  // Widget renderer 
  echo "<script type='text/javascript' src='".$path."Modules/energy/widget/energy_render.js'></script>";  

  include "Modules/energy/energy_model.php";
  $energy = new Energy($mysqli);

  include "Modules/energy/energytypes.php";

  $energyitems = $energy->get_year($session['userid'], 2013);
?>

<script>
  var energyitems = <?php echo json_encode($energyitems); ?>;
  var energytypes = <?php echo json_encode($energytypes); ?>;

  order_energyitems();

  for (z in energyitems)
  {
    energyitems[z]['data'] = JSON.parse(energyitems[z]['data'] || "null");
  }

  var stacks = prepare_stack();

</script>

<?php

?>
