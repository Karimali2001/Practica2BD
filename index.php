<?php
$serverName = "WO\SQLEXPRESS";
$connectionOptions = array(
    "Database" => "db_name",
    "Uid" => "WO\Karim",
    "PWD" => "password"
);

//Establishes the connection
$conn = sqlsrv_connect($serverName, $connectionOptions);

if($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

$tsql= "SELECT * FROM table_name";
$getResults= sqlsrv_query($conn, $tsql);
if ($getResults == FALSE) {
    die(print_r(sqlsrv_errors(), true));
}

while ($row = sqlsrv_fetch_array($getResults, SQLSRV_FETCH_ASSOC)) {
    echo $row['column_name'].", ";
}
sqlsrv_free_stmt($getResults);
?>