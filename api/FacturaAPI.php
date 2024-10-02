<?php

class FacturaAPI {

    public function API() {
        header('Content-Type: application/JSON');
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET'://consulta
                $this->getFacturas();
                break;
            case 'POST'://inserta
                echo 'POST';
                break;
            case 'PUT'://actualiza
                echo 'PUT';
                break;
            case 'DELETE'://elimina
                echo 'DELETE';
                break;
            default://metodo NO soportado
                echo 'METODO NO SOPORTADO';
                break;
        }
    }

    /**
     * Respuesta al cliente
     * @param int $code Codigo de respuesta HTTP
     * @param String $status indica el estado de la respuesta puede ser "success" o "error"
     * @param String $message Descripcion de lo ocurrido
     */
    function response($code = 200, $status = "", $message = "") {
        http_response_code($code);
        if (!empty($status) && !empty($message)) {
            $response = array("status" => $status, "message" => $message);
            echo json_encode($response, JSON_PRETTY_PRINT);
        }
    }

    function getFacturas() {
        if ($_GET['action'] == 'facturas') {
            //  $db = new FacturaDB();
            if (isset($_GET['id'])) {//muestra 1 solo registro si es que existiera ID                 
                $response = $db->leerArchivoTxt($_GET['id']);
                echo json_encode($response, JSON_PRETTY_PRINT);
            } else { //muestra todos los registros                   
                // $response = $db->leerArchivoTxt();
                //Retrieve the data from our text file.
                $fileContents = file_get_contents('../impresion/json_array.txt');

//Convert the JSON string back into an array.
                $response = json_decode($fileContents, true);

                echo json_encode($response); //($response, JSON_PRETTY_PRINT) funcion a partir de php 5.4 >
            }
        } else {
            // $this->response(400);

            
            echo "hubo un error";
        }
    }

}
