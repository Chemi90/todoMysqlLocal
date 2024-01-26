<?php
// cabecera json
header('Content-Type: application/json');

// respuesta por defecto
$respuesta = [
    'success' => false,
    'data' => null,
    'error' => ''
];

// nos conectamos a la base de datos con PDO
$dsn = 'servidorxemi.mysql.database.azure.com';
$user = 'xemita';
$pass = 'Posnose90';

try {
    $conexionBBDD = new PDO($dsn, $user, $pass);
    // echo 'Conectado a la base de datos';
    // configuramos charset a utf8
    $conexionBBDD->exec("set names utf8");
} catch (PDOException $e) {
    $respuesta['error'] = 'No se ha podido conectar con la base de datos: '.
    $e->getMessage();
    echo json_encode($respuesta);
    exit;
}
/*
// comprobamos si recibimos datos por POST
if (!isset($_POST['usuario']) || !isset($_POST['clave'])) {
    $respuesta['error'] = 'No se ha recibido ningún dato';
    echo json_encode($respuesta);
    exit;
}
$usuarioLogeado = $_POST['usuario'];
$nombreTarea = $_POST['clave'];
*/

// recibos los datos por json
$jsonData = file_get_contents('php://input');

$data = json_decode($jsonData, true);

// Check if JSON decoding was successful
if ($data === null) {
    $respuesta['error'] = 'Error decoding JSON data';
    echo json_encode($respuesta);
    exit;
}
// comprobamos si viene el usuario y clave
if (!isset($data['id_usuario']) || !isset($data['nombre'])) {
    $respuesta['error'] = 'No se ha recibido el id usuario o la tarea';
    echo json_encode($respuesta);
    exit;
}

// Access the data from the JSON object
$idUsuarioTarea = $data['id_usuario'];
$nombreTarea = $data['nombre'];


$sql = "INSERT INTO tarea (id_usuario, nombre, completada) 
        VALUES (:id_usuario, :nombre, 0)";

// preparamos la sentencia SQL
$stmt = $conexionBBDD->prepare($sql);

// ejecutamos la sentencia SQL
$stmt->execute([
    'nombre' => $nombreTarea,
    'id_usuario' => $idUsuarioTarea
]);

// obtenemos el ultimo id insertado
$idTareaInsertada =$conexionBBDD->lastInsertId(); 


if ($idTareaInsertada) {
    $respuesta['success'] = true;
    $respuesta['data'] = [
        'id_tarea' => $idTareaInsertada,    
        'nombre' => $nombreTarea,
        'fecha' => date('Y-m-d H:i:s')
    ];
} else {
    $respuesta['success'] = false;
    $respuesta['error'] = 'No se ha podido insertar la tarea';
}

echo json_encode($respuesta);
?>