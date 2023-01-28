<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php"); ?>

<?php
	$title = $config["software_name"];
?>

<!doctype html>
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/kernel.php"); ?>
<?php $db_connection = db_connect(); ?>

<html lang="en">
	<head>
		<?php include($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); ?>
		<meta name="robots" content="noindex,nofollow">
		<title><?=$title;?></title>
	</head>
  <body class=" border-top-wide border-primary d-flex flex-column">
		<?php include($_SERVER['DOCUMENT_ROOT']."/includes/header.php"); ?>
    <?php include($_SERVER['DOCUMENT_ROOT']."/includes/navigation.php"); ?>
    <div class="page page-center">
      <div class="container-narrow py-4">
        <div class="text-center mb-4">
          <a target="_blank" href="<?=$config["home_url"];?>" class="navbar-brand navbar-brand-autodark"><img src="<?=$config["logo_url"];?>" height="36" alt=""></a>
        </div>
        <div class="card card-md">
          <div class="card-body">
						<?php
							$all_polls_query = $db_connection->query("SELECT DISTINCT `terms`.`ID` AS `term_ID`, `terms`.`name` AS `term_name`, `terms`.`safe_name` AS `term_safe_name`, `ensembles`.`ID` AS `ensemble_ID`, `ensembles`.`name` AS `ensemble_name`, `ensembles`.`safe_name` AS `ensemble_safe_name` FROM `terms` CROSS JOIN `ensembles` WHERE `ensembles`.`hidden` = 0 AND `terms`.`hidden` = 0 ORDER BY `terms`.`ID`, `ensembles`.`ID` DESC");

							if ($all_polls_query->num_rows == 0)
							{
								?>
									<p>No polls to display.</p>
								<?php
							}
							else
							{
								?>
									<div class="row row-cards">
								<?php
								while($poll = $all_polls_query->fetch_assoc())
								{
									$poll_ended = $db_connection->query("SELECT `datetime` FROM term_dates WHERE `term_ID`='".$poll["term_ID"]."' AND (`is_featured` >= 0 OR `is_featured`='-".$poll["ensemble_ID"]."') AND `datetime` >= UNIX_TIMESTAMP()");

									if ($poll_ended->num_rows == 0)
									{
										$poll_ended = true;	
									}
									else
									{
										$poll_ended = false;
									}
									?>
										<div class="col-md-6 col-lg-6">
											<div class="card">
												<?php
												if ($poll_ended)
												{
													?>
														<div class="ribbon bg-red">
															ENDED
														</div>
													<?php
												}
												else
												{
													?>
														<div class="ribbon bg-green">
															ACTIVE
														</div>
													<?php
												}
												?>
												<div class="card-body">
													<h3 class="card-title w-100"><?=$poll["ensemble_name"];?>: <?=$poll["term_name"];?></h3>
													<a href="<?=$config["base_url"]."/".$poll["ensemble_safe_name"]."/".$poll["term_safe_name"]."/";?>" class="btn btn-primary w-100 <?=($poll_ended)?"disabled":"";?>">
														Visit poll
													</a>
												</div>
											</div>
										</div>
									<?php
								}
								?>
									</div>
								<?php
							}
						?>

          </div>
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