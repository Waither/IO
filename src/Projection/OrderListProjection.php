<?php
namespace App\Projection;
use App\Event\OrderSubmitted;
use App\Event\OrderValidated;
use App\Connection;

class OrderListProjection {
    public static function onOrderSubmitted(OrderSubmitted $e): void {
        $pdo = Connection::getInstance();
        $stmt= $pdo->prepare('INSERT INTO order_list (location_from, location_to, cargo) VALUES (:from, :to, :cargo)');
        $stmt->execute([':from' => $e->pickup, ':to' => $e->delivery, ':cargo' => $e->cargo, ]);
    }
    
  public static function onOrderValidated(OrderValidated $e): void {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare('UPDATE order_list SET status = 2 WHERE ID_order = :id');
        $stmt->execute([':id' => $e->orderId]);
    }
}
