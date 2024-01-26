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
$dsn = 'mysql:host=localhost;dbname=todo';
$user = 'root';
$pass = 'posnose90';

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
/*
// recibos los datos por json
$jsonData = file_get_contents('php://input');

$data = json_decode($jsonData, true);

// Check if JSON decoding was successful
if ($data === null) {
    $respuesta['error'] = 'Error decoding JSON data';
    echo json_encode($respuesta);
    exit;
}
*/
// comprobamos si viene el id_usuario como parámetro GET
if (!isset($_GET['id_usuario'])) {
    $respuesta['error'] = 'No se ha recibido el id usuario';
    echo json_encode($respuesta);
    exit;
}

// Access the data from the GET parameters
$idUsuarioTarea = $_GET['id_usuario'];


$sql = "SELECT id_tarea, nombre, completada 
FROM tarea WHERE id_usuario = :id_usuario";

// preparamos la sentencia SQL
$stmt = $conexionBBDD->prepare($sql);

// ejecutamos la sentencia SQL
$stmt->execute([
    'id_usuario' => $idUsuarioTarea
]);

$tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);


if (count($tareas)>= 0) {
    $respuesta['success'] = true;
    $respuesta['data'] = $tareas;
} else {
    $respuesta['success'] = false;
    $respuesta['error'] = 'No se ha podido insertar la tarea';
}

echo json_encode($respuesta);
?>