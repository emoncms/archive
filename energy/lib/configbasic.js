function check_config() {
    // Check configuration
    var valid = true;
    var out = "<b>ERROR:</b> Configuration not valid<br><br>";
    
    for (var z in config) {
        if (config[z].type=="feed") {
            if (feeds[config[z].autoname]==undefined) {
                if (config[z].optional!=undefined && config[z].optional) {
                    // still valid
                } else {
                    valid = false;
                    out += "- <b>Missing feed:</b> "+config[z].autoname+"<br>";
                }
            }
        }
    }

    if (!valid) {
        $("#config-error").html(out);
        $("#config-error").show();
        $("#dashboard").hide();
    }
    
    return valid;
}
