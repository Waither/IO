<?php

namespace App\QueryHandler;

use App\Query\GetCompaniesQuery;
use App\Connection;
use PDO;

class GetCompaniesHandler {
    public function handle(GetCompaniesQuery $q): array {
        $pdo  = Connection::getInstance();
        $stmt = $pdo->prepare('SELECT name_company AS name FROM company WHERE name_company LIKE "%'.$q->query.'%" ORDER BY name_company;');
        $stmt->execute();
        $values = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_column($values, 'name');
    }
}