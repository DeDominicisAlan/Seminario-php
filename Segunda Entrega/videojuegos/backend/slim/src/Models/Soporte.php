<?php

namespace App\Models;

use PDO;

class Soporte extends Modelo
{
    public function agregarSoporte($solicitud)
    {
        try {
            // Conectar a la base de datos
            $connection = $this->connect();

            // Verificar si el juego existe
            $tablaJuego = $connection->prepare("SELECT * FROM juego WHERE id = :id");
            $tablaJuego->execute([
                ":id" => $solicitud['juego_id']
            ]);
            $existeJuego = $tablaJuego->fetchColumn();

            if ($existeJuego) {
                // Obtener plataformas disponibles
                $tablaPlataformas = $connection->prepare("SELECT * FROM plataforma WHERE id = :plataforma_id");
                $tablaPlataformas->execute([':plataforma_id' => $solicitud['plataforma_id']]);
                $plataformaExistente = $tablaPlataformas->fetchColumn();

                if ($plataformaExistente) {
                    // Obtener el nuevo ID para soporte
                    $maxId = $connection->prepare("SELECT MAX(id) AS max_id FROM soporte");
                    $maxId->execute();
                    $resultado = $maxId->fetch(PDO::FETCH_ASSOC);
                    $nuevoId = $resultado['max_id'] ? $resultado['max_id'] + 1 : 1;

                    // Insertar el nuevo soporte
                    $tablaSoporte = $connection->prepare("INSERT INTO soporte (id, juego_id, plataforma_id) VALUES (:id, :juego_id, :plataforma_id)");
                    $tablaSoporte->execute([
                        ':id' => $nuevoId,
                        ':juego_id' => $solicitud['juego_id'],
                        ':plataforma_id' => $solicitud['plataforma_id']
                    ]);

                    // Respuesta exitosa
                    $this->data['Status'] = 'Success';
                    $this->data['Codigo'] = 200;
                    $this->data['Data']['id'] = $nuevoId;
                    $this->data['Data']['juego_id'] = $solicitud['juego_id'];
                    $this->data['Data']['plataforma_id'] = $solicitud['plataforma_id'];
                } else {
                    $this->data['Status'] = 'Fail';
                    $this->data['Mensaje'] = 'No se encontró la plataforma solicitada.';
                    $this->data['Codigo'] = 404;
                }
            } else {
                $this->data['Status'] = 'Fail';
                $this->data['Mensaje'] = 'No se encontró el juego solicitado.';
                $this->data['Codigo'] = 404;
            }
        } catch (\PDOException $e) {
            $this->data['Status'] = 'Throw Server/DB Error';
            $this->data['Mensaje'] = $e->getMessage() . " " . $e->getCode();
            $this->data['Codigo'] = 500;
        }

        return $this->data;
    }
}
