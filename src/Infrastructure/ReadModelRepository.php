<?php
namespace App\Infrastructure;
use PDO;

class ReadModelRepository {
  public static function fetchAll(string $table): array {
    $pdo = new PDO('mysql:host=mysql;dbname=spedycja','spedycja_user','secretpassword');
    return $pdo->query("SELECT * FROM $table")->fetchAll(PDO::FETCH_ASSOC);
  }
}
