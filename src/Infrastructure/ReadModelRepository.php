<?php
namespace App\Infrastructure;
use PDO;

class ReadModelRepository {
  public static function fetchAll(string $table): array {
    $pdo = new PDO('mysql:host=mysql;dbname=to_projekt', 'user', 'password');
    return $pdo->query("SELECT * FROM $table")->fetchAll(PDO::FETCH_ASSOC);
  }
}
