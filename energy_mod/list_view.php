<?php 

  /*

  list_view.php - licence GNU GPL Affero, Author Trystan Lea

  Overview is always on a annual basis so the requirement of any energy input
  is to calculate an annual figure.

  I see there being several levels of calculator

  	1. basic inline form
  	2. auto energy monitor
 	3. detailed form calculator

  the overview page has basic inline calculator and can link to more detailed calculators.

  Each item should have a property list and a processor function

  green electric:	annual use				straight conversion
  wood:			annual use	stove efficiency	% efficiency
  oil:			annual use	boiler efficiency	
  car:			annual use	mpg	petrol		car proc

  wood: { options:{'annual m3', 'efficiency'}, convert:{ kwh:1380, unitcost:63.5 }, process_function: 2

  */

  global $path; 

  $exampledata = false;
  if (!$energyitems) 
  {
    $energyitems[] = array('tag'=>"greenelec",'year'=>2013,'data'=>'{"quantity":"3300","efficiency":100}');
    $energyitems[] = array('tag'=>"mainsgas",'year'=>2013,'data'=>'{"quantity":"1750","efficiency":85}');
    $energyitems[] = array('tag'=>"car1",'year'=>2013,'data'=>'{"quantity":0,"efficiency":100,"miles":"5000","mpg":40}');

    $exampledata = true;
  }

?>

<script type="text/javascript" src="<?php echo $path; ?>Modules/energy/stack_lib/stacks.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/energy/stack_lib/stack_prepare.js"></script>

<style>

.itembox {
  -webkit-border-radius: 6px;
  -moz-border-radius: 6px;
  border-radius: 6px;

  width:95.5%; 
  background-color:#fff; 
  margin-bottom:15px;

  padding:15px;
}
</style>

<br>

<div class="hero-unit">

  <h1>Energy Stack Explorer</h1>
  <p style="margin-top:5px">Create a David MacKay <a href="http://www.withouthotair.com/" >Sustainable Energy Without the Hot Air</a> energy stack of your life. It can be used to compare the contribution of different uses of energy such as electricity use, heating,
  travel. Helping to put these different uses of energy in context.</p>

  <?php if ($exampledata) { ?>

  <div class="alert alert-info"><b>Example data:</b> The data below is example data to help get started, you can delete the items that dont apply to you, select new items from the add new item dropdown menu and edit the quantites as needed.</div>

  <?php } ?>
  <br>

  <div class="row-fluid" style="margin-bottom:10px">
    <div class="span8"><span style="font-size:24px; font-weight:bold;">Energy Items</span></div>
    <div class="span4" style="text-align:center"><span style="font-size:24px; ">Year 2013</span></div>
  </div>

  <div class="row-fluid">

    <div class="span8">
      <div id="energyitems"></div>

      <!-- Add item and save selector and buttons -->

      <div class="input-prepend input-append">
        <span class="add-on"><b>Add new item</b></span>

        <select id="additemselect" style="width:200px; margin:0px;">
          <?php while ($energytype = current($energytypes)) { ?>
          <option value="<?php echo key($energytypes); ?>"><?php echo $energytype['name']; ?></option>
          <?php next($energytypes); } ?>
        </select>

        <button id="additem" class="btn btn-info" type="button">Add</button>
      </div>

      <button id="save" class="btn btn-primary" style="float:right;">Save</button><div id="saved" style="padding-top:0px; color: #444; font-size:12px; float:right; margin-right:10px;">Saved</div>


    </div>

    <div class="span4" style="text-align:center">
      <canvas id="can" width="150" height="460" style="margin-left:20%"></canvas>
      <div style="clear:both"></div>
      <i style="font-size:12px; color:#444;">To embed in dashboard goto:<br> Dashboard > Edit > Widgets > stack</i>
    </div>

  </div>

</div>

<script type="application/javascript">
  var path = "<?php echo $path; ?>";
  var energytypes = <?php echo json_encode($energytypes); ?>;
  var energyitems = <?php echo json_encode($energyitems); ?>; 

  order_energyitems();

  for (z in energyitems)
  {
    energyitems[z]['data'] = JSON.parse(energyitems[z]['data'] || "null");
  }

  drawItems();

  var stacks = prepare_stack();
  draw_stacks(stacks,"can",300,460);

  $("#additem").click(function(){
    var tag = $("#additemselect").val();
    var item = {'tag':tag,'year':"2013",'data':{"quantity":0, "efficiency":100}}
    energyitems.push(item);
    order_energyitems();
    drawItems();

    var stacks = prepare_stack(energyitems,energytypes);
    draw_stacks(stacks,"can",300,460);

    $("#saved").html("");
  });

  $("#save").click(function(){
    $.ajax({                                      
      type: "GET",
      url: path+"energy/save.json",           
      data: "&data="+encodeURIComponent(JSON.stringify(energyitems)),
      success: function(msg) {if (msg=='"saved"') $("#saved").html("Saved");} 
    });
  });

  function drawItems()
  {
    var out = "";

    for (z in energyitems)
    {
      var id = z;
      var tag = energyitems[z]['tag'];
      var name = energytypes[tag]['name'];
      var units = energytypes[tag]['units'];
      var conv_kwh = energytypes[tag]['kwh'];

      var data = energyitems[z]['data'];
      var kwhd = 0;
      if (energytypes[tag]['procfn']==1) kwhd = calc_total(z);
      if (energytypes[tag]['procfn']==2) kwhd = calc_total(z);
      if (energytypes[tag]['procfn']==3) kwhd = calc_total_mpg(z);

      out += '<div class="itembox">';

      // Draw item name and kwh/d
      out += '<div style="margin-bottom:8px;">';        
      out += '<span style="font-size:20px;">'+name+'</span>';
      out += '<div style="float:right; margin-top:5px; margin-left:8px; cursor: pointer;" href="" class="removeitem" item="'+id+'"><i class="icon-trash"></i></div>'
      out += '<div style="font-size:20px; float:right; "><span id="kwhval'+id+'" >'+kwhd.toFixed(0)+'</span> kWh/d</div>';
      out += '</div>'
 

      var options = energytypes[tag]['options'];
      for (i in options)
      {
        if (data[i]==undefined) data[i] = options[i]['default'];
        out += '<div style="width:300px; float:left;"><b>'+options[i]['name']+':</b> <input class="option" tag="'+i+'" item="'+id+'" type="text" style="width:100px" value="'+data[i]+'"/ > <b>'+options[i]['units']+'</b></div>';

      }

      out += '<div style="clear:both"></div>';
      out += '</div>';
    }

    $("#energyitems").html(out);

    $(".option").keyup(function(){
      var tag = $(this).attr('tag');
      var id = $(this).attr('item');
      var val = $(this).val();
      energyitems[id]['data'][tag] = val;

      var tag = energyitems[id]['tag'];

      var kwhd = 0;
      if (energytypes[tag]['procfn']==1) kwhd = calc_total(id);
      if (energytypes[tag]['procfn']==2) kwhd = calc_total(id);
      if (energytypes[tag]['procfn']==3) kwhd = calc_total_mpg(id);
      $("#kwhval"+id).html(kwhd.toFixed(0));

      var stacks = prepare_stack(energyitems,energytypes);
      draw_stacks(stacks,"can",300,460);
      $("#saved").html("");
    });

    $(".removeitem").click(function(){
      var id = $(this).attr('item');
      
      $.ajax({                                      
        type: "GET",
        url: path+"energy/item/remove.json",           
        data: "&tag="+energyitems[id]['tag'],
        success: function(msg) {console.log(msg);} 
      });

      energyitems.splice(id,1); 

      drawItems();
      var stacks = prepare_stack(energyitems,energytypes);
      draw_stacks(stacks,"can",300,460);
    });
  }
 
</script>
