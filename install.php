<!doctype html>
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/kernel.php"); ?>
<?php $db_connection = db_connect(); ?>
<?php $title = "Install Pollererer"; ?>
<?php $step = (isset($_GET['step']))?$_GET['step']:0; ?>

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
                <div class="row align-items-center mt-3">
                  <div class="col-9">
                    <div class="steps">
                      <span class="step-item" data-bs-toggle="tooltip" title="Step 1 description">
                        Step 1
                      </span>
                      <span class="step-item" data-bs-toggle="tooltip" title="Step 2 description">
                        Step 2
                      </span>
                      <span class="step-item" data-bs-toggle="tooltip" title="Step 3 description">
                        Step 3
                      </span>
                      <span class="step-item" data-bs-toggle="tooltip" title="Step 4 description">
                        Step 4
                      </span>
                    </div>
                  </div>
                  <div class="col">
                    <div class="btn-list">
                      <a href="#" class="btn btn-primary">
                        Start
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              <?php
              break;
            case 1:
              ?>
              <div class="card card-md">
                <div class="card-body">
                  <h2 class="">Step 1</h2>
                  <p class="text-muted">Blahby blah blah.</p>
                </div>
                <div class="hr-text hr-text-center hr-text-spaceless">basic details</div>
                <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Pollererer base URL</label>
                    <div class="input-group input-group-flat">
                      <input type="text" class="form-control" autocomplete="off" placeholder="https://polls.example.com/" />
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Group home URL</label>
                    <div class="input-group input-group-flat">
                      <input type="text" class="form-control" autocomplete="off" placeholder="https://example.com/" />
                    </div>
                    <div class="form-hint">Optional, and can link back to your group's main website.</div>
                  </div>
                </div>
                <div class="hr-text hr-text-center hr-text-spaceless">next step</div>
                <div class="row align-items-center mt-3">
                  <div class="col-9">
                    <div class="steps">
                      <a href="#" class="step-item active" data-bs-toggle="tooltip" title="Step 1 description">
                        Step 1
                      </a>
                      <a href="#" class="step-item" data-bs-toggle="tooltip" title="Step 2 description">
                        Step 2
                      </a>
                      <a href="#" class="step-item" data-bs-toggle="tooltip" title="Step 3 description">
                        Step 3
                      </a>
                      <span href="#" class="step-item" data-bs-toggle="tooltip" title="Step 4 description">
                        Step 4
                      </span>
                    </div>
                  </div>
                  <div class="col">
                    <div class="btn-list">
                      <a href="#" class="btn btn-primary">
                        Next
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              <?php
              break;

            case 2:
              ?>
              <div class="card card-md">
              <div class="card-body">
                  <h2 class="">Step 2</h2>
                  <p class="text-muted">Pollererer needs a database connection. We'd recommend a fresh copy of MariaDB.</p>
                </div>
                <div class="hr-text hr-text-center hr-text-spaceless">database details</div>
                <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Pollererer base URL</label>
                    <div class="input-group input-group-flat">
                      <input type="text" class="form-control" autocomplete="off" placeholder="https://polls.example.com/" />
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Group home URL</label>
                    <div class="input-group input-group-flat">
                      <input type="text" class="form-control" autocomplete="off" placeholder="https://example.com/" />
                    </div>
                    <div class="form-hint">Optional, and can link back to your group's main website.</div>
                  </div>
                </div>
                <div class="hr-text hr-text-center hr-text-spaceless">next step</div>
                <div class="row align-items-center mt-3">
                  <div class="col-9">
                    <div class="steps">
                      <a href="#" class="step-item" data-bs-toggle="tooltip" title="Basic details">
                        Step 1
                      </a>
                      <a href="#" class="step-item active" data-bs-toggle="tooltip" title="Database details">
                        Step 2
                      </a>
                      <a href="#" class="step-item" data-bs-toggle="tooltip" title="Step 3 description">
                        Step 3
                      </a>
                      <span href="#" class="step-item" data-bs-toggle="tooltip" title="Step 4 description">
                        Step 4
                      </span>
                    </div>
                  </div>
                  <div class="col">
                    <div class="btn-list">
                      <a href="#" class="btn btn-primary">
                        Next
                      </a>
                    </div>
                  </div>
                </div>
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