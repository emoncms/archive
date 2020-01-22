<br><br><br>

<div id="welcome-block">
  <h2>Welcome</h2>
  <b>User:</b> <span id="emailout"></span><br><br>
  <b>Apikey:</b> <span id="apikey_write"></span><br>
</div>

<script>
    $("#emailout").html(session.username);
    $("#apikey_write").html(session.apikey_write);
    $(".sidenav").show();
</script>
