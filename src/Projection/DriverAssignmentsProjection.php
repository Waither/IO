<?php
namespace App\Projection;
use App\Event\DriverAssigned;
use PDO;

class DriverAssignmentsProjection {
  public static function onDriverAssigned(DriverAssigned $e): void {
    $pdo = new PDO('mysql:host=mysql;dbname=to_projekt', 'user', 'password');
    $stmt=$pdo->prepare(
      'INSERT INTO driver_assignments (driver_id,order_id,status,assigned_at)
       VALUES(:d,:o,:s,NOW())'
    );
    $stmt->execute([':d'=>$e->driverId,':o'=>$e->orderId,':s'=>'assigned']);
  }
}
