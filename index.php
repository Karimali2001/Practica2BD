<?php



// Add the CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Send the preflight response
    http_response_code(200);
    die();
}


$serverName = "WO\SQLEXPRESS";
$connectionOptions = array(
    "Database" => "ComprasDB",
    "Uid" => "sa",
    "PWD" => "1234"
);

//Establishes the connection
$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Define your routes
$routes = [
    '/getlineas' => function () use ($conn) {
        $tsql = "SELECT * FROM Lineas ORDER BY CodLinea";
        $getResults = sqlsrv_query($conn, $tsql);
        if ($getResults == FALSE) {
            die (print_r(sqlsrv_errors(), true));
        }

        $data = array ();
        while ($row = sqlsrv_fetch_array($getResults, SQLSRV_FETCH_ASSOC)) {
            $data[] = $row;
        }
        sqlsrv_free_stmt($getResults);

        header('Content-Type: application/json');
        echo json_encode($data);
    },
    '/postlineas' => function () use ($conn) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die ('Method Not Allowed');
        }

        $input = json_decode(file_get_contents('php://input'), true);

        // Log the input
        error_log(print_r($input, true));

        if (!isset ($input['CodLinea'], $input['DescripcionL'])) {
            http_response_code(400);
            die ('Bad Request');
        }

        $tsql = "INSERT INTO Lineas (CodLinea, DescripcionL) VALUES (?, ?)";
        $params = [$input['CodLinea'], $input['DescripcionL']];
        $getResults = sqlsrv_query($conn, $tsql, $params);

        if ($getResults == FALSE) {
            // Si la inserción falla, devuelve un código de estado HTTP 500
            http_response_code(500);
            die (print_r(sqlsrv_errors(), true));
        }

        sqlsrv_free_stmt($getResults);

        http_response_code(201);
        echo json_encode(['message' => 'Linea created successfully']);
    },
    '/deletelineas' => function () use ($conn) {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            http_response_code(405);
            die ('Method Not Allowed');
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset ($input['CodLinea'])) {
            http_response_code(400);
            die ('Bad Request');
        }

        $tsql = "DELETE FROM Lineas WHERE CodLinea = ?";
        $params = [$input['CodLinea']];
        $getResults = sqlsrv_query($conn, $tsql, $params);

        if ($getResults == FALSE) {
            http_response_code(500);
            die (print_r(sqlsrv_errors(), true));
        }

        sqlsrv_free_stmt($getResults);

        http_response_code(200);
        echo json_encode(['message' => 'Linea deleted successfully']);
    },
    '/updatelineas' => function () use ($conn) {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset ($input['CodLinea']) || !isset ($input['DescripcionL'])) {
            http_response_code(400);
            die ('Bad Request');
        }

        $tsql = "UPDATE Lineas SET DescripcionL = ? WHERE CodLinea = ?";
        $params = [$input['DescripcionL'], $input['CodLinea']];
        $getResults = sqlsrv_query($conn, $tsql, $params);

        if ($getResults == FALSE) {
            http_response_code(500);
            die (print_r(sqlsrv_errors(), true));
        }

        sqlsrv_free_stmt($getResults);

        http_response_code(200);
        echo json_encode(['message' => 'Linea updated successfully']);
    },

    '/getarticulos/(\d+)' => function ($CodLinea) use ($conn) {
        $tsql = "SELECT * FROM Articulos WHERE CodLinea = ?";
        $params = [$CodLinea];
        $getResults = sqlsrv_query($conn, $tsql, $params);

        if ($getResults == FALSE) {
            http_response_code(500);
            die (print_r(sqlsrv_errors(), true));
        }

        $articulos = [];
        while ($row = sqlsrv_fetch_array($getResults, SQLSRV_FETCH_ASSOC)) {
            $articulos[] = $row;
        }
        sqlsrv_free_stmt($getResults);

        if (empty ($articulos)) {
            http_response_code(404);
            echo json_encode(['message' => 'Articulos not found']);
        } else {
            http_response_code(200);
            echo json_encode($articulos);
        }
    },
    // POST method to create a new article
    '/postarticulo' => function () use ($conn) {
        $data = json_decode(file_get_contents('php://input'), true);

        // Log the data to the error log
        error_log(print_r($data, true));

        $tsql = "INSERT INTO Articulos (CodArticulo, Descripcion, CodLinea, Precio, Existencia, Maximo, Minimo, StatusA, FechaDesincorporacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [$data['CodArticulo'], $data['Descripcion'], $data['CodLinea'], $data['Precio'], $data['Existencia'], $data['Maximo'], $data['Minimo'], $data['StatusA'], $data['FechaDesincorporacion']];
        $getResults = sqlsrv_query($conn, $tsql, $params);

        if ($getResults == FALSE) {
            http_response_code(500);
            die (print_r(sqlsrv_errors(), true));
        }

        http_response_code(201);
        echo json_encode(['message' => 'Articulo created successfully']);
    },

    // DELETE method to delete an article
    '/deletearticulo' => function () use ($conn) {
        $request = json_decode(file_get_contents('php://input'), true);
        $CodArticulo = $request['CodArticulo'];

        // Log the received CodArticulo
        error_log("Received CodArticulo: " . $CodArticulo);

        $tsql = "DELETE FROM Articulos WHERE CodArticulo = ?";
        $params = [$CodArticulo];
        $getResults = sqlsrv_query($conn, $tsql, $params);

        if ($getResults == FALSE) {
            http_response_code(500);
            die (print_r(sqlsrv_errors(), true));
        }

        http_response_code(200);
        echo json_encode(['message' => 'Articulo deleted successfully']);
    },

    // PUT method to update an article
    '/updatearticulo' => function () use ($conn) {
        $request = json_decode(file_get_contents('php://input'), true);
        $CodArticulo = $request['CodArticulo'];
        $Descripcion = $request['Descripcion'];
        // Add other fields as necessary
    
        $tsql = "UPDATE Articulos SET Descripcion = ? WHERE CodArticulo = ?";
        $params = [$Descripcion, $CodArticulo];
        $getResults = sqlsrv_query($conn, $tsql, $params);

        if ($getResults == FALSE) {
            http_response_code(500);
            die (print_r(sqlsrv_errors(), true));
        }

        http_response_code(200);
        echo json_encode(['message' => 'Articulo updated successfully']);
    },

];

// Get the path from the URL
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Check each route for a match
foreach ($routes as $pattern => $function) {
    if (preg_match('#^' . $pattern . '$#', $path, $matches)) {
        array_shift($matches);  // Remove the full match from the start of the matches
        $function(...$matches);  // Call the function with the matches as parameters
        exit;
    }
}

// If no route was matched, return a 404
http_response_code(404);
echo 'Not found';
?>