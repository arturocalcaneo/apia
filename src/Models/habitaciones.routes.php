<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;

/**
 * HABITACIONES: CONSULTAR TODOS
 */

$app->get('/habitaciones/todos', function (Request $request, Response $response) {
    $query= "SELECT * FROM cat_habitaciones";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->query($query);
        $habitaciones = $stmt->fetchAll(PDO::FETCH_OBJ);

        $db = null;

        $response->getBody()->write(json_encode($habitaciones));

        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
    }
    catch(PDOException $e){
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));

        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(500);
    }

    return $response;
});

$app->get('/habitaciones/todos/disponibles', function (Request $request, Response $response) {
    $query= "SELECT * FROM cat_habitaciones WHERE ESTATUS_CATHAB= '0'";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->query($query);
        $habitaciones = $stmt->fetchAll(PDO::FETCH_OBJ);

        $db = null;

        $response->getBody()->write(json_encode($habitaciones));

        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
    }
    catch(PDOException $e){
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));

        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(500);
    }

    return $response;
});

/**
 * HABITACIONES: AGREGAR NUEVA
 */

$app->post('/habitaciones/nuevo', function(Request $request, Response $response, array $args){

    $data= $request->getParsedBody();

    $piso           = $data['PISO'];
    $tipo_habit     = $data['TIPO_HABIT'];
    $tipo_habit_otro_desc= (!isset($data['TIPO_HABIT_OTRO_DESC']) || empty($data['TIPO_HABIT_OTRO_DESC'])) ? "" : $data['TIPO_HABIT_OTRO_DESC'];
    $precio         = $data['PRECIO'];
    $estatus_cathab = $data['ESTATUS_CATHAB'];

    $query= "INSERT INTO cat_habitaciones (PISO, TIPO_HABIT, TIPO_HABIT_OTRO_DESC, PRECIO, ESTATUS_CATHAB) 
             VALUES (:piso, :tipo_habit, :tipo_habit_otro_desc, :precio, :estatus_cathab)";

    try {
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':piso', $piso);
        $stmt->bindParam(':tipo_habit', $tipo_habit);
        $stmt->bindParam(':tipo_habit_otro_desc', $tipo_habit_otro_desc);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':estatus_cathab', $estatus_cathab);

        $result = $stmt->execute();

        $db = null;

        $response->getBody()->write(json_encode($result));

        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));

        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(500);
    }
});

/**
 * HABITACIONES: ACTUALIZAR
 */
$app->put('/habitaciones/actualizar/{idu}', function(Request $request, Response $response, array $args){
    $id   = $request->getAttribute('idu');
    $data = $request->getParsedBody();
    
    $piso           = $data['PISO'];
    $tipo_habit     = $data['TIPO_HABIT'];
    $tipo_habit_otro_desc= (!isset($data['TIPO_HABIT_OTRO_DESC']) || empty($data['TIPO_HABIT_OTRO_DESC'])) ? "" : $data['TIPO_HABIT_OTRO_DESC'];
    $precio         = $data['PRECIO'];
    $estatus_cathab = $data['ESTATUS_CATHAB'];

    $query= "UPDATE cat_habitaciones 
             SET PISO = :piso, 
                 TIPO_HABIT = :tipo_habit, 
                 TIPO_HABIT_OTRO_DESC = :tipo_habit_otro_desc, 
                 PRECIO = :precio,
                 ESTATUS_CATHAB = :estatus_cathab
             WHERE CLAVE_CATHAB = $id";

    try {
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':piso', $piso);
        $stmt->bindParam(':tipo_habit', $tipo_habit);
        $stmt->bindParam(':tipo_habit_otro_desc', $tipo_habit_otro_desc);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':estatus_cathab', $estatus_cathab);

        $result = $stmt->execute();

        $db = null;
        
        $response->getBody()->write(json_encode($result));

        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
    }
    catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        
        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(500);
    }
});

/**
 * HABITACIONES: ELIMINAR
 */
$app->delete('/habitaciones/eliminar/{id}', function(Request $req, Response $response, array $args){
    $id = intval( $req->getAttribute('id') );

    $query= "DELETE FROM cat_habitaciones WHERE CLAVE_CATHAB = $id";

    try{

        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->prepare($query);
        $result = $stmt->execute();

        $db = null;
        
        $response->getBody()->write(json_encode($result));

        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);

    }catch(PDOException $e){
        $error= array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));

        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(500);
    }
});

?>