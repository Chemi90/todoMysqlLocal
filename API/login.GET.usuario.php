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
if (!isset($data['usuario']) || !isset($data['clave'])) {
    $respuesta['error'] = 'No se ha recibido el usuario o la clave';
    echo json_encode($respuesta);
    exit;
}

// Access the data from the JSON object
$usuarioLogeado = $data['usuario'];
$usuarioClave = $data['clave'];


$sql = "SELECT id_usuario, nombre 
    FROM usuarios 
    WHERE usuario = :usuario 
    AND clave = :clave";

// preparamos la sentencia SQL
$stmt = $conexionBBDD->prepare($sql);

// ejecutamos la sentencia SQL
$stmt->execute([
    'clave' => $usuarioClave,
    'usuario' => $usuarioLogeado
]);

// obtenemos resultados
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

if ($resultado) {
    $respuesta['success'] = true;
    $respuesta['data'] = $resultado;    
} else {
    $respuesta['success'] = false;
    $respuesta['error'] = 'Usuario o clave incorrecta';
}

echo json_encode($respuesta);
?>