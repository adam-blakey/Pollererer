<?php
    use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	require_once($_SERVER['DOCUMENT_ROOT']."/dist/libs/phpmailer/src/Exception.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/dist/libs/phpmailer/src/PHPMailer.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/dist/libs/phpmailer/src/SMTP.php");

	require_once($_SERVER['DOCUMENT_ROOT']."/includes/kernel.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/cron/generate-seating-plan-pdf.php");
	
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
		$mail->addAddress($config["admin_email"]);

		//$mail->isHTML(true);
		$mail->Subject = "Test email";
		$mail->Body = "If you've received this, then the test has worked.";

		$mail->send();

		echo "Test email succeeded.";

	}
	catch (Exception $e)
	{
		echo "Test email failed.";
		echo "Mailer Error: " . $mail->ErrorInfo;
	}
    	
?>