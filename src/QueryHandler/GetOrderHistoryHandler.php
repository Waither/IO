<?php
namespace App\QueryHandler;

use App\Query\GetOrderHistoryQuery;
use App\Connection;
use PDO;

class GetOrderHistoryHandler {    public function handle(GetOrderHistoryQuery $q): array {
        $pdo = Connection::getInstance();
        
        // Pobieranie wszystkich wydarzeń i filtrowanie w PHP (dla uproszczenia)
        $stmt = $pdo->prepare('
            SELECT 
                type,
                payload,
                created_at
            FROM events 
            ORDER BY created_at ASC
        ');
        
        $stmt->execute();
        $allEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Filtrowanie wydarzeń dla konkretnego zamówienia
        $history = [];
        foreach ($allEvents as $event) {
            $payload = json_decode($event['payload'], true);
            
            // Sprawdź czy wydarzenie dotyczy naszego zamówienia
            $eventOrderId = null;
            if (isset($payload['orderId'])) {
                $eventOrderId = $payload['orderId'];
            } elseif (isset($payload['order_id'])) {
                $eventOrderId = $payload['order_id'];
            } elseif (isset($payload['ID_order'])) {
                $eventOrderId = $payload['ID_order'];
            }
            
            if ($eventOrderId == $q->orderId) {
                $type = str_replace('App\\Event\\', '', $event['type']);
                
                $history[] = [
                    'type' => $type,
                    'timestamp' => $event['created_at'],
                    'details' => $payload
                ];
            }
        }
        
        return $history;
    }
}
