<?php

class Logger
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function logEvento($modulo, $evento, $usuario_id)
    {
        $consulta = "INSERT INTO log (modulo, usuario_id, evento) VALUES (:modulo, :usuario_id, :evento)";
        $stmt = $this->conexion->prepare($consulta);
        $stmt->bindParam(':modulo', $modulo);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':evento', $evento);
        $stmt->execute();
    }

}