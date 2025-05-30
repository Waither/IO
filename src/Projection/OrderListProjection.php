<?php
namespace App\Projection;
use App\Event\OrderSubmitted;
use PDO;

class OrderListProjection {
  public static function onOrderSubmitted(OrderSubmitted $e): void {
    $pdo = new PDO('mysql:host=mysql;dbname=spedycja','spedycja_user','secretpassword');
    $stmt= $pdo->prepare(
      'INSERT INTO order_list (order_id,status,created_at)
       VALUES(:id,:st,NOW())'
    );
    $stmt->execute([':id'=>$e->orderId,':st'=>'submitted']);
  }
}
