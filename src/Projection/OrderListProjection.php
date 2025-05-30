<?php
namespace App\Projection;
use App\Event\OrderSubmitted;
use App\Event\OrderValidated;
use PDO;

class OrderListProjection {
  public static function onOrderSubmitted(OrderSubmitted $e): void {
    $pdo = new PDO('mysql:host=mysql;dbname=to_projekt', 'user', 'password');
    $stmt= $pdo->prepare(
      'INSERT INTO order_list (order_id,status,created_at)
       VALUES(:id,:st,NOW())'
    );
    $stmt->execute([':id'=>$e->orderId,':st'=>'submitted']);
  }
  
  public static function onOrderValidated(OrderValidated $e): void
    {
        $pdo = new \PDO('mysql:host=mysql;dbname=spedycja', 'spedycja_user', 'secretpassword');
        $stmt = $pdo->prepare(
            'UPDATE order_list SET status = :status WHERE order_id = :id'
        );
        $stmt->execute([
            ':status' => 'validated',
            ':id'     => $e->orderId,
        ]);
    }
}
