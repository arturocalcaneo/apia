<?php
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Selective\BasePath\BasePathMiddleware;
    use Slim\Factory\AppFactory;

    $app->get('/usuario/existe/{tipo}', function(Request $request, Response $response){
        $tipoUsuario= strtolower( trim( $request->getAttribute('tipo') ) );
        $correoUsuario= trim( $_GET['correo'] );

        $query= "SELECT COUNT(*) AS existe FROM ";

        try{

            // Completar el Query según el tipo de usuario
            if( $tipoUsuario == 'recepcionista' ){
                $query.= "recepcionistas WHERE CORREO= '".$correoUsuario."'";
            }
            else{
                $query.= "huespedes WHERE CORREO_H= '".$correoUsuario."'";
            }

            // Consultar a la base de datos, recuperar y enviar la respuesta.
            $db= new DB();
            $con= $db->connect();
            $statement= $con->query($query);
            $result= $statement->fetchAll(PDO::FETCH_OBJ);

            $db= null;

            $response->getBody()->write( json_encode( boolval( $result[0]->existe ) ) );

            $status= 200;

        }catch(PDOException $ex){
            $error = array(
                "message" => $e->getMessage()
            );
    
            $response->getBody()->write(json_encode($error));
            $status= 500;
        }

        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus($status);
    });

    $app->get('/usuario/autenticar/{tipo}', function(Request $request, Response $response){
        $tipoUsuario= strtolower( trim( $request->getAttribute('tipo') ) );
        $correoUsuario= trim( $_GET['correo'] );
        $contrasena= $_GET['contra'];

        $query= "SELECT COUNT(*) AS existe FROM ";

        try{

            // Completar el Query según el tipo de usuario
            if( $tipoUsuario == 'recepcionista' ){
                $query.= "recepcionistas WHERE CORREO= '".$correoUsuario."' AND PASSW= '".$contrasena."'";
            }
            else{
                $query.= "huespedes WHERE CORREO_H= '".$correoUsuario."' AND PASSW_H= '".$contrasena."'";
            }

            // Consultar a la base de datos, recuperar y enviar la respuesta.
            $db= new DB();
            $con= $db->connect();
            $statement= $con->query($query);
            $result= $statement->fetchAll(PDO::FETCH_OBJ);

            $db= null;

            $response->getBody()->write( json_encode( boolval( $result[0]->existe ) ) );

            $status= 200;

        }catch(PDOException $ex){
            $error = array(
                "message" => $e->getMessage()
            );
    
            $response->getBody()->write(json_encode($error));
            $status= 500;
        }

        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus($status);
    });

    $app->post('/usuario/nuevo/{tipo}', function(Request $request, Response $response, array $args){
        $tipo= strtolower( trim($request->getAttribute('tipo')) );
        $data= $request->getParsedBody();

        $query= "INSERT INTO";

        // Preparar datos y completar el Query SQL.
        if( $tipo == 'recepcionista' )
        {
            $nombre= $data['NOMBRE_RECEP'];
            $app= $data['AP_PATERNO_R'];
            $apm= $data['AP_MATERNO_R'];
            $rfcGenerico= $data['RFC_GENERICO'];
            $fechaNaci= $data['FECHA_NACIR'];
            $correo= $data['CORREO'];
            $password= $data['PASSW'];
            $fechaAlta= $data['FECHA_ALTA'] = "current_timestamp()";
            $estatusRecep= $data['ESTATUS_R'];

            $query.= " recepcionistas (NOMBRE_RECEP,AP_PATERNO_R,AP_MATERNO_R,RFC_GENERICO,FECHA_NACIR,CORREO,PASSW,FECHA_ALTA,ESTATUS_R) 
                       VALUES ('".$nombre."','".$app."','".$apm."','".$rfcGenerico."','".$fechaNaci."','".$correo."','".$password."',".$fechaAlta.",b'1')";
        }else{
            $nombre= $data['NOMBRE_HUESP'];
            $app= $data['AP_PATERNO_H'];
            $apm= $data['AP_MATERNO_H'];
            $correo= $data['CORREO_H'];
            $password= $data['PASSW_H'];
            $fechaAlta= $data['FECHA_NACI_H'];
            $estatusRecep= $data['BALANCE'];
            $telefono= $data['TELEFONO_H'];

            $query.= " huespedes () VALUES ()";
        }

        // Ejecutar el Query SQL.
        try{
            $db= new DB();

            $con= $db->connect();
            $statement= $con->prepare($query);
            $result= $statement->execute();

            $db= null;
            
            $response->getBody()->write( json_encode( $result ) );
            $estatus= 200;

        }catch(PDOException $e){
            $error = array(
                "message" => $e->getMessage()
            );
    
            $response->getBody()->write(json_encode($error));
            $estatus= 500;
        }

        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus($estatus);
    });
?>