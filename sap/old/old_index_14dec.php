<!-----------------------------------------------------------------------------

  All SAP Emoncms module code is released under the GNU Affero General Public License.
  See COPYRIGHT.txt and LICENSE.txt.

  ---------------------------------------------------------------------
  Part of the OpenEnergyMonitor project:
  http://openenergymonitor.org
 
------------------------------------------------------------------------------->

<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Bootstrap 101 Template</title>
    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <script type="text/javascript" src="jquery.min.js"></script>
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }

      p {
        color:#222;
        font-style:italic;
      }
 
    </style>
  </head>
  <body>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#">Project name</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li class="active"><a href="#">Home</a></li>
              <li><a href="#about">About</a></li>
              <li><a href="#contact">Contact</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

<style>
  input {
    width:80px;
  }
</style>

    <div class="container">

      <h1>SAP Worksheet 2012</h1>

<?php
$myFile = "desc.txt";
$fh = fopen($myFile, 'r');

$equations = array();

$br = 0;

$out = '';
$eqs = "function calculate(data) { \n";

while(!feof($fh))
{
  $line = fgets($fh);
  $inline = trim($line);
  $line = trim($line);

  // Headings
  if ($line[0] == '#' && !$table) {$line = substr($line,1); $out .= "<h3>".$line."</h3>\n";}
  if ($line[0] == 'p' && !$table) {$line = substr($line,1); $out .= "<p>".$line."</p>\n";}

  if ($line[0] == 't' && $line[2]=='1')  {$br = 1;}
  if ($line[0] == 't' && $line[2]=='0')  {$br = 0;}

  if ($line[0] == '$') {
    $line = preg_replace('/[^\w\s=+÷.-]/','|',$line);
    $items = explode('|', $line);

    // If result of equation has m for monthly as the precending character then equation is of monthly type
    if ($items[2][0]=='m')
    {
      $item = substr($items[2],1);
      $eqs .= "for (var i=1; i<13; i++) { data['".$item."-'+i] =";


      for ($i=4; $i<count($items)-1; $i++)
      {
        $item = $items[$i];
        if ($item[0]=='m')
        {
          $item = substr($items[$i],1);
          $item = "data['".$item."-'+i]";
        }
        elseif ($item[0]=='v') $item = substr($item,1);
        elseif ($item=='x') $item = '*';
        elseif ($item=='÷') $item = '/';
        elseif ($item=='+') $item = '+';
        elseif ($item=='-') $item = '-';
        else { $item = "data['".$item."']"; }
        $eqs .= ' '.$item;
      }

      $eqs .= "; }\n";

      // Create an equation for each month
      for ($m=1; $m<13; $m++)
      {
        $result = $items[2];
        $result = substr($result,1);
        $result = $result.'-'.$m;
        //$eqs .= "data['".$result."'] =";

        $equation_items = array();
        for ($i=4; $i<count($items)-1; $i++)
        {
          $item = $items[$i];
          if ($items[$i][0]=='m')
          {
            $item = substr($items[$i],1);
            $item = $item.'-'.$m;
          }
          $equation_items[] = $item;

          if ($item[0]=='v') $item = substr($item,1);
          elseif ($item=='x') $item = '*';
          elseif ($item=='÷') $item = '/';
          elseif ($item=='+') $item = '+';
          elseif ($item=='-') $item = '-';
          else { $item = "data['".$item."']"; }
          //$eqs .= ' '.$item;
        }
        $equations[] = array('result'=>$result,'items'=>$equation_items);
        //$eqs .= ";\n";
      }
    }
    else
    {
      $eqs .= "data['".$items[2]."'] =";
      $equation_items = array();
      for ($i=4; $i<count($items)-1; $i++)
      {
        $equation_items[] = $items[$i];

          $item = $items[$i];
          if ($item[0]=='v') $item = substr($item,1);
          elseif ($item=='x') $item = '*';
          elseif ($item=='÷') $item = '/';
          elseif ($item=='+') $item = '+';
          elseif ($item=='-') $item = '-';
          else { $item = "data['".$item."']"; }
          $eqs .= ' '.$item;
      }
      $equations[] = array('result'=>$items[2],'items'=>$equation_items);
      $eqs .= ";\n";
    }
  }

  if ($line[0] == '>') {$table = false; $out .= "</table>\n";}

  if ($table) 
  {
    $items = explode('|', $line);
    $out .= "  <tr>\n";
    foreach ($items as $td)
    {
      if ($td[0]=='(') 
      {
        $id = getid($td);
        $out .= "    <td style='padding:8px 6px 0px 6px; width:60px'><input style='width:60px' id='".$id."' type='text' placeholder='".$id."' value='0' /></td>\n";
      }
      else
      {
        $out .= "    <td>".$td."</td>\n";
      }
    }
    $out .= "  </tr>\n";
  }

  if ($line[0] == '<') {$table = true; $out .= "<table class='table table-bordered'>\n";}

  // Parse monthly table automatically
  if ($line[0] == 'm' && !$table)
  {
    $tag = substr($line, 3,-1);
    $d='-';
    $out .= "<table class='table table-bordered'>\n  <tr>\n    <td style='padding-top:13px'>(".$tag.")m</td>\n";
    for ($i=1; $i<13; $i++)
    {
      $id = $tag.$d.$i;
      $out .= "    <td style='padding:8px 0px 0px 6px'><input style='width:45px;' id='".$id."' type='text' placeholder='".$id."' value='0' /></td>\n";
    }
    $out .= "  </tr>\n</table>\n";
  }
}

fclose($fh);

function getid($str)
{
  $id = '';
  for ($i=1; $i<strlen($str)-1; $i++)
  {
    $id .= $str[$i];
  }
  return $id;
}

echo $out;

// Export to html file
$outfile = "form.txt";
$out_fh = fopen($outfile, 'w');
fwrite($out_fh, $out);
fclose($out_fh);

// Export to html file
$outfile = "equations.txt";
$out_fh = fopen($outfile, 'w');
fwrite($out_fh, "var equation_list = ".json_encode($equations)."\n");
fclose($out_fh);

// Export to html file
$outfile = "eqs.js";
$out_fh = fopen($outfile, 'w');

$eqs .= "return data; \n";
$eqs .= '}';
fwrite($out_fh, $eqs);
fclose($out_fh);
?>

<script>

var equation_list = <?php echo json_encode($equations); ?>;
console.log(equation_list);

$('input').keyup(function()
{

for (z in equation_list)
{
  var equation = equation_list[z];
  var len = equation['items'].length;

    var value = 0;
    if (equation['items'][0][0] == 'v')
    {
      var str = equation['items'][0];
      value = parseFloat(str.replace('v',''));
    }
    else
    {
      value = parseFloat($("#"+equation['items'][0]).val());
    }
    if (isNaN(value)) value = 0;

  var result = value;

  for (var i=1; i<len; i=i+2)
  {
    var operator = equation['items'][i];

    // detect if value is input element id or fixed value denoted by v character
    var value = 0;
    if (equation['items'][i+1][0] == 'v')
    {
      var str = equation['items'][i+1];
      value = parseFloat(str.replace('v',''));
    }
    else
    {
      value = parseFloat($("#"+equation['items'][i+1]).val());
    }
    if (isNaN(value)) value = 0;

    if (operator == '+') result = result + value;
    if (operator == '-') result = result - value;
    if (operator == 'x') result = result * value;
    if (operator == '÷') result = result / value;
  }
  
  $("#"+equation['result']).val(result);
}

});
</script>

</div>

</body>
</html>
