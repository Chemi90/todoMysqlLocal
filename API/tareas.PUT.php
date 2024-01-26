<?php
// Cabecera JSON
header('Content-Type: application/json');

// Respuesta por defecto
$respuesta = [
    'success' => false,
    'data' => null,
    'error' => ''
];

// Conexión a la base de datos con PDO
$dsn = 'mysql:host=localhost;dbname=todo';
$user = 'root';
$pass = 'posnose90';

try {
    $conexionBBDD = new PDO($dsn, $user, $pass);
    $conexionBBDD->exec("set names utf8");
} catch (PDOException $e) {
    $respuesta['error'] = 'No se ha podido conectar con la base de datos: ' . $e->getMessage();
    echo json_encode($respuesta);
    exit;
}

// Comprobar si se reciben el id_usuario e id_tarea como parámetros GET
if (!isset($_GET['id_usuario']) || !isset($_GET['id_tarea'])) {
    $respuesta['error'] = 'No se ha recibido el id_usuario o el id_tarea';
    echo json_encode($respuesta);
    exit;
}

$idUsuario = $_GET['id_usuario'];
$idTarea = $_GET['id_tarea'];

// Recibir los datos por JSON para el estado de completada
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

// Comprobar si el estado de completada está presente en los datos JSON
if ($data === null || !isset($data['completada'])) {
    $respuesta['error'] = 'Datos inválidos o faltantes para completada';
    echo json_encode($respuesta);
    exit;
}

$completada = (int)$data['completada']; // Convertir a entero

// Preparar la sentencia SQL para actualizar el estado de la tarea
$sql = "UPDATE tarea SET completada = :completada WHERE id_tarea = :id_tarea AND id_usuario = :id_usuario";

// Preparar y ejecutar la sentencia
$stmt = $conexionBBDD->prepare($sql);
$success = $stmt->execute([
    'id_tarea' => $idTarea,
    'id_usuario' => $idUsuario,
    'completada' => $completada
]);

// Comprobar si la actualización fue exitosa
if ($success) {
    $respuesta['success'] = true;
    $respuesta['data'] = "Estado de la tarea actualizado correctamente.";
} else {
    $respuesta['error'] = 'No se ha podido actualizar el estado de la tarea';
}

echo json_encode($respuesta);
?>
