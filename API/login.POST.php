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

// Recibir los datos por JSON
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

// Verificar si la decodificación de JSON fue exitosa y si los campos necesarios están presentes
if ($data === null || !isset($data['nombre']) || !isset($data['usuario']) || !isset($data['clave'])) {
    $respuesta['error'] = 'Datos inválidos o faltantes';
    echo json_encode($respuesta);
    exit;
}

// Acceder a los datos del objeto JSON
$nombre = $data['nombre'];
$usuarioLogeado = $data['usuario'];
$usuarioClave = $data['clave'];

// Preparar la sentencia SQL para insertar el nuevo usuario
$sql = "INSERT INTO usuarios (nombre, usuario, clave) VALUES (:nombre, :usuario, :clave)";

// Preparar y ejecutar la sentencia
$stmt = $conexionBBDD->prepare($sql);
$success = $stmt->execute([
    'nombre' => $nombre,
    'usuario' => $usuarioLogeado,
    'clave' => $usuarioClave
]);

// Comprobar si la inserción fue exitosa
if ($success) {
    $respuesta['success'] = true;
    $respuesta['data'] = 'Usuario agregado correctamente';
} else {
    $respuesta['error'] = 'No se ha podido agregar el usuario';
}

echo json_encode($respuesta);
?>
