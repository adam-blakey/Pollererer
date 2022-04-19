function addLogin()
{
  document.getElementById("add-login-result").style = "visibility: block;"
  document.getElementById("add-login-button-text").innerHTML = "Please wait.";
  document.getElementById("add-login-result-icon").innerHTML = '<span class="spinner-border spinner-border-sm me-2 text-blue" role="status"></span>';
  document.getElementById("add-login-button-icon").innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>';
  document.getElementById("add-login-result-title").innerHTML = "Loading...";
  document.getElementById("add-login-result-text").innerHTML = "This may take up to 5 seconds.";
  document.getElementById("add-login-result-status").classList.remove("bg-primary");
  document.getElementById("add-login-result-status").classList.remove("bg-success");
  document.getElementById("add-login-result-status").classList.remove("bg-danger");
  document.getElementById("add-login-result-status").classList.add("bg-primary");
  document.getElementById("add-login-button").disabled = true;


  var xhttp = new XMLHttpRequest();

  xhttp.open("POST", "./api/v1/add_login.php", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  var memberIDField = document.getElementById("add-login-member-ID").value;
  var emailField    = document.getElementById("add-login-email").value;
  var passwordField = document.getElementById("add-login-password").value;
  
  xhttp.send("member_ID=" + memberIDField + "&email=" + emailField + "&password"  + passwordField);
  xhttp.onreadystatechange = function() {
    const JSON_response = JSON.parse(this.responseText);

    if (JSON_response.status == "success") {
      document.getElementById("add-login-button-text").innerHTML = "Success!";
      document.getElementById("add-login-result-title").innerHTML = "Success!";
      document.getElementById("add-login-result-text").innerHTML = "You've added this login.";
      document.getElementById("add-login-result-icon").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-green icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M9 12l2 2l4 -4" /></svg>';
      document.getElementById("add-login-button-icon").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>';
      document.getElementById("add-login-result-status").classList.remove("bg-primary");
      document.getElementById("add-login-result-status").classList.remove("bg-success");
      document.getElementById("add-login-result-status").classList.remove("bg-danger");
      document.getElementById("add-login-result-status").classList.add("bg-success");
      document.getElementById("add-login-button").disabled = true;
    }
    else {
      document.getElementById("add-login-button-text").innerHTML = "Add login";
      document.getElementById("add-login-result-title").innerHTML = "Oops! Something went wrong.";
      document.getElementById("add-login-result-text").innerHTML = "Error: " + JSON_response.error_message;
      document.getElementById("add-login-result-icon").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-red icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="12" cy="12" r="9"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>';
      document.getElementById("add-login-button-icon").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>';
      document.getElementById("add-login-result-status").classList.remove("bg-primary");
      document.getElementById("add-login-result-status").classList.remove("bg-success");
      document.getElementById("add-login-result-status").classList.remove("bg-danger");
      document.getElementById("add-login-result-status").classList.add("bg-danger");
      document.getElementById("add-login-button").disabled = false;
    }
  }
}