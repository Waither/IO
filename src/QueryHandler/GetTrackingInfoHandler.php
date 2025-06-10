<?php
namespace App\QueryHandler;

use App\Query\GetTrackingInfoQuery;
use App\Connection;
use PDO;

class GetTrackingInfoHandler {
    public function handle(GetTrackingInfoQuery $q): array {
        $pdo = Connection::getInstance();
          // Pobieranie informacji o śledzeniu zlecenia
        $stmt = $pdo->prepare('
            SELECT 
                o.ID_order,
                s.name_status as status,
                o.location_from,
                o.location_to,
                o.cargo,
                d.name_driver,
                t.latitude,
                t.longitude,
                t.updated_at as last_location_update,
                r.route,
                r.estimated_time,
                r.estimated_cost
            FROM order_list o
            LEFT JOIN status s ON o.ID_status = s.ID_status
            LEFT JOIN driver d ON o.ID_driver = d.ID_driver
            LEFT JOIN order_tracking t ON o.ID_order = t.order_id
            LEFT JOIN order_routes r ON o.ID_order = r.order_id
            WHERE o.ID_order = :orderId
        ');
        
        $stmt->execute([':orderId' => $q->orderId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return ['error' => 'Order not found'];
        }
        
        return $result;
    }
}
