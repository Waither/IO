<?php
namespace App\QueryHandler;

use App\Query\GetDriverAssignmentsQuery;
use PDO;

class GetDriverAssignmentsHandler {
  public function handle(GetDriverAssignmentsQuery $q): array {
    $pdo= new PDO('mysql:host=mysql;dbname=spedycja','spedycja_user','secretpassword');
    $stmt=$pdo->prepare(
      'SELECT order_id,status
       FROM driver_assignments
       WHERE driver_id=:d'
    );
    $stmt->execute([':d'=>$q->driverId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
