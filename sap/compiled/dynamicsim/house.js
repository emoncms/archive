function draw_house(ctx,x,y)
{


  var width = 350;
  var height = 290;

  var wallwidth = 30;
  var floorwidth = wallwidth;

  var roofwidth = 30;

  ctx.beginPath();

  ctx.moveTo(x-0,y-0);

  x += width; ctx.lineTo(x,y);
  y -= height; ctx.lineTo(x,y);

  x -= width/2;
  y -= Math.tan(2*Math.PI*((35)/360))*(width/2);
  ctx.lineTo(x,y);

  x -= width/2;
  y -= Math.tan(2*Math.PI*((-35)/360))*(width/2);
  ctx.lineTo(x,y);

  var corner_x = wallwidth * 2;
  var corner_y = 10;
  x -= corner_x; ctx.lineTo(x,y);
  y -= corner_y; ctx.lineTo(x,y);

  x += (width/2)+corner_x;
  y -= Math.tan(2*Math.PI*((35)/360))*((width/2)+corner_x);
  ctx.lineTo(x,y);

  x += (width/2)+corner_x;
  y -= Math.tan(2*Math.PI*((-35)/360))*((width/2)+corner_x);
  ctx.lineTo(x,y);

  y += corner_y; ctx.lineTo(x,y);
  x -= wallwidth; ctx.lineTo(x,y);

  y += height+floorwidth; ctx.lineTo(x,y);
  x -= width+(wallwidth*2); ctx.lineTo(x,y);
  y -= floorwidth; ctx.lineTo(x,y);

  ctx.closePath();

  ctx.fillStyle = "#c8bc8c";
  ctx.fill();

  ctx.strokeStyle = "#000";
  ctx.lineWidth = 2;
  ctx.stroke();

  // Glass

  ctx.beginPath();

  x += 5;
  ctx.moveTo(x,y);
  y -= height; ctx.lineTo(x,y);

  x -= 5;
  ctx.moveTo(x,y);
  y += height; ctx.lineTo(x,y);

  ctx.closePath();

  ctx.strokeStyle = "#444";
  ctx.lineWidth = 1;
  ctx.stroke();

  // Thermal mass

  ctx.beginPath();
  x += 5; y -= 1;
  ctx.moveTo(x,y);
  y -= 20; ctx.lineTo(x,y);
  x += width+wallwidth-6; ctx.lineTo(x,y);
  y += 20; ctx.lineTo(x,y);
  ctx.closePath();

  ctx.fillStyle = "#f69844";
  ctx.fill();
}
