<?php
$host = "localhost";
$usuario = "root";
$password = "";
$bd = "miPrimerAPI";

// Crear una conexión a la base de datos
$conexion = new mysqli($host, $usuario, $password, $bd);

// Verificar si hay un error de conexión
if ($conexion->connect_error) {
    die("Conexion no establecida: " . $conexion->connect_error);
}

// Establecer el tipo de contenido de la respuesta HTTP
header("Content-Type: application/json");

// Obtener el método de la solicitud HTTP (GET, POST, etc.)
$metodo = $_SERVER['REQUEST_METHOD'];
$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';

$buscarId = explode('/', $path);
$id = ($path !== '/') ? end($buscarId) : null;

// Manejar diferentes métodos de solicitud HTTP
switch ($metodo) {
    case 'GET':
        consultar($conexion);
        break;
    case 'POST':
        insertar($conexion);
        break;
    case 'PUT':
        actualizar($conexion, $id);
        break;
    case 'DELETE':
        eliminar($conexion, $id);
        break;
    default:
        echo "Método no permitido";
        break;
}

// Consultar datos de la BD
function consultar($conexion)
{
    $sql = "SELECT * FROM usuario";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        $datos = array();

        while ($fila = $resultado->fetch_assoc()) {
            $datos[] = $fila;
        }
        echo json_encode($datos);
    }
}

//Insertar datos en la BD
function insertar($conexion)
{
    $dato = json_decode(file_get_contents('php://input'), true);
    $nombre = $dato['nombre'];

    $sql = "INSERT INTO usuario (nombre) VALUES ('$nombre')";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        $dato['id'] = $conexion->insert_id;
        echo json_encode($dato);
    } else {
        echo json_encode(array('error' => 'error al crear el ususario'));
    }
}
//Elimiar datos de la BS

function eliminar($conexion, $id)
{
    $sql = "DELETE FROM usuario WHERE id = $id";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        echo json_encode(array('Mensaje' => 'Usiario eliminado'));
    } else {
        echo json_encode(array('Mensaje' => 'Error al eliminar el usuario'));
    }
}

function actualizar($conexion, $id)
{
    $dato = json_decode(file_get_contents('php://input'), true);
    $nombre = $dato['nombre'];

    echo "El id a actualizar es " . $id. " con el nombre ".$nombre;
    $sql = "UPDATE usuario SET nombre = '$nombre' WHERE id = $id";
    $resultado = $conexion->query($sql);

    if ($resultado) {
        echo json_encode(array('mensaje' => 'Usuario actualizado'));
    } else {
        echo json_encode(array('mensaje' => 'Error al actualizar usuario'));
    }
}
