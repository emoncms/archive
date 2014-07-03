  function draw_arrow(ctx,tail,head,tailwidth,headwidth,headlength)
  {
    var tailheadvector = subtract(tail, head)
    var tailheadunitvector = unitvector(tailheadvector);

    var arrowback = scale(tailheadvector,headlength);

    ctx.beginPath();

    // tail side A
    ctx.moveTo(tail.x+tailheadunitvector.y*tailwidth,tail.y-tailheadunitvector.x*tailwidth);

    // tail side B
    ctx.lineTo(tail.x-tailheadunitvector.y*tailwidth,tail.y+tailheadunitvector.x*tailwidth);

    // head side B
    ctx.lineTo(tail.x+arrowback.x-tailheadunitvector.y*tailwidth,tail.y+arrowback.y+tailheadunitvector.x*tailwidth);
    ctx.lineTo(tail.x+arrowback.x-tailheadunitvector.y*headwidth,tail.y+arrowback.y+tailheadunitvector.x*headwidth);

    // Arrow head
    ctx.lineTo(head.x,head.y);

    // head side A
    ctx.lineTo(tail.x+arrowback.x+tailheadunitvector.y*headwidth,tail.y+arrowback.y-tailheadunitvector.x*headwidth);
    ctx.lineTo(tail.x+arrowback.x+tailheadunitvector.y*tailwidth,tail.y+arrowback.y-tailheadunitvector.x*tailwidth);

    ctx.closePath();
    ctx.fill();
    ctx.stroke();
  }
