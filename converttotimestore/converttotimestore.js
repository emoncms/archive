
var converttotimestore = {

  'get':function()
  {
    var result = {};
    $.ajax({ url: path+"converttotimestore/get.json", dataType: 'json', async: false, success: function(data) {result = data;} });
    return result;
  },
  
  'set':function(id,converttime)
  {
    var result = {};
    $.ajax({ url: path+"converttotimestore/set.json", data: "feedid="+id+"&convert="+converttime, async: false, success: function(data){} });
    return result;
  },
  
  
  'scan':function()
  {
    var result = {};
    $.ajax({ url: path+"converttotimestore/scan.json", data: "", async: true, success: function(data){} });
    return result;
  }

}

