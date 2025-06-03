<?php
namespace App\QueryHandler;

use App\Query\GetOrdersQuery;
use App\Connection;
use PDO;

class GetOrdersHandler {
    public function handle(GetOrdersQuery $q): array {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare('SELECT ID_order, name_status AS status, name_company AS company, location_from, location_to, cargo, ID_driver, created_at FROM order_list NATURAL JOIN status NATURAL JOIN company;');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}