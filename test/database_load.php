<?php require($_SERVER['DOCUMENT_ROOT']."/includes/db_connect.php"); ?>

<?php
$db_connection = db_connect();

$filename_database_structure = "database_structure.sql";
$filename_database_data      = "database_data.sql";

$temp_line = "";
$lines = file($filename_database_structure);
foreach ($lines as $line)
{
    if (substr($line, 0, 2) == "--")
    {
        // ignore
    }
    else
    {
        $temp_line .= trim($line);

        if ($line[strlen($line)-1] == ";")
        {
            $db_connection->query($temp_line);
            $temp_line = "";
        }
    }
}

echo "Success!";

db_disconnect($db_connection);


?>