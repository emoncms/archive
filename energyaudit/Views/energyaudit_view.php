<!---------------------------------------------------------------------------------
   All Emoncms code is released under the GNU Affero General Public License.
   See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org
---------------------------------------------------------------------------------->

<style>

.inpsel {
width:70px;
}

.top_menu2
{
  background-color:#eee;
}

.top_menu2 ul
{
  margin:0;
  padding: 0;
  list-style-type: none;
}

.top_menu2 ul li 
{
  float: left;
  width: 33.33%;
}

.top_menu2 ul li a {
  font-size: 13px;
  text-align: center;

  font-weight: bold;
  color: #fff;

  display: block;
  text-decoration: none;
  line-height: 35px;
  margin: 0;
  text-align: center;
  border-left: 2px solid #eee;
  background-color: #aaa;
}

.top_menu2 ul li a:hover { color: #fff; cursor:pointer; background-color: #888;}
.top_menu2 li:hover ul, li.over ul { display: block;  background-color:#fff;}
</style>

          <div style="margin: 0px auto; max-width: 990px; text-align:left; margin-top:20px;">

<div class='widget-container' style="width:540px; min-height:550px; margin-bottom: 20px; float:left; position:relative;">

  <div class='top_menu2'>
    <ul>
      <li><a href='../energyaudit/electric?lang=en'>Electric</a></li>
      <li><a href='../energyaudit/heating?lang=en'>Heating</a></li>
      <li><a href='../energyaudit/transport?lang=en'>Transport</a></li>
    </ul>
    <div style='clear:both;'></div>
  </div>

  <?php echo $left; ?>
  <div style="position:absolute; bottom:20px; right:20px; width:200px; text-align:right;">
  <i><span id="save_label" style="padding-right:10px; color:#666;"></span></i><button type='button' id='save' class='button05' style="width:80px; line-height:30px;" >Save</button>
  </div>
</div>

<div class='widget-container' style="width:300px; height:550px; margin-left: 15px; float:left;">
  <h2>Energy Stacks</h2>
  <?php echo $right; ?>
</div>

</div>
