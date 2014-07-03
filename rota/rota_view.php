<?php

  /*

    All Emoncms code is released under the GNU Affero General Public License.
    See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org

  */

  global $path; 
?>

<h2>Rota</h2>
<p>Insert rota csv file in textbox to add rota</p>
<textarea id='csv' cols="50" rows="10"></textarea><br>
<input id="savecsv" type="button" value="Save" />

<script>

  var path = "<?php echo $path; ?>";

  $("#savecsv").click(function(){

    var csv = $("#csv").val();
    console.log(csv);

    var result = {};
    $.ajax({type:'POST', url: path+"rota/parsecsv.json", data: "csv="+csv, dataType: 'json', async: false, success: function(data){}});

  });

</script>

