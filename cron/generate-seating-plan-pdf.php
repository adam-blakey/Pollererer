<?php
	require_once($_SERVER['DOCUMENT_ROOT']."/includes/kernel.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/dist/libs/tcpdf/tcpdf.php");

	// $ensemble_ID  = (isset($_GET["ensemble_ID"]))?intval($_GET["ensemble_ID"]):0;
 //  $term_date_ID = (isset($_GET["term_date_ID"]))?intval($_GET["term_date_ID"]):0;

	function generate_seating_plan_PDF($ensemble_ID, $term_date_ID)
	{
		// Extend the TCPDF class to create custom Header and Footer
		class SeatingPDF extends TCPDF {

		    //Page header
		    public function Header() {
		        // Logo
		        $image_file = $config['logo_url'];
		        //$image_file = K_PATH_IMAGES.'logo_example.jpg';
		        $this->Image($image_file, 10, 10, 55, '', 'png', '', 'T', false, 300, '', false, false, 0, false, false, false);
		        // Set font
		        $this->SetFont('helvetica', 'B', 20);
		    }

		    // Page footer
		    public function Footer() {
		        // Position at 15 mm from bottom
		        $this->SetY(-15);
		        // Set font
		        $this->SetFont('helvetica', 'I', 8);
		        // Page number
		        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		    }

		    // Table
		    public function SeatingTable($names, $instruments, $attendance) {
						require($_SERVER['DOCUMENT_ROOT']."/config.php");

		        // Colors, line width and bold font
		        $this->SetFillColor(192, 30, 67);
		        $this->SetTextColor(255);
		        $this->SetDrawColor(0, 0, 0);
		        $this->SetLineWidth(0.5);
		        $this->SetFont('', '');
		        $this->setCellMargins(null, null, null, null);	

		        $table_width = 275;
		        $spacing     = 10;
		        $height      = 6;

		        // Header
		        $num_rows = count($names[0]);
		        for ($j=0; $j < $num_rows; ++$j)
		        {
		        	$row_attendance = 0;
		        	for ($i=0; $i < count($names); ++$i)
		        	{
		        		if ($attendance[$i][$j] == "1")
		        		{
		        			$row_attendance++;
		        		}
		        	}

		            $this->Cell($table_width/($num_rows)-$height-$spacing, $height+1, 'Row '.($j+1), 1,  0, 'C', 1);
		            $this->Cell($height,  $height+1, $row_attendance, 'LRTB', 0, 'C', 1);
		            $this->Cell($spacing, $height+1, '',              '',     0, 'C', 0);
		        }
		        $this->Ln();

		        // Names
		        $previous_instrument = $instruments[0];
		        $this->SetTextColor(0);
		        for ($i=0; $i < count($names); ++$i)
		        {
		        		$column = $names[$i];

		        		for ($j=0; $j < count($names[0]); ++$j)
		        		{
		        			$border  = '';
		        			$border .= ($column[$j] != NULL and $column[$j] != '')?'L':'';
			        		$border .= ($previous_instrument[$j] != $instruments[$i][$j])?'T':'';
			        		$border .= ($i == count($names)-1 and $column[$j] != NULL and $column[$j] != '')?'B':'';

		        			if ($column[$j] == NULL or $column[$j] == '')
		        			{        				
		        				$this->Cell($table_width/($num_rows)-$spacing, $height, $column[$j], $border, 0, 'L');
		        				$this->Cell($spacing,                 $height, '',          '',      0, 'L');
		        			}
		        			else
		        			{
			            	// $this->Cell($table_width/($num_rows)-$height-$spacing, $height, '<s>'.$column[$j].'</s>', $border, 0, 'L');
			            	// $this->writeHTMLCell($height, $height, '', '', '<img src="'.$config["base_url"].'/emails/example/assets/icons-green-check.png" class=" va-middle" width="12" height="12" alt="check" style="outline: none; text-decoration: none; vertical-align: middle; font-size: 0; border-width: 0;" />', 'L', 0, 0, 1, 'L', 0);
			            	if ($attendance[$i][$j] == "0")
			            	{
			            		$this->writeHTMLCell($table_width/($num_rows)-$height-$spacing, $height, '', '', '<span style="color: #d63939;"><s>'.$column[$j].'</s></span>', $border, 0, 0, 1, 'L');
			            	}
			            	else if ($attendance[$i][$j] == "1")
			            	{
			            		$this->writeHTMLCell($table_width/($num_rows)-$height-$spacing, $height, '', '', '<span style="color: #74b816;">'.$column[$j].'</span>', $border, 0, 0, 1, 'L');
			            	}	
			            	else
			            	{
											if ($config["assume_attending"])
											{
												$this->writeHTMLCell($table_width/($num_rows)-$height-$spacing, $height, '', '', '<span style="color: #74b816;">'.$column[$j].'</span>', $border, 0, 0, 1, 'L');
											}
											else
											{
												$this->writeHTMLCell($table_width/($num_rows)-$height-$spacing, $height, '', '', '<span style="color: #626976;">'.$column[$j].' ??</span>', $border, 0, 0, 1, 'L');
											}
			            	}
			            	$this->Cell($height,  $height, '', 'LRTB',   0, 'L');
			            	$this->Cell($spacing, $height, '', '',       0, 'L');
			            }
		            }
		            $this->Ln();

		            $previous_instrument = $instruments[$i];
		        }
		    }
		}

		// create new PDF document
		$pdf = new SeatingPDF('L', 'mm', 'A4', true, 'UTF-8', false, false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor($config["software_name"]);

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		    require_once(dirname(__FILE__).'/lang/eng.php');
		    $pdf->setLanguageArray($l);
		}

		// ---------------------------------------------------------

		// set font
		$pdf->SetFont('helvetica', '', 11);

		// add a page
		$pdf->AddPage();

		$db_connection = db_connect();

		$ensemble_query = $db_connection->query("SELECT `name` FROM `ensembles` WHERE `ID`='".$ensemble_ID."'");
		$ensemble_name = $ensemble_query->fetch_array()[0];

		$term_date_query = $db_connection->query("SELECT `datetime` FROM `term_dates` WHERE `ID`='".$term_date_ID."'");
		$term_date_date = new DateTime();
		$term_date_date ->setTimestamp($term_date_query->fetch_array()[0]);
		$term_date_date ->setTimezone(new DateTimeZone('Europe/London'));

		$pdf->SetTitle('Rehearsal Seating for NSWO on '.$term_date_date->format("jS F"));
		$pdf->SetSubject('Rehearsal Seating for NSWO on '.$term_date_date->format("jS F"));

		$seats_query = $db_connection->query("SELECT `members`.`ID` AS `member_ID`, `first_name`, `last_name`, `instrument`, `row`, `seat` FROM `members` INNER JOIN `members-ensembles` ON `members`.`ID`=`members-ensembles`.`member_ID` WHERE `members-ensembles`.`ensemble_ID`='".$ensemble_ID."' AND `members`.`deleted`='0' ORDER BY `row`, `seat` ASC");

		$names = [[]];
		$instruments = [[]];
		$attendance = [[]];

		$total_attendance = 0;
		while($member = $seats_query->fetch_assoc())
		{
			$i = $member["row"];
			$j = $member["seat"];
			if ($i > 0 and $j > 0)
			{
				$names      [$j-1][$i-1] = $member["first_name"]." ".substr($member["last_name"], 0, 1);
				$instruments[$j-1][$i-1] = $member["instrument"];

				$attendance_query = $db_connection->query("SELECT `status`, `edit_datetime` FROM `attendance` WHERE `member_ID`='".$member['member_ID']."' AND `ensemble_ID`='".$ensemble_ID."' AND `term_dates_ID`='".$term_date_ID."' ORDER BY `edit_datetime` DESC LIMIT 1");

				if ($attendance_query->num_rows == 0)
				{
					$attendance[$j-1][$i-1] = NULL;
				}
				else
				{
					$attendance[$j-1][$i-1] = $attendance_query->fetch_assoc()["status"];

					if ($attendance[$j-1][$i-1] == "1")
					{
						$total_attendance++;
					}
				}
			}
		}

		// HTML content
		// $html = '
		// <strong>Date:</strong> '.$term_date_date->format("j/m/y").' <br />
		// <strong>Ensemble:</strong> '.$ensemble_name.' <br />
		// <strong>Confirmed attendees:</strong> '.$total_attendance.' <br />

		// <h1>Rehearsal Seating for '.$ensemble_name.' on '.$term_date_date->format("jS F Y @ H:i:s").'</h1>
		// <br />
		// ';

		$html = '
		<strong>Date:</strong> '.$term_date_date->format("j/m/y").' <br />
		<strong>Ensemble:</strong> '.$ensemble_name.' <br />
		<strong>Confirmed attendees:</strong> '.$total_attendance.' <br />

		<h1>Rehearsal Seating for '.$ensemble_name.' on '.$term_date_date->format("jS F Y @ H:i:s").'</h1>
		<br />
		';

		// output HTML
		$pdf->writeHTML($html, true, false, true, false, '');
		// $pdf->SeatingTable(
		// 	array('Row 1 [8]', 'Row 2 [13]', 'Row 3 [8]', 'Row 4 [16]', 'Row 5 [4]'),
		// 	array(
		// 		array('Alison', 'Nikki', 'Dave', 'Gemma', 'Jon'),
		// 		array('Nicola', 'Rachel', 'Joe', 'Mel', 'Catriona'),
		// 		array('Megan', 'Amanda', 'Ros', 'Sarah', 'Gareth'),
		// 		array('Laura', 'Hannah', 'Jo', 'Gill', 'Mike'),
		// 		array('Vicky', 'Hannah T', 'Catherine', 'Phil', '')
		// 	),
		// 	array(
		// 		array('Flute', 'Flute', 'Bass Clarinet', 'French Horn', 'Percussion'),
		// 		array('Flute', 'Flute', 'Bass Clarinet', 'French Horn', 'Percussion'),
		// 		array('Flute', 'Flute', 'Alto Clarinet', 'French Horn', 'Percussion'),
		// 		array('Flute', 'Saxophone', 'Alto Clarinet', 'French Horn', 'Percussion'),
		// 		array('Oboe', 'Saxophone', 'Clarinet', 'Trumpet', '')
		// 	)
		// );

		// REALLY HACKY WAY OF DOING THINGS.
		$num_cols = max(array_keys($attendance))+1;
		$num_rows = 0;
		for ($i=0; $i < $num_cols; ++$i)
		{
			if (max(array_keys($attendance[$i])) > $num_rows)
			{
				$num_rows = max(array_keys($attendance[$i]));
			}
		}
		$num_rows += 1;
		for ($j=0; $j < $num_rows; ++$j)
		{
			for ($i=0; $i < $num_cols; ++$i)
			{
				if(!isset($names[$i][$j]))
				{
					$names      [$i][$j] = '';
					$instruments[$i][$j] = '';
					$attendance [$i][$j] = NULL;
				}
			}
		}

		$pdf->SeatingTable(
			$names,
			$instruments,
			$attendance
		);

		db_disconnect($db_connection);

		// ---------------------------------------------------------

		//Close and save PDF document
		$filename = $_SERVER['DOCUMENT_ROOT'].'/seating-plans/'.'seating_'.$ensemble_name.'_'.$term_date_date->format("Ymj").'.pdf';
		$pdf->Output($filename, 'F');

		//echo '<pre>'; print_r($names); echo '</pre>';

		return $filename;
	}

	//generate_seating_plan_PDF(3, 3);

	//echo $message;
?>