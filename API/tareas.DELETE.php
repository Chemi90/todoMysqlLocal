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
$dsn = 'servidorxemi.mysql.database.azure.com';
$user = 'xemita';
$pass = 'Posnose90';

try {
    $conexionBBDD = new PDO($dsn, $user, $pass);
    $conexionBBDD->exec("set names utf8");
} catch (PDOException $e) {
    $respuesta['error'] = 'No se ha podido conectar con la base de datos: ' . $e->getMessage();
    echo json_encode($respuesta);
    exit;
}

// Comprobar si se recibe el id_tarea como parámetro GET
if (!isset($_GET['id_tarea'])) {
    $respuesta['error'] = 'No se ha recibido el id de la tarea';
    echo json_encode($respuesta);
    exit;
}

// Acceder al id de la tarea desde los parámetros GET
$idTarea = $_GET['id_tarea'];

// Preparar la sentencia SQL para eliminar la tarea
$sql = "DELETE FROM tarea WHERE id_tarea = :id_tarea";

// Preparar y ejecutar la sentencia
$stmt = $conexionBBDD->prepare($sql);
$success = $stmt->execute(['id_tarea' => $idTarea]);

// Comprobar si la eliminación fue exitosa
if ($success) {
    $respuesta['success'] = true;
    $respuesta['data'] = "Tarea eliminada correctamente.";
} else {
    $respuesta['error'] = 'No se ha podido eliminar la tarea';
}

echo json_encode($respuesta);
?>
