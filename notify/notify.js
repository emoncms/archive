
var notify = {

  'status':function()
  {
    var result = {};
    $.ajax({ url: path+"notify/status.json", dataType: 'json', async: false, success: function(data) {result = data; } });
    return result;
  },

  'enable':function(email)
  {
    var result = {};
    $.ajax({ url: path+"notify/enable.json", data: "&email="+email ,dataType: 'json', async: false, success: function(data) {result = data;} });
    return result;
  },
  
  'disable':function()
  {
    var result = {};
    $.ajax({ url: path+"notify/disable.json" ,dataType: 'json', async: false, success: function(data) {result = data;} });
    return result;
  }

}

