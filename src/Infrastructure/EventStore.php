<?php
namespace App\Infrastructure;
use App\Connection;

class EventStore {
    public static function append(object $e): void {
        $data= get_object_vars($e);

        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare('INSERT INTO events (type, payload) VALUES (:type, :payload)');
        $stmt->execute([':type'=> get_class($e), ':payload'=> json_encode($data)]);

        ProjectionDispatcher::dispatch($e);
    }
}
