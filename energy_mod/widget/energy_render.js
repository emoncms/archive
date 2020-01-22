function energy_widgetlist()
{
  var widgets = {
    "stack": 
    {
      "offsetx":-50,"offsety":-200,"width":100,"height":440,
      "menu":"Widgets"
    }
  }
  return widgets;
}

function energy_init()
{
  setup_widget_canvas('stack');
  energy_draw();
}

function energy_draw()
{
  $('.stack').each(function(index) {
    var id = "can-"+$(this).attr("id");
    draw_stacks(stacks,id,300,460);
  });
}

function energy_slowupdate()
{
}

function energy_fastupdate()
{
}


