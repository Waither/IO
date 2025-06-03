<?php
namespace App\Projection;
use App\Event\OrderSubmitted;
use App\Event\OrderValidated;
use App\Connection;

class OrderListProjection {
    public static function onOrderSubmitted(OrderSubmitted $e): void {
        $pdo = Connection::getInstance();
        $stmt= $pdo->prepare('CALL prc_add_order(:company, :from, :to, :cargo, :weight)');
        $stmt->execute([
            ':company' => $e->company,
            ':from'    => $e->pickup,
            ':to'      => $e->delivery,
            ':cargo'   => $e->cargo,
            ':weight'  => $e->weight
        ]);
    }
    
  public static function onOrderValidated(OrderValidated $e): void {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare('UPDATE order_list SET ID_status = 2 WHERE ID_order = :id');
        $stmt->execute([':id' => $e->orderId]);
    }
}
