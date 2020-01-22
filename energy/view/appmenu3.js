var appmenu = {
  "myelectric": {
    "name": "My Electric",
    "feeds":["use","use_kwh"]
  },
  "myheatpump": {
    "name": "My Heatpump",
    "feeds":["heatpump_elec_kwh","heatpump_heat","heatpump_heat_kwh","heatpump_flowT","heatpump_returnT"]
  },
  "myopenevse": {
    "name": "My OpenEVSE",
    "feeds":["OpenEVSE_POWER","OpenEVSE_KWH"]
  },
}

if (window.feeds==undefined) feeds = feed.listbyname();

load_menu();
setInterval(load_menu,10000);

function load_menu() {
    var out = "";
    for (var z in appmenu) {

        var valid = true;
        for (var x in appmenu[z].feeds) {
            if (feeds[appmenu[z].feeds[x]]==undefined) valid = false;
        }

        if (valid) out += "<a href='"+path+"app/"+z+"'>"+appmenu[z].name+"</a>";
    }
    if (out!="") out = "<br><b>Dashboards</b><br>"+out;

    $("#appmenu").html(out);

}
