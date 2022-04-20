<?php
  $redirect_page = isset($_GET["redirect_page"])?urldecode($_GET["redirect_page"]):"/login.php";

  require_once("./includes/functions.php");

  do_logout();

  header("Location: https://attendance.nsw.org.uk".$redirect_page);
?>