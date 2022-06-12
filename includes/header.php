<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php"); ?>
<?php require_once($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");?>

<header class="navbar navbar-expand-md navbar-light d-print-none">
  <div class="container-xl">
    <?php
      if (login_restricted(1))
      {
      ?>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
          <span class="navbar-toggler-icon"></span>
        </button>
      <?php
      }
    ?>
    <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
      <a href="<?=$config['home_url'];?>" target="_blank">
        <img src="<?=$config['logo_url'];?>" width="110" height="32" alt="<?$config['software_name'];?>" class="navbar-brand-image">
      </a>
    </h1>
    <div class="navbar-nav flex-row order-md-last">
      <a href="?<?=http_build_query(array_merge($_GET, array('theme'=>'dark')));?>" class="nav-link px-0 hide-theme-dark" title="Enable dark mode" data-bs-toggle="tooltip" data-bs-placement="bottom">
        <!-- Download SVG icon from http://tabler-icons.io/i/moon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" /></svg>
      </a>
      <a href="?<?=http_build_query(array_merge($_GET, array('theme'=>'light')));?>" class="nav-link px-0 hide-theme-light" title="Enable light mode" data-bs-toggle="tooltip" data-bs-placement="bottom">
        <!-- Download SVG icon from http://tabler-icons.io/i/sun -->
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="4" /><path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" /></svg>
      </a>
      <?php
        if (login_valid())
        {
          ?>

          <?php
            // ************************************** //
            // HARD-CODED FOR GENERIC ENSEMBLE LOGINS //
            // ************************************** //
            $login_query = $db_connection->query("SELECT `members`.`ID` AS `member_ID`, `members`.`first_name`, `members`.`last_name`, `image` FROM `members` LEFT JOIN `logins_sessions` ON `members`.`ID`=`logins_sessions`.`member_ID` WHERE `logins_sessions`.`ID`='".$_COOKIE["session_ID"]."' LIMIT 1");

            if ($login_query->num_rows == 0)
            {
              $icon_style = "";
              $name       = "Unknown User";
              $role       = "Unknown role";

              $group_user = false;
            }
            else
            {
              $login_query_result = $login_query->fetch_assoc();

              $icon_style = "style=\"background-image: url('".$login_query_result["image"]."')\"";
              $name       = $login_query_result["first_name"]." ".$login_query_result["last_name"];
              $role       = $login_query_result["first_name"]." User";

              $group_user = true;
            }

            // ************************************** //
            // ************************************** //
          ?>

          <?php
            if (!$group_user)
            {
              ?>
              <div class="nav-item dropdown d-none d-md-flex me-3">
                <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1" aria-label="Show notifications">
                  <!-- Download SVG icon from http://tabler-icons.io/i/bell -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" /><path d="M9 17v1a3 3 0 0 0 6 0v-1" /></svg>
                  <span class="badge bg-red"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-card">
                  <div class="list-group list-group-flush list-group-hoverable">
                    <div class="list-group-item">
                      <div class="row align-items-center">
                        <div class="col-auto"><span class="badge bg-red"></span></div>
                        <div class="col-auto">
                          <a href="#">
                            <span class="avatar">AB</span>
                          </a>
                        </div>
                        <div class="col-auto">
                          <a href="#" class="text-body d-block">Adam Blakey (NSWO, Clarinet)</a>
                          <small class="text-wrap text-muted">Edited their attendance for 1st May to 'not attending'.</small>
                          <small class="d-block text-muted mt-n1">8 minutes ago</small>
                        </div>
                      </div>
                    </div>
                    <div class="list-group-item">
                      <div class="row align-items-center">
                        <div class="col-auto"><span class="badge bg-red"></span></div>
                        <div class="col-auto">
                          <a href="#">
                            <span class="avatar">BL</span>
                          </a>
                        </div>
                        <div class="col-auto">
                          <a href="#" class="text-body d-block">Bridget Langham (NSWO, Saxophone)</a>
                          <small class="text-wrap text-muted mt-n1">Edited their attendance for 1st May to 'attending'.</small>
                          <small class="d-block text-muted mt-n1">13 minutes ago</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <?php
            }
          ?>

          <div class="nav-item dropdown">
            <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
              <span class="avatar avatar-sm" <?=$icon_style;?>></span>
              <div class="d-none d-xl-block ps-2">
                <div><?=$name;?></div>
                <div class="mt-1 small text-muted"><?=$role;?></div>
              </div>
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <?php
                if (!$group_user)
                {
                  ?>
                    <a href="#" class="dropdown-item">View notifications</a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">Settings</a>
                  <?php
                }
              ?>
              <a href="./logout.php?redirect_page=<?=urlencode($_SERVER["REQUEST_URI"]);?>" class="dropdown-item">Logout</a>
            </div>
          </div>
        <?php
        }
        else
        {
        ?>
            <a href="./login.php?redirect_page=<?=urlencode($_SERVER["REQUEST_URI"]);?>" class="nav-link d-flex lh-1 text-reset p-0" aria-label="Login">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-login" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                <path d="M20 12h-13l3 -3m0 6l-3 -3" />
              </svg>
            </a>
        <?php
        }
        ?>
    </div>
  </div>
</header>