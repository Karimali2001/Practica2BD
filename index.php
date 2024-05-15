<?php
$serverName = "WO\SQLEXPRESS";
$connectionOptions = array(
    "Database" => "ComprasDB",
    "Uid" => "sa",
    "PWD" => "1234"
);

//Establishes the connection
$conn = sqlsrv_connect($serverName, $connectionOptions);

if($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Define your routes
$routes = [
    '/getlineas' => function() use ($conn) {
        $tsql= "SELECT * FROM Lineas ORDER BY CodLinea";
        $getResults= sqlsrv_query($conn, $tsql);
        if ($getResults == FALSE) {
            die(print_r(sqlsrv_errors(), true));
        }

        $data = array();
        while ($row = sqlsrv_fetch_array($getResults, SQLSRV_FETCH_ASSOC)) {
            $data[] = $row;
        }
        sqlsrv_free_stmt($getResults);

        // Add the CORS headers
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Origin, Authorization');

        header('Content-Type: application/json');
        echo json_encode($data);
    },
    // Add more routes here...
];

// Get the path from the URL
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// If the path matches a route, run the associated function
if (isset($routes[$path])) {
    $routes[$path]();
} else {
    http_response_code(404);
    echo 'Not found';
}
?>