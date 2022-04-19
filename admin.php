<?php require_once("./includes/kernel.php"); ?>

<?php
  login_valid();
  
  if(login_restricted(1))
  {
    $db_connection = db_connect();

    $title = "Admin overview";

    ?>

    <html lang="en">
      <head>
        <?php include("./includes/head.php"); ?>
        <title><?=$title;?></title>
        <script type="text/javascript" src="./includes/cards.js"></script>
      </head>
      <body>
        <div class="wrapper">
          <?php include("./includes/header.php"); ?>
          <?php include("./includes/navigation.php"); ?>     
          <div class="page-wrapper">
            <div class="container-xl">
              <div class="page-header d-print-none">
                <div class="row align-items-center">
                  <div class="col">
                    <h2 class="page-title">
                      <?=$title;?>
                    </h2>
                  </div>
                </div>
              </div>
            </div>   
            <div class="page-body">
              <div class="container-xl">
                <div class="row row-cards">
                  <div class="col-md-6 col-xl-4">
                    <div class="row row-cards">
                      <div class="col-12">
                        <?php output_logins(15); ?>
                      </div>
                      <div class="col-12">
                        <?php output_members(30); ?>
                      </div>
                      <div class="col-12">
                        <?php output_polls(15); ?>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 col-xl-8">
                    <div class="row row-cards">
                      <div class="col-12">
                        <?php output_notifications(10); ?>
                      </div>

                      <div class="col-12">
                        <?php output_ensembles(10); ?>
                      </div>
                      <div class="col-12">
                        <?php output_terms(10); ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php include("./includes/footer.php"); ?>
          </div>
        </div>

        <script src="./dist/js/tabler.min.js"></script>
        <script src="./dist/js/demo.min.js"></script>
      </body>
    </html>
    <?php db_disconnect($db_connection); ?>

    <?php
  }
  else
  {
    output_restricted_page();
  }
?>