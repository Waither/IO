<?php
namespace App\Projection;
use App\Event\DriverAssigned;
use App\Connection;

class DriverAssignmentsProjection {
    public static function onDriverAssigned(DriverAssigned $e): void {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare('INSERT INTO driver_assignments (ID_order, ID_driver) VALUES(:o, :d);');
        $stmt->execute([':d' => $e->driverId, ':o' => $e->orderId]);
    }
}
