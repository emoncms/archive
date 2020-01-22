
  function prepare_stack()
  {
    var stacks = [];
    stacks[0] = {'name':"Average", 'stack':[]};
    var total_kwhd = 0;

    var i = 0;
    for (z in energyitems)
    {
      var tag = energyitems[z]['tag'];
      var name = energytypes[tag]['name'];

      var kwhd = 0;
      if (energytypes[tag]['procfn']==1) kwhd = calc_total(z);
      if (energytypes[tag]['procfn']==2) kwhd = calc_use(z);
      if (energytypes[tag]['procfn']==3) kwhd = calc_total_mpg(z);

      var carbon = energytypes[tag]['carbon'];
      var color = 0; if (carbon>0) color = 0; else color = 1;
      stacks[0]['stack'][i] = {'kwhd':kwhd, 'color':color, 'name':name }; total_kwhd += kwhd; i++;
    }

    for (z in energyitems)
    {
      var tag = energyitems[z]['tag'];
      var name = energytypes[tag]['name'];
      var kwhd = 0; if (energytypes[tag]['procfn']==2) kwhd = calc_loss(z);
      var carbon = energytypes[tag]['carbon'];
      var color = 0; if (carbon>0) color = 2; else color = 3;
      stacks[0]['stack'][i] = {'kwhd':kwhd, 'color':color, 'name':name }; total_kwhd += kwhd; i++;
    }

    stacks[0]['height'] = total_kwhd;

    return stacks;
  }

    function calc_total(id)
    {
      var tag = energyitems[id]['tag'];
      var conv_kwh = energytypes[tag]['kwh'];
      var kwhd = (energyitems[id]['data']['quantity'] * conv_kwh) / 365.0;
      return kwhd;
    }

    function calc_use(id)
    {
      var tag = energyitems[id]['tag'];
      var conv_kwh = energytypes[tag]['kwh'];
      var data = energyitems[z]['data'];
      var kwhd = (data['efficiency']/100) * (data['quantity'] * conv_kwh) / 365.0;
      return kwhd;
    }

    function calc_loss(id)
    {
      var tag = energyitems[id]['tag'];
      var conv_kwh = energytypes[tag]['kwh'];
      var data = energyitems[z]['data'];
      var kwhd = ((100-data['efficiency'])/100) * (data['quantity'] * conv_kwh) / 365.0;
      if (100-data['efficiency']<0) kwhd = 0;
      return kwhd;
    }

    function calc_total_mpg(id)
    {
      // 4.55 Litre's per imperial gallon
      // 10 kWh per litre = 45.5 kWh/gallon
      var tag = energyitems[id]['tag'];
      var kwhd = ((energyitems[id]['data']['miles'] / energyitems[id]['data']['mpg']) * energytypes[tag]['kwh']) / 365.0;
      return kwhd;
    }


function order_energyitems() {

  for(x = 0; x < energyitems.length; x++) {
    for(y = 0; y < (energyitems.length-1); y++) {
      var tagA = energyitems[y]['tag'];
      var tagB = energyitems[y+1]['tag'];
      if(energytypes[tagA]['order'] > energytypes[tagB]['order']) {
        holder = energyitems[y+1];
        energyitems[y+1] = energyitems[y];
        energyitems[y] = holder;
      }
    }
  }
}

