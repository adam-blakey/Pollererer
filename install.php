<!doctype html>
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/kernel.php"); ?>
<?php $db_connection = db_connect(); ?>
<?php $title = "Install Pollererer"; ?>
<?php $step = (isset($_GET['step']))?$_GET['step']:0; ?>

<?php
  function output_steps($step)
  {
    $step_names = array("Welcome", "Basic details", "Database details", "Poll settings", "Create admin login");
    $step_links = array("./install.php?step=0", "./install.php?step=1", "./install.php?step=2", "./install.php?step=3", "./install.php?step=4");

    $output_html = "";

    $output_html .= '<div class="row align-items-center mt-3">';
      $output_html .= '<div class="col-9">';
        $output_html .= '<div class="steps">';
            for ($i = 0; $i < count($step_names); $i++)
            {
              $active = ($i == $step)?"active":"";
              $link = $step_links[$i];
              $name = $step_names[$i];
              
              $output_html .= '<a class="step-item '.$active.'" href="'.$link.'" data-bs-toggle="tooltip" title="'.$name.'">Step '.$i.'</a>';
            }
        $output_html .= '</div>';
      $output_html .= '</div>';
      $output_html .= '<div class="col">';
        $output_html .= '<div class="btn-list">';
          if ($step < count($step_names) - 1)
          {
            $output_html .= '<a class="btn btn-primary" href="./install.php?step='.($step + 1).'">Next</a>';
          }
          else
          {
            $output_html .= '<a class="btn btn-primary" href="./admin.php">Finish</a>';
          }
          $output_html .= '</div>';
      $output_html .= '</div>';
    $output_html .= '</div>';

    return $output_html;
  }
?>

<html lang="en">
	<head>
		<?php include($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); ?>
		<meta name="robots" content="noindex,nofollow">
		<title><?=$title;?></title>
	</head>
  <body class=" border-top-wide border-primary d-flex flex-column">
    <script src="./dist/js/demo-theme.min.js?1660132725"></script>
    <div class="page page-center">
      <div class="container-tight py-4">
        <div class="text-center mb-4">
          <a href="." class="navbar-brand navbar-brand-autodark"><img src="./static/logo.svg" height="36" alt=""></a>
        </div>
        <?php
          switch ($step)
          {
            case 0:
              ?>
              <div class="card card-md">
                <div class="card-body text-center py-4 p-sm-5">
                  <img src="./static/logo.png" height="128" class="mb-n2" alt="">
                  <h1 class="mt-5">Welcome to Pollererer!</h1>
                  <p class="text-muted">Pollererer is a simple music rehearsal absence system, where members can login and fill out their availabiliy.</p>
                </div>
                <div class="hr-text hr-text-center hr-text-spaceless">start wizard</div>
                <?=output_steps($step);?>
              </div>
              <?php
              break;
            case 1:
              ?>
              <div class="card card-md">
                <div class="card-body">
                  <h2 class="">Step 1: Basic details</h2>
                  <p class="text-muted">Blahby blah blah.</p>
                </div>
                <div class="hr-text hr-text-center hr-text-spaceless">group details</div>
                <div class="card-body">
                  <div class="mb-3">
                    <label class="form-label">Group name</label>
                    <div class="input-group input-group-flat">
                      <input id="group_name" type="text" class="form-control" autocomplete="off" placeholder="The Example Orchestra" />
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Group home URL</label>
                    <div class="input-group input-group-flat">
                      <input id="home_url" type="text" class="form-control" autocomplete="off" placeholder="https://example.com/" />
                    </div>
                  </div>
                </div>
                <div class="hr-text hr-text-center hr-text-spaceless">pollererer details</div>
                <div class="card-body">
                  <div class="mb-3">
                    <label class="form-label">Software name</label>
                    <div class="input-group input-group-flat">
                      <input id="software_name" type="text" class="form-control" autocomplete="off" placeholder="Pollererer" />
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Pollererer base URL</label>
                    <div class="input-group input-group-flat">
                      <input id="base_url" type="text" class="form-control" autocomplete="off" placeholder="https://polls.example.com/" />
                    </div>
                  </div>
                  <div class="mb-3">
                    <div class="form-label">Software logo</div>
                    <input id="logo_url" type="file" class="form-control">
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Admin email address</label>
                    <div class="input-group input-group-flat">
                      <input id="admin_email" type="email" class="form-control" autocomplete="off" placeholder="admin@example.com" />
                    </div>
                  </div>
                </div>
                <div class="hr-text hr-text-center hr-text-spaceless">next step</div>
                  <?=output_steps($step);?>
                </div>
              </div>
              <?php
              break;

            case 2:
              ?>
              <div class="card card-md">
              <div class="card-body">
                  <h2 class="">Step 2: Database details</h2>
                  <p class="text-muted">Pollererer needs a database connection. We'd recommend a fresh copy of MariaDB.</p>
                </div>
                <div class="hr-text hr-text-center hr-text-spaceless">database details</div>
                <div class="card-body">
                  <div class="mb-3">
                    <label class="form-label">Database host</label>
                    <div class="input-group input-group-flat">
                      <input type="text" class="form-control" autocomplete="off" placeholder="localhost:3206" />
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Database username</label>
                    <div class="input-group input-group-flat">
                      <input type="text" class="form-control" autocomplete="off" placeholder="pollererer" />
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Database password</label>
                    <div class="input-group input-group-flat">
                      <input type="text" class="form-control" autocomplete="off" placeholder="Password123!" />
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Database name</label>
                    <div class="input-group input-group-flat">
                      <input type="text" class="form-control" autocomplete="off" placeholder="pollererer" />
                    </div>
                  </div>
                </div>
                <div class="hr-text hr-text-center hr-text-spaceless">next step</div>
                <?=output_steps($step);?>
              </div>
              <?php
              break;

            case 3:
              ?>
              <div class="card card-md">
              <div class="card-body">
                  <h2 class="">Step 3: Poll settings</h2>
                  <p class="text-muted">Blahby blah blah.</p>
                </div>
                <div class="hr-text hr-text-center hr-text-spaceless">poll interface</div>
                <div class="card-body">
                  <div class="mb-3">
                    <div class="form-label">Repeat headings</div>
                    <label class="form-check form-switch">
                      <input class="form-check-input" type="checkbox">
                    </label>
                  </div>
                </div>
                <div class="hr-text hr-text-center hr-text-spaceless">pre-rehearsal email</div>
                <div class="card-body">
                  <div class="mb-3">
                    <div class="form-label">Send a pre-rehearsal email</div>
                    <label class="form-check form-switch">
                      <input id="email_pdf" class="form-check-input" type="checkbox">
                    </label>
                  </div>
                  <fieldset class="form-fieldset">
                    <div class="mb-3">
                      <label class="form-label required">Email from address</label>
                      <input id="email_from" type="email" class="form-control" autocomplete="off" placeholder="pollererer@example.com" disabled>
                    </div>
                    <div class="mb-3">
                      <label class="form-label required">SMTP host</label>
                      <input id="smtp_host" type="text" class="form-control" autocomplete="off" placeholder="mail.example.com" disabled>
                    </div>
                    <div class="mb-3">
                      <label class="form-label required">SMTP username</label>
                      <input id="smtp_username" type="text" class="form-control" autocomplete="off" placeholder="pollererer@example.com" disabled>
                    </div>
                    <div class="mb-3">
                      <label class="form-label required">SMTP password</label>
                      <input id="smtp_password" type="text" class="form-control" autocomplete="off" placeholder="Password123!" disabled>
                    </div>
                    <div class="mb-3">
                      <label class="form-label required">SMTP port</label>
                      <input id="smtp_port" type="number" class="form-control" autocomplete="off" placeholder="465" disabled>
                    </div>
                  </fieldset>
                </div>
                <div class="hr-text hr-text-center hr-text-spaceless">next step</div>
                <?=output_steps($step);?>
              </div>
              <?php
              break;

            case 4:
              ?>
              <div class="card card-md">
              <div class="card-body">
                  <h2 class="">Step 4: Create admin login</h2>
                  <p class="text-muted">Blahby blah blah.</p>
                </div>
                <div class="hr-text hr-text-center hr-text-spaceless">database details</div>
                <div class="card-body">
                  <div class="mb-3">
                    <label class="form-label">First name</label>
                    <div class="input-group input-group-flat">
                      <input id="admin-user_first-name" type="text" class="form-control" autocomplete="off" placeholder="Alice" />
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Last name</label>
                    <div class="input-group input-group-flat">
                      <input id="admin-user_last-name" type="text" class="form-control" autocomplete="off" placeholder="Smith" />
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Email</label>
                    <div class="input-group input-group-flat">
                      <input id="admin-user_email" type="text" class="form-control" autocomplete="off" placeholder="admin@example.com" />
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group input-group-flat">
                      <input id="admin-user_password" type="password" class="form-control" autocomplete="off" placeholder="******" />
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Password repeat</label>
                    <div class="input-group input-group-flat">
                      <input id="admin-user_password-repeat" type="password" class="form-control" autocomplete="off" placeholder="******" />
                    </div>
                  </div>
                </div>
                <div class="hr-text hr-text-center hr-text-spaceless">next step</div>
                <?=output_steps($step);?>
              </div>
              <?php
              break;

            default:
              
          }
        ?>
        <div class="card">
          
        </div>
      </div>
    </div>
    <script
	src="https://browser.sentry-cdn.com/5.27.6/bundle.tracing.min.js"
	integrity="sha384-9Z8PxByVWP+gIm/rTMPn9BWwknuJR5oJcLj+Nr9mvzk8nJVkVXgQvlLGZ9SIFEJF"
	crossorigin="anonymous"
></script>
    <script>
      Sentry.init({
      	dsn: "https://8e4ad02f495946f888620f9fb99fd495@o484108.ingest.sentry.io/5536918",
      	release: "tabler@1.0.0-beta11",
      	integrations: [
      		new Sentry.Integrations.BrowserTracing()
      	],

      	tracesSampleRate: 1.0,
      });
    </script>
    <!-- Libs JS -->
    <!-- Tabler Core -->
    <script src="./dist/js/tabler.min.js?1660132725" defer></script>
    <script src="./dist/js/demo.min.js?1660132725" defer></script>
  </body>
</html>

<?php
	db_disconnect($db_connection);
?>