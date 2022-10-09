<?php
// // https://www.php.net/manual/en/function.mail.php
// function mail_with_attachment
// (
//     string $fileAttachment,
//     string $mailMessage,
//     string $subject,
//     string $toAddress,
//     string $ccAddress,
//     string $fromAddress
// )
// : bool
// {
//     $fileAttachment = trim($fileAttachment);
//     $from           = $fromMail;
//     $pathInfo       = pathinfo($fileAttachment);
//     $attchmentName  = $pathInfo["filename"];
   
//     $attachment    = chunk_split(base64_encode(file_get_contents($fileAttachment)));
//     $boundary      = "PHP-mixed-".md5(time());
//     $boundWithPre  = "\n--".$boundary;
   
//    	$headers  = "MIME-Version: 1.0\r\n";
//     $headers .= "From: ".$config["software_name"]." <$fromAddress>\r\n";
//     $headers .= "Content-Type: multipart/mixed; boundary=\"".$boundary."\"";
//     $headers .= "CC: <$ccAddress>\r\n";
// 		$headers .= "X-Mailer: PHP/".phpversion()."\r\n";
   
//     $message  = $boundWithPre;
//     $message .= "\n Content-Type: text/html; charset=UTF-8\n";
//     $message .= "\n $mailMessage";
   
//     $message .= $boundWithPre;
//     $message .= "\nContent-Type: application/octet-stream; name=\"".$attchmentName."\"";
//     $message .= "\nContent-Transfer-Encoding: base64\n";
//     $message .= "\nContent-Disposition: attachment\n";
//     $message .= $attachment;
//     $message .= $boundWithPre."--";
   
//     return mail($toAddress, $subject, $message, $headers);
// }

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	require_once($_SERVER['DOCUMENT_ROOT']."/dist/libs/phpmailer/src/Exception.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/dist/libs/phpmailer/src/PHPMailer.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/dist/libs/phpmailer/src/SMTP.php");

	require_once($_SERVER['DOCUMENT_ROOT']."/includes/kernel.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/cron/generate-seating-plan-pdf.php");

	$time = new DateTime();
	$time ->setTimestamp(time());
	$time ->setTimezone(new DateTimeZone('Europe/London'));

	$db_connection = db_connect();

	$upcoming_rehearsals = $db_connection->query("SELECT DISTINCT `term_dates`.`ID` AS `term_dates_ID`, `ensembles`.`ID` AS `ensemble_ID`, `ensembles`.`name` AS `ensemble_name`, `ensembles`.`admin_email` FROM `term_dates` CROSS JOIN `ensembles` WHERE `term_dates`.`deleted`='0' AND `datetime` > ".($time->format('U') + 60*60*0)." AND `datetime` <= ".($time->format('U') + 60*60*1)." AND ((`term_dates`.`is_featured` = 0) OR (`term_dates`.`is_featured` = -`ensembles`.`ID`))");

	while($rehearsal = $upcoming_rehearsals->fetch_assoc())
	{
		$term_date_ID = $rehearsal["term_dates_ID"];
		$ensemble_ID  = $rehearsal["ensemble_ID"];

		$been_sent_check = $db_connection->query("SELECT `been_sent` FROM `pre-rehearsal-email` WHERE `ensemble_ID`='".$ensemble_ID."' AND `term_date_ID`='".$term_date_ID."' LIMIT 1");

		if ($been_sent_check->num_rows == 0)
		{
			$been_sent = false;
		}
		else
		{
			if ($been_sent_check->fetch_array()[0] == "1")
			{
				$been_sent = true;
			}
			else
			{
				$been_sent = false;

			}
		}

		if (!$been_sent)
		{
			$ensemble_name = $db_connection->query("SELECT `name` FROM `ensembles` WHERE `ID`='".$ensemble_ID."'")->fetch_array()[0];

			$rehearsal_date = $db_connection->query("SELECT `datetime` FROM `term_dates` WHERE `ID`='".$term_date_ID."'")->fetch_array()[0];

			$member_query = $db_connection->query("SELECT `members`.`ID`, `members`.`first_name`, `members`.`last_name`, `members`.`instrument` FROM `members` LEFT JOIN `members-ensembles` ON `members`.`ID` = `members-ensembles`.`member_ID` WHERE `members-ensembles`.`ensemble_ID` = '".$ensemble_ID."' AND `members`.`deleted`='0' ORDER BY `members`.`instrument`, `members`.`first_name`");

			$attendance_list  = array();
			$absence_list     = array();
			$no_response_list = array();

			if ($member_query)
			{
				while($member = $member_query->fetch_assoc())
				{
					$attendance_query = $db_connection->query("SELECT `status`, `edit_datetime` FROM `attendance` WHERE `member_ID`='".$member['ID']."' AND `ensemble_ID`='".$ensemble_ID."' AND `term_dates_ID`='".$term_date_ID."' ORDER BY `edit_datetime` DESC LIMIT 1");

					if ($attendance_query->num_rows>0)
					{
						$attendance = $attendance_query->fetch_assoc();
					}
					else
					{
						$attendance["status"] = NULL;
						$attendance["edit_datetime"] = 0;
					}

					$new_status = new attendanceStatus
					(
						$member["ID"],
						//$member["ensemble_ID"],
						$ensemble_ID,
						$attendance["status"],
						$member["first_name"],
						$member["last_name"],
						$member["instrument"],
						$attendance["edit_datetime"]
					);

					if ($new_status->status == "1")
					{
						$attendance_list[$new_status->instrument][] = $new_status;
					}
					elseif ($new_status->status == "0")
					{
						$absence_list[$new_status->instrument][] = $new_status;
					}
					else
					{
						$no_response_list[$new_status->instrument][] = $new_status;
					}
				}
			}
			else
			{
				echo "Failed on member query: ".$attendance_query->error_message;
			}

			$attendance_count  = 0;
			foreach ($attendance_list as $item)
			{
				$attendance_count += count($item);
			}

			$absence_count     = 0;
			foreach ($absence_list as $item)
			{
				$absence_count += count($item);
			}

			$no_response_count = 0;
			foreach ($no_response_list as $item)
			{
				$no_response_count += count($item);
			}

			$total_count       = $attendance_count + $absence_count + $no_response_count;

			$to       = $rehearsal["admin_email"];
			$subject  = "Rehearsal Attendance for ".$rehearsal["ensemble_name"]." on ".date("jS M", $rehearsal_date);;
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8\r\n";
			$headers .= "From: ".$config["software_name"]." <".$config["software_name"].">\r\n";
			$headers .= "CC: <".$config["admin_email"].">\r\n";
			$headers .= "X-Mailer: PHP/".phpversion()."\r\n";

			$message  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
			$message .= '<html xmlns="http://www.w3.org/1999/xhtml" xmlns="http://www.w3.org/1999/xhtml">';
			$message .= '';
			$message .= '<head>';
				$message .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
				$message .= '<meta name="viewport" content="width=device-width, initial-scale=1.0" />';
				$message .= '<meta content="telephone=no" name="format-detection" />';
				$message .= '<title>'.$subject.'</title>';
				$message .= '<style type="text/css" data-premailer="ignore">';
					$message .= '@import url(https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700);';
				$message .= '</style>';
				$message .= '<style data-premailer="ignore">';
					$message .= '@media screen and (max-width: 600px) {';
						$message .= 'u+.body {';
							$message .= 'width: 100vw !important;';
						$message .= '}';
					$message .= '}';
			$message .= '';
					$message .= 'a[x-apple-data-detectors] {';
						$message .= 'color: inherit !important;';
						$message .= 'text-decoration: none !important;';
						$message .= 'font-size: inherit !important;';
						$message .= 'font-family: inherit !important;';
						$message .= 'font-weight: inherit !important;';
						$message .= 'line-height: inherit !important;';
					$message .= '}';
				$message .= '</style>';
				$message .= '<!--[if mso]>';
				  $message .= '<style type="text/css">';
					$message .= 'body, table, td {';
						$message .= 'font-family: Arial, Helvetica, sans-serif !important;';
					$message .= '}';
			$message .= '		';
					$message .= 'img {';
						$message .= '-ms-interpolation-mode: bicubic;';
					$message .= '}';
			$message .= '		';
					$message .= '.box {';
						$message .= 'border-color: #eee !important;';
					$message .= '}';
				  $message .= '</style>';
				$message .= '<![endif]-->';
			$message .= '	';
			$message .= '<style>body {';
			$message .= 'margin: 0; padding: 0; background-color: #f5f7fb; font-size: 15px; line-height: 160%; mso-line-height-rule: exactly; color: #444; width: 100%;';
			$message .= '}';
			$message .= 'body {';
			$message .= 'font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif;';
			$message .= '}';
			$message .= 'img {';
			$message .= 'border: 0; line-height: 100%; outline: none; text-decoration: none; vertical-align: baseline; font-size: 0;';
			$message .= '}';
			$message .= 'a:hover {';
			$message .= 'text-decoration: underline;';
			$message .= '}';
			$message .= '.btn:hover {';
			$message .= 'text-decoration: none;';
			$message .= '}';
			$message .= '.btn.bg-bordered:hover {';
			$message .= 'background-color: #f9fbfe !important;';
			$message .= '}';
			$message .= 'a.bg-blue:hover {';
			$message .= 'background-color: #3a77cc !important;';
			$message .= '}';
			$message .= 'a.bg-azure:hover {';
			$message .= 'background-color: #37a3f1 !important;';
			$message .= '}';
			$message .= 'a.bg-indigo:hover {';
			$message .= 'background-color: #596ac9 !important;';
			$message .= '}';
			$message .= 'a.bg-purple:hover {';
			$message .= 'background-color: #9d50e8 !important;';
			$message .= '}';
			$message .= 'a.bg-pink:hover {';
			$message .= 'background-color: #f55f91 !important;';
			$message .= '}';
			$message .= 'a.bg-red:hover {';
			$message .= 'background-color: #c01e1d !important;';
			$message .= '}';
			$message .= 'a.bg-orange:hover {';
			$message .= 'background-color: #fd8e35 !important;';
			$message .= '}';
			$message .= 'a.bg-yellow:hover {';
			$message .= 'background-color: #e3b90d !important;';
			$message .= '}';
			$message .= 'a.bg-lime:hover {';
			$message .= 'background-color: #73cb2d !important;';
			$message .= '}';
			$message .= 'a.bg-green:hover {';
			$message .= 'background-color: #56ab00 !important;';
			$message .= '}';
			$message .= 'a.bg-teal:hover {';
			$message .= 'background-color: #28beae !important;';
			$message .= '}';
			$message .= 'a.bg-cyan:hover {';
			$message .= 'background-color: #1596aa !important;';
			$message .= '}';
			$message .= 'a.bg-gray:hover {';
			$message .= 'background-color: #95a9b0 !important;';
			$message .= '}';
			$message .= 'a.bg-secondary:hover {';
			$message .= 'background-color: #ecf0f2 !important;';
			$message .= '}';
			$message .= '.img-hover:hover img {';
			$message .= 'opacity: .64;';
			$message .= '}';
			$message .= '.theme-dark a.bg-secondary:hover {';
			$message .= 'background-color: #354258 !important;';
			$message .= '}';
			$message .= '.theme-dark .btn.bg-bordered:hover {';
			$message .= 'background-color: #467fcf !important; color: #fff !important;';
			$message .= '}';
			$message .= '.theme-dark .btn.bg-bordered:hover .btn-span {';
			$message .= 'color: #fff !important;';
			$message .= '}';
			$message .= '@media only screen and (max-width:560px) {';
			  $message .= 'body {';
			    $message .= 'font-size: 14px !important;';
			  $message .= '}';
			  $message .= '.content {';
			    $message .= 'padding: 24px !important;';
			  $message .= '}';
			  $message .= '.content-image-text {';
			    $message .= 'padding: 24px !important;';
			  $message .= '}';
			  $message .= '.content-image {';
			    $message .= 'height: 100px !important;';
			  $message .= '}';
			  $message .= '.content-image-text {';
			    $message .= 'padding-top: 96px !important;';
			  $message .= '}';
			  $message .= 'h1 {';
			    $message .= 'font-size: 24px !important;';
			  $message .= '}';
			  $message .= '.h1 {';
			    $message .= 'font-size: 24px !important;';
			  $message .= '}';
			  $message .= 'h2 {';
			    $message .= 'font-size: 20px !important;';
			  $message .= '}';
			  $message .= '.h2 {';
			    $message .= 'font-size: 20px !important;';
			  $message .= '}';
			  $message .= 'h3 {';
			    $message .= 'font-size: 18px !important;';
			  $message .= '}';
			  $message .= '.h3 {';
			    $message .= 'font-size: 18px !important;';
			  $message .= '}';
			  $message .= '.col {';
			    $message .= 'display: table !important; width: 100% !important;';
			  $message .= '}';
			  $message .= '.col-spacer {';
			    $message .= 'display: table !important; width: 100% !important;';
			  $message .= '}';
			  $message .= '.col-spacer-xs {';
			    $message .= 'display: table !important; width: 100% !important;';
			  $message .= '}';
			  $message .= '.col-spacer-sm {';
			    $message .= 'display: table !important; width: 100% !important;';
			  $message .= '}';
			  $message .= '.col-hr {';
			    $message .= 'display: table !important; width: 100% !important;';
			  $message .= '}';
			  $message .= '.row {';
			    $message .= 'display: table !important; width: 100% !important;';
			  $message .= '}';
			  $message .= '.col-hr {';
			    $message .= 'border: 0 !important; height: 24px !important; width: auto !important; background: 0 0 !important;';
			  $message .= '}';
			  $message .= '.col-spacer {';
			    $message .= 'width: 100% !important; height: 24px !important;';
			  $message .= '}';
			  $message .= '.col-spacer-sm {';
			    $message .= 'height: 16px !important;';
			  $message .= '}';
			  $message .= '.col-spacer-xs {';
			    $message .= 'height: 8px !important;';
			  $message .= '}';
			  $message .= '.chart-cell-spacer {';
			    $message .= 'width: 4px !important;';
			  $message .= '}';
			  $message .= '.text-mobile-center {';
			    $message .= 'text-align: center !important;';
			  $message .= '}';
			  $message .= '.d-mobile-none {';
			    $message .= 'display: none !important;';
			  $message .= '}';
			$message .= '}';
			$message .= '</style></head>';
			$message .= '';
			$message .= '<body class="bg-body" style="font-size: 15px; line-height: 160%; mso-line-height-rule: exactly; color: #444; width: 100%; font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; margin: 0; padding: 0;" bgcolor="#f5f7fb">';
				$message .= '<center>';
					$message .= '<table class="main bg-body" width="100%" cellspacing="0" cellpadding="0" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; border-collapse: collapse; width: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;" bgcolor="#f5f7fb">';
						$message .= '<tr>';
							$message .= '<td align="center" valign="top" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif;">';
								$message .= '<!--[if (gte mso 9)|(IE)]>';
						  $message .= '<table border="0" cellspacing="0" cellpadding="0">';
							$message .= '<tr>';
							  $message .= '<td align="center" valign="top" width="640">';
						$message .= '<![endif]-->';
								$message .= '<span class="preheader" style="font-size: 0; display: none; max-height: 0; mso-hide: all; line-height: 0; color: transparent; height: 0; max-width: 0; opacity: 0; overflow: hidden; visibility: hidden; width: 0; padding: 0;">There are '.$attendance_count.' people attending, '.$no_response_count.' people with no response, and '.$absence_count.' people confirmed absent.</span>';
								$message .= '<table class="wrap" cellspacing="0" cellpadding="0" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; border-collapse: collapse; width: 100%; max-width: 640px; text-align: left;">';
									$message .= '<tr>';
										$message .= '<td class="p-sm" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; padding: 8px;">';
											$message .= '<table cellpadding="0" cellspacing="0" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; border-collapse: collapse; width: 100%;">';
												$message .= '<tr>';
													$message .= '<td class="py-lg" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; padding-top: 24px; padding-bottom: 24px;">';
														$message .= '<table cellspacing="0" cellpadding="0" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; border-collapse: collapse; width: 100%;">';
															$message .= '<tr>';
																$message .= '<td style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif;">';
																	//$message .= '<a href="https://tabler.io/emails?utm_source=demo" style="color: #467fcf; text-decoration: none;"><img src="'.$config["base_url"].'/emails/example/assets/sample-tabler-gray.png" width="116" height="34" alt="" style="line-height: 100%; outline: none; text-decoration: none; vertical-align: baseline; font-size: 0; border-width: 0;" /></a>';
																$message .= '<span class="text-muted-light font-sm" style="color: #bbc8cd; text-decoration: none; font-size: 13px;">Details correct as of '.$time->format("jS M Y @ H:i:s").'</span>';
																$message .= '</td>';
																$message .= '<td class="text-right" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif;" align="right">';
																	$message .= '<a href="'.$config["base_url"].'/email.php?ensemble_ID='.$ensemble_ID.'&term_date_ID='.$term_date_ID.'" class="text-muted-light font-sm" style="color: #bbc8cd; text-decoration: none; font-size: 13px;">';
																		$message .= 'View online';
																	$message .= '</a>';
																$message .= '</td>';
															$message .= '</tr>';
														$message .= '</table>';
													$message .= '</td>';
												$message .= '</tr>';
											$message .= '</table>';
											$message .= '<div class="main-content">';
												$message .= '<table class="box" cellpadding="0" cellspacing="0" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; border-collapse: collapse; width: 100%; border-radius: 3px; -webkit-box-shadow: 0 1px 4px rgba(0,0,0,.05); box-shadow: 0 1px 4px rgba(0,0,0,.05); border: 1px solid #f0f0f0;" bgcolor="#fff">';
													$message .= '<tr>';
														$message .= '<td style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif;">';
															$message .= '<table cellpadding="0" cellspacing="0" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; border-collapse: collapse; width: 100%;">';
																$message .= '<tr>';
																	$message .= '<td class="content" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; padding: 40px 48px;">';
																		$message .= '<table class="row row-flex" cellspacing="0" cellpadding="0" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; border-collapse: collapse; width: 100%; table-layout: auto;">';
																			$message .= '<tr>';
																				$message .= '<td class="col text-mobile-center va-middle lh-1" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; line-height: 100%;" valign="middle">';
																					$message .= '<a href="https://tabler.io/emails?utm_source=demo" style="color: #467fcf; text-decoration: none;">';
																						$message .= '</a><a href="https://tabler.io/emails?utm_source=demo" style="color: #467fcf; text-decoration: none;"><img src="'.$config["logo_url"].' height="34" alt="" style="line-height: 100%; outline: none; text-decoration: none; vertical-align: baseline; font-size: 0; border-width: 0;" /></a>';
			$message .= '																		';
																				$message .= '</td>';
																				$message .= '<td class="col-spacer col-spacer-sm" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; width: 16px;" valign="top"></td>';
																				$message .= '<td class="col text-mobile-center text-right font-sm pl-md" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; font-size: 13px; padding-left: 16px;" align="right" valign="top">';
																					$message .= '<span class="font-strong" style="font-weight: 600;">Rehearsal Attendance Update</span><br />';
																					$message .= $ensemble_name.': '.date("jS M Y @ H:i", $rehearsal_date);
																				$message .= '</td>';
																			$message .= '</tr>';
																		$message .= '</table>';
																		$message .= '<h4 class="mt-xl" style="font-weight: 600; font-size: 16px; margin: 48px 0 .5em;">Attendance stats</h4>';
																		$message .= '<table class="row" cellspacing="0" cellpadding="0" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; border-collapse: collapse; width: 100%; table-layout: fixed;">';
																			$message .= '<tr>';
																				$message .= '<td class="col text-center va-top" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif;" align="center" valign="top">';
																					$message .= '<table cellspacing="0" cellpadding="0" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; border-collapse: collapse; width: 100%;">';
																						$message .= '<tr>';
																							$message .= '<td class="text-left font-sm pb-xs" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; font-size: 13px; padding-bottom: 4px;" align="left">Attending</td>';
																							$message .= '<td class="text-right font-sm pb-xs text-muted" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; color: #9eb0b7; font-size: 13px; padding-bottom: 4px;" align="right">'.$attendance_count.'</td>';
																						$message .= '</tr>';
																						$message .= '<tr>';
																							$message .= '<td colspan="2" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif;">';
																								$message .= '<table class="chart" cellpadding="0" cellspacing="0" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; border-collapse: collapse; width: 100%; table-layout: fixed;">';
																									$message .= '<tr>';
																										$message .= '<td width="'.($attendance_count/$total_count*100)."%".'" class="chart-percentage bg-green" style="height: 6px; font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; font-size: 12px; padding-left: 5px; border-radius: 2px 0 0 2px; color: #fff;" bgcolor="#5eba00">'.round($attendance_count/$total_count*100, 1)."%".'</td>';
																										$message .= '<td width="'.((1 - $attendance_count/$total_count)*100)."%".'" class="chart-percentage bg-green-lightest" style="height: 6px; font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; font-size: 12px; border-radius: 0 2px 2px 0;" bgcolor="#eff8e6"></td>';
																									$message .= '</tr>';
																								$message .= '</table>';
																							$message .= '</td>';
																						$message .= '</tr>';
																					$message .= '</table>';
																				$message .= '</td>';
																				$message .= '<td class="col-spacer" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; width: 24px;" valign="top"></td>';
																				$message .= '<td class="col text-center va-top" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif;" align="center" valign="top">';
																					$message .= '<table cellspacing="0" cellpadding="0" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; border-collapse: collapse; width: 100%;">';
																						$message .= '<tr>';
																							$message .= '<td class="text-left font-sm pb-xs" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; font-size: 13px; padding-bottom: 4px;" align="left">No response</td>';
																							$message .= '<td class="text-right font-sm pb-xs text-muted" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; color: #9eb0b7; font-size: 13px; padding-bottom: 4px;" align="right">'.$no_response_count.'</td>';
																						$message .= '</tr>';
																						$message .= '<tr>';
																							$message .= '<td colspan="2" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif;">';
																								$message .= '<table class="chart" cellpadding="0" cellspacing="0" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; border-collapse: collapse; width: 100%; table-layout: fixed;">';
																									$message .= '<tr>';
																										$message .= '<td width="'.($no_response_count/$total_count*100)."%".'" class="chart-percentage bg-orange" style="height: 6px; font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; font-size: 12px; padding-left: 5px; border-radius: 2px 0 0 2px; color: #fff;" bgcolor="#fd9644">'.round($no_response_count/$total_count*100, 1)."%".'</td>';
																										$message .= '<td width="'.((1 - $no_response_count/$total_count)*100)."%".'" class="chart-percentage bg-orange-lightest" style="height: 6px; font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; font-size: 0; border-radius: 0 2px 2px 0;" bgcolor="#fff5ec"></td>';
																									$message .= '</tr>';
																								$message .= '</table>';
																							$message .= '</td>';
																						$message .= '</tr>';
																					$message .= '</table>';
																				$message .= '</td>';
																				$message .= '<td class="col-spacer" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; width: 24px;" valign="top"></td>';
																				$message .= '<td class="col text-center va-top" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif;" align="center" valign="top">';
																					$message .= '<table cellspacing="0" cellpadding="0" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; border-collapse: collapse; width: 100%;">';
																						$message .= '<tr>';
																							$message .= '<td class="text-left font-sm pb-xs" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; font-size: 13px; padding-bottom: 4px;" align="left">Absent</td>';
																							$message .= '<td class="text-right font-sm pb-xs text-muted" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; color: #9eb0b7; font-size: 13px; padding-bottom: 4px;" align="right">'.$absence_count.'</td>';
																						$message .= '</tr>';
																						$message .= '<tr>';
																							$message .= '<td colspan="2" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif;">';
																								$message .= '<table class="chart" cellpadding="0" cellspacing="0" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; border-collapse: collapse; width: 100%; table-layout: fixed;">';
																									$message .= '<tr>';
																										$message .= '<td width="'.($absence_count/$total_count*100)."%".'" class="chart-percentage bg-red" style="height: 6px; font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; font-size: 12px; padding-left: 5px; border-radius: 2px 0 0 2px; color: #fff;" bgcolor="#cd201f">'.round($absence_count/$total_count*100, 1)."%".'</td>';
																										$message .= '<td width="'.((1 - $absence_count/$total_count)*100)."%".'" class="chart-percentage bg-red-lightest" style="height: 6px; font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; font-size: 0; border-radius: 0 2px 2px 0;" bgcolor="#fae9e9"></td>';
																									$message .= '</tr>';
																								$message .= '</table>';
																							$message .= '</td>';
																						$message .= '</tr>';
																					$message .= '</table>';
																				$message .= '</td>';
																			$message .= '</tr>';
																		$message .= '</table>';
																		$message .= '<h2 class="mt-xl border-bottom" style="font-weight: 300; font-size: 24px; line-height: 130%; border-bottom-width: 1px; border-bottom-color: #f0f0f0; border-bottom-style: solid; margin: 48px 0 .5em;">Attendance list</h2>';

																			foreach ($attendance_list as $instrument => $status_list)
																			{
																				$message .= '<h5 class="mt-xl" style="font-weight: 600; font-size: 14px; margin: 48px 0 .5em;">'.$instrument.'</h5>';
																					$message .= '<table class="list" cellspacing="0" cellpadding="0" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; border-collapse: collapse; width: 100%;">';

																					foreach ($status_list as $status)
																					{
																						$message .= '<tr class="list-item">';
																							$message .= '<td class="pr-md w-1p" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; padding-top: 0; padding-bottom: 8px; width: 1%; padding-right: 16px;">';
																								$message .= '<img src="'.$config["base_url"].'/emails/example/assets/icons-green-check.png" class=" va-middle" width="18" height="18" alt="check" style="line-height: 100%; outline: none; text-decoration: none; vertical-align: middle; font-size: 0; border-width: 0;" />';
																							$message .= '</td>';
																							$message .= '<td style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; padding-top: 0; padding-bottom: 8px;">';
																								$message .= '<a href="https://tabler.io/emails?utm_source=demo" class="text-default" style="color: #444; text-decoration: none;">'.$status->first_name." ".$status->last_name.'</a>';
																							$message .= '</td>';
																							$message .= '<td class="font-sm text-muted w-auto text-right" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; padding-top: 0; padding-bottom: 8px; color: #9eb0b7; width: auto; font-size: 13px;" align="right">'.findTimeAgo($status->edit_datetime).'</td>';
																						$message .= '</tr>';
																					}																

																				$message .= '</table>';
																			}

																		$message .= '<h2 class="mt-xl border-bottom" style="font-weight: 300; font-size: 24px; line-height: 130%; border-bottom-width: 1px; border-bottom-color: #f0f0f0; border-bottom-style: solid; margin: 48px 0 .5em;">No responses</h2>';

																			foreach ($no_response_list as $instrument => $status_list)
																			{
																				$message .= '<h5 class="mt-xl" style="font-weight: 600; font-size: 14px; margin: 48px 0 .5em;">'.$instrument.'</h5>';
																					$message .= '<table class="list" cellspacing="0" cellpadding="0" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; border-collapse: collapse; width: 100%;">';

																					foreach ($status_list as $status)
																					{
																						$message .= '<tr class="list-item">';
																							$message .= '<td class="pr-md w-1p" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; padding-top: 0; padding-bottom: 8px; width: 1%; padding-right: 16px;">';
																								$message .= '<img src="'.$config["base_url"].'/emails/example/assets/icons-gray-slash.png" class=" va-middle" width="18" height="18" alt="slash" style="line-height: 100%; outline: none; text-decoration: none; vertical-align: middle; font-size: 0; border-width: 0;" />';
																							$message .= '</td>';
																							$message .= '<td style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; padding-top: 0; padding-bottom: 8px;">';
																								$message .= '<a href="https://tabler.io/emails?utm_source=demo" class="text-default" style="color: #444; text-decoration: none;">'.$status->first_name." ".$status->last_name.'</a>';
																							$message .= '</td>';
																							$message .= '<td class="font-sm text-muted w-auto text-right" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; padding-top: 0; padding-bottom: 8px; color: #9eb0b7; width: auto; font-size: 13px;" align="right">'.findTimeAgo($status->edit_datetime).'</td>';
																						$message .= '</tr>';
																					}																

																				$message .= '</table>';
																			}

																		$message .= '<h2 class="mt-xl border-bottom" style="font-weight: 300; font-size: 24px; line-height: 130%; border-bottom-width: 1px; border-bottom-color: #f0f0f0; border-bottom-style: solid; margin: 48px 0 .5em;">Confirmed absences</h2>';

																			foreach ($absence_list as $instrument => $status_list)
																			{
																				$message .= '<h5 class="mt-xl" style="font-weight: 600; font-size: 14px; margin: 48px 0 .5em;">'.$instrument.'</h5>';
																					$message .= '<table class="list" cellspacing="0" cellpadding="0" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; border-collapse: collapse; width: 100%;">';

																					foreach ($status_list as $status)
																					{
																						$message .= '<tr class="list-item">';
																							$message .= '<td class="pr-md w-1p" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; padding-top: 0; padding-bottom: 8px; width: 1%; padding-right: 16px;">';
																								$message .= '<img src="'.$config["base_url"].'/emails/example/assets/icons-red-x.png" class=" va-middle" width="18" height="18" alt="x" style="line-height: 100%; outline: none; text-decoration: none; vertical-align: middle; font-size: 0; border-width: 0;" />';
																							$message .= '</td>';
																							$message .= '<td style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; padding-top: 0; padding-bottom: 8px;">';
																								$message .= '<a href="https://tabler.io/emails?utm_source=demo" class="text-default" style="color: #444; text-decoration: none;">'.$status->first_name." ".$status->last_name.'</a>';
																							$message .= '</td>';
																							$message .= '<td class="font-sm text-muted w-auto text-right" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; padding-top: 0; padding-bottom: 8px; color: #9eb0b7; width: auto; font-size: 13px;" align="right">'.findTimeAgo($status->edit_datetime).'</td>';
																						$message .= '</tr>';
																					}																

																				$message .= '</table>';
																			}

																		
																	$message .= '</td>';
																$message .= '</tr>';
															$message .= '</table>';
														$message .= '</td>';
													$message .= '</tr>';
												$message .= '</table>';
											$message .= '</div>';
											$message .= '<table cellspacing="0" cellpadding="0" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; border-collapse: collapse; width: 100%;">';
												$message .= '<tr>';
													$message .= '<td class="py-xl" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; padding-top: 48px; padding-bottom: 48px;">';
														$message .= '<table class="font-sm text-center text-muted" cellspacing="0" cellpadding="0" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; border-collapse: collapse; width: 100%; color: #9eb0b7; text-align: center; font-size: 13px;">';
															$message .= '<tr>';
																$message .= '<td class="px-lg" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; padding-right: 24px; padding-left: 24px;">';
																	$message .= 'If you have any questions, feel free to message us at <a href="mailto:'.$config["admin_email"].'" class="text-muted" style="color: #9eb0b7; text-decoration: none;">'.$config["admin_email"].'</a>.';
																$message .= '</td>';
															$message .= '</tr>';
															$message .= '<tr>';
																$message .= '<td class="pt-md" style="font-family: Open Sans,-apple-system,BlinkMacSystemFont,Roboto,Helvetica Neue,Helvetica,Arial,sans-serif; padding-top: 16px;">';
																	$message .= 'You are receiving this email because you are an administrator of Pollererer. <a href="'.$config["base_url"].'/emails" class="text-muted" style="color: #9eb0b7; text-decoration: none;">Unsubscribe (NOT YET IMPLMENTED)</a>';
																$message .= '</td>';
															$message .= '</tr>';
														$message .= '</table>';
													$message .= '</td>';
												$message .= '</tr>';
											$message .= '</table>';
										$message .= '</td>';
									$message .= '</tr>';
								$message .= '</table>';
								$message .= '<!--[if (gte mso 9)|(IE)]>';
						$message .= '</td>';
					  $message .= '</tr>';
					$message .= '</table>';
						$message .= '<![endif]-->';
							$message .= '</td>';
						$message .= '</tr>';
					$message .= '</table>';
				$message .= '</center>';
			$message .= '</body>';
			$message .= '';
			$message .= '</html>';

			$mail = new PHPMailer(true);

			try
			{
				$mail->isSMTP();
				$mail->Host       = $config["smtp_host"];
				$mail->SMTPAuth   = true;
				$mail->Username   = $config["smtp_username"];
				$mail->Password   = $config["smtp_password"];
				$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
				$mail->Port       = $config["smtp_port"];

				$mail->setFrom($config["email_from"], $config["software_name"]);
				foreach (explode(", ", $to) as $address)
				{
					$mail->addAddress($address);
				}
				$mail->addCC("nsworep@nsw.org.uk");

				if (count($config["email_pdf"]) >= 1)
				{
					if (in_array($ensemble_ID, $config["email_pdf"]))
					{
						$mail->addAttachment(generate_seating_plan_PDF($ensemble_ID, $term_date_ID));
					}
				}

				$mail->isHTML(true);
				$mail->Subject = $subject;
				$mail->Body = $message;

				$mail->send();

				echo "Pre-rehearsal email succeeded.";

				if ($been_sent_check->num_rows == 0)
				{
					$been_sent_update = $db_connection->query("INSERT INTO `pre-rehearsal-email` (`been_sent`, `ensemble_ID`, `term_date_ID`, `message_content`) VALUES ('1', '".$ensemble_ID."', '".$term_date_ID."', '".base64_encode($message)."');");
				}
				else
				{
					$been_sent_update = $db_connection->query("UPDATE `pre-rehearsal-email` SET `been_sent` = '1', `message_content`='".base64_encode($message)."' WHERE `pre-rehearsal-email`.`ensemble_ID` = '".$ensemble_ID."' AND `pre-rehearsal-email`.`term_date_ID` = '".$term_date_ID."';");
				}
			}
			catch (Exception $e)
			{
				echo "Pre-rehearsal email failed; ".$mail->ErrorInfo;
			}

			// echo $message;
		}
	}

	db_disconnect($db_connection);
?>