<?php

namespace App\QueryHandler;

use App\Query\GetDriversQuery;
use App\Connection;
use PDO;

class GetDriversHandler {
    public function handle(GetDriversQuery $q): array {
        $pdo  = Connection::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM driver ORDER BY name_driver;');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}