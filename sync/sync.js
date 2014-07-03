
var sync = {

  'get_remote_feeds':function()
  {
    var result = {};
    $.ajax({ url: path+"sync/getremotefeeds.json", dataType: 'json', async: false, success: function(data) {result = data;} });
    return result;
  },

  'get_local_feeds':function()
  {
    var result = {};
    $.ajax({ url: path+"sync/getlocalfeeds.json", dataType: 'json', async: false, success: function(data) {result = data;} });
    return result;
  },

  'get_importqueue':function()
  {
    var result = {};
    $.ajax({ url: path+"sync/getimportqueue.json", dataType: 'json', async: false, success: function(data) {result = data;} });
    return result;
  },


  'feed':function(feedid, feedname,datatype)
  {
    var result = {};
    $.ajax({ url: path+"sync/feed.json", data: "feedid="+feedid+"&name="+feedname+"&datatype="+datatype, async: false, success: function(data){} });
    return result;
  },

  'setsettings':function(remoteurl, remotekey)
  {
    var result = {};
    $.ajax({ url: path+"sync/setsettings.json", data: "remoteurl="+remoteurl+"&remotekey="+remotekey, async: false, dataType: 'json', success: function(data){result = data;} });
    return result;
  },

  'getsettings':function()
  {
    var result = {};
    $.ajax({ url: path+"sync/getsettings.json", async: false, dataType: 'json', success: function(data){result = data;} });
    return result;
  }

}

