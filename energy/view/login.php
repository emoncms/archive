<?php
    global $path, $session;
    $apikey = $session['apikey_read'];
?>

<style>

input[type=text] {
  margin-bottom:20px;
}

input[type=password] {
  margin-bottom:20px;
}

.register-elements {
  display:none;
}

</style>

<br><br><br>

<div id="login-block" style="text-align:center">
  <div class="login-box">
  
  <div class="login-elements">
    <h2>Login</h2>
    <p>Login with your <b>emoncms.org</b> account</p>
  </div>
  
  <div class="register-elements">
    <h2>Register</h2>
    <p>Create a new <b>emoncms.org</b> account</p>
  </div>
  
    <input id="username" type="text" placeholder="Username...">
    <input id="email" type="text" placeholder="Email..." class="register-elements">
    <input id="password" type="password" placeholder="Password...">
    <input id="password-confirm" type="password" placeholder="Confirm Password..." class="register-elements">
    
    <div class="login-elements">
      <button id="login" class="btn">Login</button> or <span id="register-open">register</span>
    </div>
    
    <div class="register-elements">
      <button id="register" class="btn">Register</button> or <span id="register-cancel">cancel</span>
    </div>
    
  <div id="alert"></div>
  </div>
</div>

<div id="welcome-block" style="display:none">
  <h2>Welcome</h2>
  <div id="emailout"></div><br>
</div>

<script>
var path = "<?php echo $path; ?>";
var session = JSON.parse('<?php echo json_encode($session); ?>');

if (!session) {

} else {
    $("#login-block").hide();
    $("#welcome-block").show();
    $("#emailout").html(session.email);
    $(".sidenav").show();
}

$("#login").click(function() {
    var username = $("#username").val();
    var password = $("#password").val();

    $.ajax({                                      
        url: path+"/login",                         
        data: "username="+username+"&password="+password,
        dataType: 'json',
        success: function(result) {
            console.log(result);
            if (result.userid!=undefined) {
                window.location = "";
            } else {
                $("#alert").html("<i class='icon-alert'></i>"+result);
            }
        }
    });
});

$("#register-open").click(function() {
    $(".login-elements").hide();
    $(".register-elements").show();
    $("#alert").html("");
});

$("#register-cancel").click(function() {
    $(".register-elements").hide();
    $(".login-elements").show();
    $("#alert").html("");
});

$("#register").click(function() {
    var username = $("#username").val();
    var email = $("#email").val();
    var password = $("#password").val();
    var passwordconfirm = $("#password-confirm").val();

    if (password=="" || passwordconfirm=="") {
        $("#alert").html("<i class='icon-alert'></i>Passwords must be at least<br> 4 characters long");
        return false;
    }
        
    if (password!=passwordconfirm) {
        $("#alert").html("<i class='icon-alert'></i>Passwords do not match");
        return false;
    }
    
    $.ajax({                                      
        url: path+"/register",                         
        data: "username="+username+"&email="+email+"&password="+password,
        dataType: 'json',
        success: function(result) {
            console.log(result);
            if (result.userid!=undefined) {
                window.location = "";
            } else {
                $("#alert").html("<i class='icon-alert'></i>"+result);
            }
        }
    });
});

</script>
