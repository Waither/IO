<?php
namespace App\Projection;
use App\Event\DriverAssigned;
use App\Connection;

class DriverAssignmentsProjection {
    public static function onDriverAssigned(DriverAssigned $e): void {
        $pdo = Connection::getInstance();
        $stmt=$pdo->prepare('INSERT INTO driver_assignments (driver_id, order_id, status, assigned_at) VALUES(:d, :o, :s, NOW())');
        $stmt->execute([':d' => $e->driverId, ':o' => $e->orderId, ':s' => 'assigned']);
    }
}
