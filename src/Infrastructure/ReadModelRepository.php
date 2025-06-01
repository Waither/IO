<?php
namespace App\Infrastructure;
use App\Connection;
use PDO;

class ReadModelRepository {
    public static function fetchAll(string $table): array {
        $pdo = Connection::getInstance();
        $stmt = $pdo->query("SELECT * FROM $table");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
