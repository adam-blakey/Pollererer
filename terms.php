<?php require_once($_SERVER['DOCUMENT_ROOT']."/includes/kernel.php"); ?>

<?php $db_connection = db_connect(); ?>

<?php
  if (login_valid() and login_restricted(3))
  {
    ?>

    <!doctype html>

    <html lang="en">
      <head>
        <?php include($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); ?>
        <title>Terms</title>
      </head>
      <body>
        <div class="wrapper">
          <?php include($_SERVER['DOCUMENT_ROOT']."/includes/header.php"); ?>
          <?php include($_SERVER['DOCUMENT_ROOT']."/includes/navigation.php"); ?>
          <div class="page-wrapper">
            <div class="page-body">
              <div class="container-xl">
                <div class="row row-cards">              
                  <div class="col-12">
                    <?php output_terms(INF); ?>
                  </div>
                </div>
              </div>
            </div>
            <?php include($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
          </div>
        </div>

        <script src="./dist/js/tabler.min.js"></script>
        <script src="./dist/js/demo.min.js"></script>
      </body>
    </html>

    <?php
  }
  else
  {
    output_restricted_page();
  }
?>