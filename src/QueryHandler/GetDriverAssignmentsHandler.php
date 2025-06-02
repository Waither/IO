<?php
namespace App\QueryHandler;

use App\Query\GetDriverAssignmentsQuery;
use App\Connection;
use PDO;

class GetDriverAssignmentsHandler {
    public function handle(GetDriverAssignmentsQuery $q): array {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare('SELECT ID_order, name_status FROM order_list WHERE ID_driver = :d');
        $stmt->execute([':d' => $q->driverId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
