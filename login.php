<?php require_once("./includes/kernel.php"); ?>

<?php
  $redirect_page = isset($_GET["redirect_page"])?$_GET["redirect_page"]:"admin.php";
?>

<!doctype html>
<html lang="en">
  <head>
    <?php include("./includes/head.php"); ?>
    <title>Login to NSW Attendance</title>
    <script type="text/javascript">
      function togglePassword()
      {
        var passwordField = document.getElementById("passwordField");

        if (passwordField.type === "password")
        {
          passwordField.type = "text";
        }
        else
        {
          passwordField.type = "password";
        }
      }

      function doLogin()
      {
        document.getElementById("do-login-button").innerHTML = "Checking details...";

        var xhttp = new XMLHttpRequest();

        xhttp.open("POST", "./api/v1/do_login.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        var emailField    = document.getElementById("emailField").value;
        var passwordField = document.getElementById("passwordField").value;

        xhttp.send("email=" + emailField + "&password=" + passwordField);

        xhttp.onreadystatechange = function() {
          if (xhttp.status == 200) {
            const JSON_response = JSON.parse(this.responseText);

            if (JSON_response.status == "success") {
              document.getElementById("do-login-button").innerHTML = "Success!";
              document.cookie = 'session_ID='+JSON_response.session_ID+'; expires='+JSON_response.expiry+'; path=/';
              //window.location.href = "https://attendance.nsw.org.uk/<?=$redirect_page;?>";
            } 
            else {
              document.getElementById("do-login-button").innerHTML = "Log in";
              document.getElementById("do-login-error").innerHTML = "Error message: " + JSON_response.error_message;
            }
          }
        }
      }
    </script>
  </head>
  <body  class=" border-top-wide border-primary d-flex flex-column">
    <div class="page page-center">
      <div class="container-tight py-4">
        <div class="text-center mb-4">
          <a href="." class="navbar-brand navbar-brand-autodark"><img src="./static/logo-horizontal.png" height="36" alt=""></a>
        </div>
        <form class="card card-md" action="." method="get" autocomplete="off">
          <div class="card-body">
            <h2 class="card-title text-center mb-4">Login to your account</h2>
            <div class="mb-3">
              <label class="form-label">Email address</label>
              <input type="email" class="form-control" id="emailField" placeholder="Enter email" tabindex="1">
            </div>
            <div class="mb-2">
              <label class="form-label">
                Password
                <span class="form-label-description">
                  <a href="./forgot-password.php" tabindex="5">I forgot password</a>
                </span>
              </label>
              <div class="input-group input-group-flat">
                <input type="password" class="form-control" id="passwordField" placeholder="Password"  autocomplete="off"  tabindex="2">
                <span class="input-group-text">
                  <a href="#" class="link-secondary" title="Show password" data-bs-toggle="tooltip" onclick="togglePassword()" tabindex="3"><!-- Download SVG icon from http://tabler-icons.io/i/eye -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="2" /><path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" /></svg>
                  </a>
                </span>
              </div>
            </div>
            <div class="mb-2" id="do-login-error">
              
            </div>
            <div class="form-footer">
              <button id="do-login-button" onclick="doLogin()" class="btn btn-primary w-100" tabindex="4">Log in</button>
            </div>
          </div>
        </form>
        <div class="d-flex py-1 align-items-center">
          <a class="btn btn-link" onclick="history.back()">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <line x1="5" y1="12" x2="19" y2="12" />
              <line x1="5" y1="12" x2="11" y2="18" />
              <line x1="5" y1="12" x2="11" y2="6" />
            </svg>
            Take me back
          </a>
        </div>
      </div>
    </div>
    <!-- Libs JS -->
    <!-- Tabler Core -->
    <script src="./dist/js/tabler.min.js"></script>
    <script src="./dist/js/demo.min.js"></script>
  </body>
</html>