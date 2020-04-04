<?php

//Auth with HTTP
/*
$user = array_key_exists( 'PHP_AUTH_USER', $_SERVER ) ? $_SERVER['PHP_AUTH_USER'] : '';
$pwd = array_key_exists( 'PHP_AUTH_PW', $_SERVER ) ? $_SERVER['PHP_AUTH_PW'] : '';
if( $user !== 'jorge' || $pwd !== '1234'){
    die;
}
*/

//Auth with HMAC

if(
    !array_key_exists('HTTP_X_HASH', $_SERVER) ||
    !array_key_exists('HTTP_X_TIMESTAMP', $_SERVER) ||
    !array_key_exists('HTTP_X_UID', $_SERVER)
) {
    die;
} 

list($hash, $uid, $timestamp) = [
    $_SERVER['HTTP_X_HASH'],
    $_SERVER['HTTP_X_UID'],
    $_SERVER['HTTP_X_TIMESTAMP']
];

$secret = 'Secret,GG';

$newHash = sha1($uid.$timestamp.$secret);

if( $newHash !== $hash){
    die;
}

//Definimos los recursos disponibles
$allowedResourceTypes = [
    'books',
    'authors',
    'genres'
];
//Validamos que el recurso está disponible
$resourceType = $_GET['resource_type'];

if(!in_array($resourceType, $allowedResourceTypes)){
    die;
}

// Defino los recursos
$books = [
    1 => [
        'titulo' => 'Lo que el viento se llevo',
        'id_autor' => 2,
        'id_genero' => 2,
    ],
    2 => [
        'titulo' => 'La Iliada',
        'id_autor' => 1,
        'id_genero' => 1,
    ],
    3 => [
        'titulo' => 'La Odisea',
        'id_autor' => 1,
        'id_genero' => 1,
    ],
];

// Se indica al cliente que lo que recibirá es un json
header('Content-Type: application/json');

// Levantamos el id del recurso buscado
$resourceId = array_key_exists('resource_id', $_GET) ? $_GET['resource_id'] : '';

switch ( strtoupper($_SERVER['REQUEST_METHOD'])){
    case 'GET':
        // en caso de que no pidan ningún recurso
        if (empty($resourceId)){
            echo json_encode($books);
        } else {
            //Si se pide un recurso en específico
            if( array_key_exists($resourceId,$books)){
                echo json_encode( $books[$resourceId]);
            }
        }
        break;
    case 'POST':
        $json = file_get_contents('php://input');
        $books[] = json_decode ($json, true);
        //echo array_keys($books)[count($books) - 1];
        echo json_encode($books);
        break;
    case 'PUT':
        //validamos que el recurso buscado exista
        if (!empty($resourceId) && array_key_exists($resourceId, $books)){
            // Tomamos la entrada cruda
            $json = file_get_contents('php://input');
             // transformamos el json recibido a un nuevo elemento del arreglo
            $books[$resourceId] = json_decode($json, true);
            // Retornamos la coleccion modificada en formato json
            echo json_encode($books);
        }
        break;
    case 'DELETE':
        // validamos que el recurso exista
        if (!empty($resourceId) && array_key_exists($resourceId, $books)){
            // Eliminamos el recurso
            unset( $books[ $resourceId]);    
            // Aquí verificamos que los cambios se han realizado  
            echo json_encode($books);      
        }
        break;
}

// Inicio el servidor en la terminal 1
// php -S localhost:8000 server.php

// Terminal 2 ejecutar 
// curl http://localhost:8000 -v
// curl http://localhost:8000/\?resource_type\=books
// curl http://localhost:8000/\?resource_type\=books | jq

?>