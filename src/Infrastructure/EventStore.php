<?php
namespace App\Infrastructure;
use App\Connection;

class EventStore {
    public static function append(object $event): void {
        $data= get_object_vars($event);

        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare('INSERT INTO events (aggregate_id, type, payload, created_at) VALUES (:id, :type, :payload, NOW())');
        $stmt->execute([':id'=> $data['orderId'], ':type'=> get_class($event), ':payload'=> json_encode($data)]);

        ProjectionDispatcher::dispatch($event);
    }
}
