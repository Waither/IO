<?php
namespace App\Projection;
use App\Event\OrderSubmitted;
use App\Event\OrderValidated;
use App\Event\OrderAccepted;
use App\Event\DriverAssigned;
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
        $stmt = $pdo->prepare('UPDATE order_list SET ID_status = 2, price = :p WHERE ID_order = :id');
        $stmt->execute([':id' => $e->orderId, ':p' => $e->price]);
    }

    public static function onOrderAccepted(OrderAccepted $e): void {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare('UPDATE order_list SET ID_status = 3 WHERE ID_order = :o');
        $stmt->execute([':o' => $e->orderId]);
    }

    public static function onDriverAssigned(DriverAssigned $e): void {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare('UPDATE order_list SET ID_driver = :d WHERE ID_order = :o');
        $stmt->execute([':d' => $e->driverId, ':o' => $e->orderId]);
    }
}
