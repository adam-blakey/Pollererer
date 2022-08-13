<?php require($_SERVER['DOCUMENT_ROOT']."/includes/db_connect.php"); ?>

<?php
$db_connection = db_connect();

$filename_database_structure = "database_structure.sql";
$filename_database_data      = "database_data.sql";

///////////////////////////////
// IMPORT DATABASE STRUCTURE //
///////////////////////////////
$temp_line = "";
$lines = file($filename_database_structure);

foreach ($lines as $line)
{
    if ((substr($line, 0, 2) == "--") or (trim($line) == ""))
    {
        // ignore
    }
    else
    {
        $temp_line .= trim($line);

        if ($temp_line[-1] == ";")
        {
            $db_connection->query($temp_line);
            $temp_line = "";

            if ($db_connection->error)
            {
                echo "Error: " . $db_connection->error . "<br />";
            }
        }
    }
}

if ($db_connection->error)
{
    echo "Error: " . $db_connection->error;
    die();
}
else
{
    echo "Database structure loaded successfully.";   
}

///////////////////////////
// IMPORT DATABASE DATA //
//////////////////////////
$temp_line = "";
$lines = file($filename_database_data);

foreach ($lines as $line)
{
    if ((substr($line, 0, 2) == "--") or (trim($line) == ""))
    {
        // ignore
    }
    else
    {
        $temp_line .= trim($line);

        if ($temp_line[-1] == ";")
        {
            $db_connection->query($temp_line);
            $temp_line = "";

            if ($db_connection->error)
            {
                echo "Error: " . $db_connection->error . "<br />";
            }
        }
    }
}

if ($db_connection->error)
{
    echo "Error: " . $db_connection->error;
    die();
}
else
{
    echo "Database data loaded successfully.";   
}

db_disconnect($db_connection);

?>