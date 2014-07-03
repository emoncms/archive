<?php global $path; ?>

<script type="text/javascript" src="<?php echo $path; ?>/Modules/sap/equations.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>/Modules/sap/solar.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>/Modules/sap/windowgains.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>/Modules/sap/utilisationfactor.js"></script>
<style>

  input {
    width:80px;
  }

  .tag
  {
    font-size:12px; 
    color:#888; 
    padding:6px 2px 0px 2px; 
    float:right;
  }

.table tbody tr:hover td,
.table tbody tr:hover th {
    background-color: inherit;
}
</style>


<?php if ($page==1) { ?>
<div class="hero-unit">
  <div class="container">
    <div class="wall">
      <div class="span6">
        <h1>Open SAP 2012</h1>
        <p>This is an open source implementation of the 2012 SAP worksheet for calculating energy performance and heating demand of buildings.</p>
        <p>Early beta version, errors expected, help us with development <a href="http://github.com/emoncms/sap" >here</a></p>
      </div>
      <div class="span5">
        <div style="float:right;">
        <img src="<?php echo $path; ?>/Modules/sap/graph.png" style="width:500px"/>
        </div>
      </div>
    </div>
  </div>
</div>



<?php } ?>


<div class="container">

<?php if ($example==true && $page==1) { ?>
  <div class="alert alert-info"><b>Example data:</b> The following example data is for a small single-story old welsh stone cottage.</div>
<?php } ?>

  <div class="row-fluid">
    <div class="span3">
      <h3>SAP 2012</h3>

<?php if ($datafromsession==true) { ?>
  <div class="alert alert-info"><b>Editing as guest!</b><br><a href="<?php echo $path; ?>user/login" >Login or create an account</a> to save your data permanently. Once logged in navigate to Extras > SAP to complete saving process.</div>
<?php } ?>
        <ul class="nav nav-tabs nav-stacked" style="padding-left: 0px;">
          <li><a href="<?php echo $path; ?>sap/1" >1. Overall dwelling dimensions</a></li>
          <li><a href="<?php echo $path; ?>sap/2">2. Ventilation rate</a></li>
          <li><a href="<?php echo $path; ?>sap/3">3. Heat losses and heat loss parameter</a></li>
          <li><a href="<?php echo $path; ?>sap/4">4. Water heating energy requirement</a></li>
          <li><a href="<?php echo $path; ?>sap/5">5. Internal gains</a></li>
          <li><a href="<?php echo $path; ?>sap/6">6. Solar gains</a></li>
          <li><a href="<?php echo $path; ?>sap/7">7. Mean internal temperature</a></li>
          <li><a href="<?php echo $path; ?>sap/8">8. Space heating & cooling</a></li>
          <li><a href="<?php echo $path; ?>sap/9">9. Energy requirements</a></li>
          <li><a href="<?php echo $path; ?>sap/10">10. Fuel costs</a></li>
          <li><a href="<?php echo $path; ?>sap/11">11. SAP rating</a></li>
          <li><a href="<?php echo $path; ?>sap/graph">Graph</a></li>
          <li><a href="<?php echo $path; ?>sap/H1">Appendix H1. Solar input for solar water heating</a></li>
          <li><a href="<?php echo $path; ?>sap/AppendixL">Appendix L. Lighting and Appliances</a></li>
          <li><a href="<?php echo $path; ?>sap/dynamicsim"><b>Simple Dynamic Simulation</b></a></li>
        </ul>
       <h3>Tutorials</h3>
        <ul class="nav nav-tabs nav-stacked" style="padding-left: 0px;">
          <li><a href="<?php echo $path; ?>sap/introduction" >Introduction</a></li>
          <li><a href="<?php echo $path; ?>sap/fabricheatloss" >Building fabric conduction</a></li>
          <li><a href="<?php echo $path; ?>sap/airchange" >Ventilation and Infiltration</a></li>
    </div>
    <div class="span9">
      <?php require "compiled/".$page.".html"; ?>
    </div>
  </div>
</div>

<script>

  var path = "<?php echo $path; ?>";
  var data = <?php echo $data; ?>;

  console.log(data);

  if (data==0)
  {
    console.log("No data, using example data");
    data = {'region':0, 'solarcyl':false, 'solardhw':false};
    
    $('input').each(function()
    {
      var id = $(this).attr('class');
      if (id) data[id] = $(this).val()*1;
    });
  }
  else
  {
    $('input').each(function()
    {
      var id = $(this).attr('class');
      if (id && data[id]==undefined) data[id] = $(this).val()*1;
    });

    // calculate solar gains from windows
    var gains = calc_solar_gains_from_windows(data['window'],data['H5a']);
    // copy over the calc results into the worksheet cells
    for (var z=1; z<13; z++) data['83-'+z] = gains[z-1];

    data = calculate(data);
    for (z in data) {if (z) $('.'+z).val(data[z]);}

  if (data['solardhw']) $("#solardhw-checkbox").attr('checked','checked');
  if (data['solarcyl']) $("#solarcyl-checkbox").attr('checked','checked');
  }

  if (data['heatlossitems']==undefined) data['heatlossitems'] = [];
  if (data['window']==undefined) data['window'] = [];

  $('input').change(function()
  {
    var id = $(this).attr('class');
    if (id)
    {
      data[id] = $(this).val()*1;
      var last = JSON.parse(JSON.stringify(data));
      data = calculate(data);
      for (z in data)
      {
        if (z!=id && last[z]!=data[z]) { $("."+z).val(data[z]); $("."+z).attr('readonly', 'readonly');}
      }
      save();
    }
  });

  $("#ventilationtype-button").click(function(){
    var type = $("#ventilationtype-select").val();

    if (type==0) for (var i=1; i<13; i++) data['25-'+i] = data['24a-'+i];
    if (type==1) for (var i=1; i<13; i++) data['25-'+i] = data['24b-'+i];
    if (type==2) for (var i=1; i<13; i++) data['25-'+i] = data['24c-'+i];
    if (type==3) for (var i=1; i<13; i++) data['25-'+i] = data['24d-'+i];

    data = calculate(data);

    for (z in data) $("."+z).val(data[z]);

    save();
  });

  $("#solardhw-checkbox").click(function(){
    data['solardhw'] = $(this)[0].checked;
    var last = JSON.parse(JSON.stringify(data));
    data = calculate(data);
    for (z in data)
    {
      if (last[z]!=data[z]) { $("."+z).val(data[z]); $("."+z).attr('readonly', 'readonly');}
    }
    save();
  });

  $("#solarcyl-checkbox").click(function(){
    data['solarcyl'] = $(this)[0].checked;
    var last = JSON.parse(JSON.stringify(data));
    data = calculate(data);
    for (z in data)
    {
      if (last[z]!=data[z]) { $("."+z).val(data[z]); $("."+z).attr('readonly', 'readonly');}
    }
    save();
  });

  function save()
  {
    $.ajax({                                      
      type: "POST",
      url: path+"sap/save.json",           
      data: "&data="+encodeURIComponent(JSON.stringify(data)),
      success: function(msg) {console.log(msg);} 
    });
  }

</script>

