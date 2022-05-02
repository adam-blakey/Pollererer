<?php require_once($_SERVER['DOCUMENT_ROOT']."/includes/kernel.php"); ?>

<!doctype html>

<html lang="en">
  <head>
    <?php include($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); ?>
    <title>Members</title>
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

<?php include($_SERVER['DOCUMENT_ROOT']."/includes/db_disconnect.php"); ?>