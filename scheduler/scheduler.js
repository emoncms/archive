
var scheduler = {

  apikey: false,

  'get':function()
  {
    var apikey_str = ""; if (this.apikey) apikey_str = "?apikey="+this.apikey;
    var result = {};
    $.ajax({ url: path+"scheduler/get.json"+apikey_str, dataType: 'json', async: false, success: function(data) {result = data;} });
    return result;
  },

  'set':function(schedule)
  {
    var apikey_str = ""; if (this.apikey) apikey_str = "?apikey="+this.apikey;
    var result = {};
    $.ajax({type:'POST', url: path+"scheduler/set.json"+apikey_str, data: "schedule="+JSON.stringify(schedule), async: true, success: function(data){} });
    //return result;
  }
}
