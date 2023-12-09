<?php

// Establecimiento de la conexión a la base de datos
$host="localhost";
$usuario="root";
$passoword="";
$basededatos="api";

$conexion= new mysqli($host,$usuario,$passoword,$basededatos);

if($conexion->connect_error){
   die ("Conexion no establecida". $conexion->connect_error);
}

// Configuración del encabezado para respuesta JSON
header("Content-Type: application/json");

// Obtención del método HTTP y la ruta
$metodo= $_SERVER['REQUEST_METHOD'];
$path= isset($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:'/';

$buscarId = explode('/', $path);
$id= ($path!=='/') ? end($buscarId):null;

// Manejo de las diferentes operaciones según el método HTTP
switch($metodo){

    // Consulta Selecta
    case 'GET':
        consulta($conexion, $id);
        break;

    // Inserción
    case 'POST':
        insertar($conexion);
        break;

    // Actualización
    case 'PUT':
        actualizar($conexion, $id); 
        break;

    // Eliminación
    case 'DELETE':
        borrar($conexion, $id);
        break;

    default:
        echo "Metodo no permitido";
        break;
}

// Función para realizar consultas
function consulta($conexion, $id){
    // Construcción de la consulta según si se proporciona un ID o no
    $sql=($id===null) ? "SELECT * FROM usuarios":"SELECT * FROM usuarios WHERE id=$id";
    $resultado= $conexion->query($sql);

    // Si hay resultados, se formatean como JSON y se devuelven
    if($resultado){
        $datos= array();
        while($fila= $resultado->fetch_assoc()){
            $datos[]= $fila;
        }
        echo json_encode($datos);
    }
}

// Función para insertar datos
function insertar($conexion){
    $dato= json_decode(file_get_contents('php://input'), true);
    $nombre= $dato['nombre'];

    $sql= "INSERT INTO usuarios(nombre) VALUES ('$nombre')";
    $resultado= $conexion->query($sql);

    // Si la inserción tiene éxito, se devuelve el nuevo registro en formato JSON
    if($resultado){
        $dato['id'] = $conexion->insert_id;
        echo json_encode($dato);
    }else{
        echo json_encode(array('error'=> 'Error al crear usuario'));
    }
}

// Función para eliminar datos
function borrar($conexion, $id){
    if ($id !== null) {
        $sql = "DELETE FROM usuarios WHERE id = $id";
        $resultado = $conexion->query($sql);

        // Se devuelve un mensaje indicando si la eliminación tuvo éxito o no
        if ($resultado) {
            echo json_encode(array('mensaje' => 'Usuario borrado'));
        } else {
            echo json_encode(array('error' => 'Error al borrar usuario'));
        }
    }
}

// Función para actualizar datos
function actualizar($conexion, $id){
    $dato = json_decode(file_get_contents('php://input'), true);
    $nombre = $dato['nombre'];

    $sql = "UPDATE usuarios SET nombre = '$nombre' WHERE id = $id";
    $resultado = $conexion->query($sql);

    // Se devuelve un mensaje indicando si la actualización tuvo éxito o no
    if ($resultado) {
        echo json_encode(array('mensaje' => 'Usuario Actualizado'));
    } else {
        echo json_encode(array('error' => 'Error al actualizar el usuario'));
    }
}

?>
