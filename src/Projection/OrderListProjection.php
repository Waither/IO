<?php
namespace App\Projection;
use App\Event\OrderSubmitted;
use App\Event\OrderValidated;
use App\Connection;

class OrderListProjection {
    public static function onOrderSubmitted(OrderSubmitted $e): void {
        $pdo = Connection::getInstance();
        $stmt= $pdo->prepare('INSERT INTO order_list (order_id, status, created_at) VALUES (:id, :st, NOW())');
        $stmt->execute([':id' => $e->orderId, ':st' => 'submitted']);
    }
    
  public static function onOrderValidated(OrderValidated $e): void {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare('UPDATE order_list SET status = :status WHERE order_id = :id');
        $stmt->execute([':status' => 'validated', ':id' => $e->orderId]);
    }
}
