<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;

abstract class Controlador{

    protected function validarCampo($solicitud,$campo, $mensaje, $response)
    {
        if (!isset($solicitud[$campo])) {
            $data = [
                'Status' => 'Fail',
                'Codigo' => 400,
                'Mensaje' => $mensaje,
                'Data' => $solicitud
            ];
            $response->getBody()->write(json_encode($data));
            return $response->withStatus($data['Codigo']);
        }
        return null; // Si la validación pasa, retorna null
    }
    
    
    
}

?>