<?php
namespace App\Infrastructure;
use PDO;

class EventStore {
  public static function append(object $event): void {
    $pdo = new PDO('mysql:host=mysql;dbname=to_projekt', 'user', 'password');
    $stmt = $pdo->prepare(
      'INSERT INTO events (aggregate_id,type,payload,created_at) VALUES 
       (:id,:type,:payload,NOW())'
    );
    $data= get_object_vars($event);
    $stmt->execute([
      ':id'=> $data['orderId'],
      ':type'=> get_class($event),
      ':payload'=> json_encode($data)
    ]);
    ProjectionDispatcher::dispatch($event);
  }
}
