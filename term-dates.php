<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/kernel.php"); ?>

<?php $db_connection = db_connect(); ?>

<?php
  $term_id = $_GET["term_id"];

  $term_id_valid = false;

  $term_ID_query = $db_connection->prepare("SELECT `ID` FROM `terms` WHERE `ID`=? LIMIT 1");
  $term_ID_query ->bind_param("s", $term_id);
  $term_ID_query ->execute();
  $term_ID_query ->store_result();
  
  $term_ID_num_rows = $term_ID_query ->num_rows;

  $term_id_valid = ($term_ID_num_rows > 0);

?>

<?php
if (login_valid() and login_restricted(1))
{
  if ($term_id_valid)
  {

    ?>

    <!doctype html>

    <html lang="en">

    <head>
      <?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/head.php"); ?>
      <title>Term dates</title>
    </head>

    <body>
      <div class="wrapper">
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/header.php"); ?>
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/navigation.php"); ?>
        <div class="page-wrapper">
          <div class="page-body">
            <div class="container-xl">
              <div class="row row-cards">
                <div class="col-12">
                  <?php output_term_dates($term_id, INF); ?>
                </div>
              </div>
            </div>
          </div>
          <?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"); ?>
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
    ?>

    <!doctype html>

    <html lang="en">

    <head>
      <?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/head.php"); ?>
      <title>Term dates</title>
    </head>

    <body>
      <div class="wrapper">
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/header.php"); ?>
        <?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/navigation.php"); ?>
        <div class="page-wrapper">
          <div class="page-body">
            <div class="container-xl">
              <div class="row row-cards">
                <div class="col-12">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">
                        <p><a href="./term-dates.php">Term dates</a></p>
                      </h3>
                    </div>
                    <div class="card-body">
                      Invalid term id provided: <?=$term_id;?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"); ?>
        </div>
      </div>

      <script src="./dist/js/tabler.min.js"></script>
      <script src="./dist/js/demo.min.js"></script>
    </body>

    </html>

    <?php
  }
}
else
{
  output_restricted_page();
}

?>