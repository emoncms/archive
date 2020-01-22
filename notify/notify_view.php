<?php global $path; ?>
<script type="text/javascript" src="<?php echo $path; ?>Modules/notify/notify.js"></script>

<br>

<h2><?php echo _('Notify me on inactive feeds'); ?></h2>
<p><?php echo _('Receive an email notification when any feed become inactive for more than 2 hours'); ?></p>

<div class="input-prepend input-append">
  <span class="add-on"><?php echo _('Send email to:'); ?></span>
  <input id="email" type="text" />
  <span id="enabledtag" class="add-on" style="display:none"><?php echo _('Enabled!'); ?></span>
  <button id="notifybtn" class="btn btn-primary" type="button"><?php echo _('Enable'); ?></button>
</div>

<p><i><?php echo _('The server checks for inactive feeds every 2 hours.<br>Feeds that are historically inactive are ignored.'); ?></i></p>

<script>
  var path = "<?php echo $path; ?>";
  var result = notify.status();

  var enabled = result.enabled*1;
  console.log("Enabled: "+enabled);
  
  var email = result.email;
  
  update();
      
  // Enable / disable button
  $("#notifybtn").click(function(){
    enabled = !enabled;
    console.log("<?php echo _('Enabled: '); ?>"+enabled);
    
    
    if (enabled) {
      var lastemail = email;
      email = $("#email").val();
      console.log("<?php echo _('Sending email to: '); ?>"+email);
      var result = notify.enable(email);
      if (result.success == false) {
        alert('<?php echo _('ERROR: Please enter a valid email address'); ?>');
        email = lastemail;
        enabled = false;
        update();
      }
    } else {
      notify.disable();
    }
        
    update();
  });
  
  function update()
  {
    if (enabled) {
      $("#notifybtn").removeClass("btn-primary");
      $("#notifybtn").html("<?php echo _('Disable'); ?>");
      $("#notifybtn").addClass("btn-danger");
      $("#enabledtag").show();
    } else {
      $("#notifybtn").removeClass("btn-danger");
      $("#notifybtn").html("<?php echo _('Enable'); ?>");
      $("#notifybtn").addClass("btn-primary");
      $("#enabledtag").hide();
    }
    $("#email").val(email);
  }

</script>

