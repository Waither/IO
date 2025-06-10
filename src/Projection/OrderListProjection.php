<?php
namespace App\Projection;
use App\Event\OrderSubmitted;
use App\Event\OrderValidated;
use App\Event\OrderAccepted;
use App\Event\DriverAssigned;
use App\Event\RoutePlanned;
use App\Event\DeliveryStarted;
use App\Event\LocationUpdated;
use App\Event\DeliveryCompleted;
use App\Event\DocumentGenerated;
use App\Connection;

class OrderListProjection {
    public static function onOrderSubmitted(OrderSubmitted $e): void {
        $pdo = Connection::getInstance();
        $stmt= $pdo->prepare('CALL prc_add_order(:company, :from, :to, :cargo, :weight)');
        $stmt->execute([
            ':company' => $e->company,
            ':from'    => $e->pickup,
            ':to'      => $e->delivery,
            ':cargo'   => $e->cargo,
            ':weight'  => $e->weight
        ]);
    }
    
    public static function onOrderValidated(OrderValidated $e): void {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare('UPDATE order_list SET ID_status = 2, price = :p WHERE ID_order = :id');
        $stmt->execute([':id' => $e->orderId, ':p' => $e->price]);
    }

    public static function onOrderAccepted(OrderAccepted $e): void {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare('UPDATE order_list SET ID_status = 3 WHERE ID_order = :o');
        $stmt->execute([':o' => $e->orderId]);
    }

    public static function onDriverAssigned(DriverAssigned $e): void {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare('UPDATE order_list SET ID_driver = :d WHERE ID_order = :o');
        $stmt->execute([':d' => $e->driverId, ':o' => $e->orderId]);
    }

    public static function onRoutePlanned(RoutePlanned $e): void {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare('
            INSERT INTO order_routes (order_id, route, estimated_time, estimated_cost, planned_at)
            VALUES (:orderId, :route, :estimatedTime, :estimatedCost, :plannedAt)
            ON DUPLICATE KEY UPDATE 
                route = VALUES(route),
                estimated_time = VALUES(estimated_time),
                estimated_cost = VALUES(estimated_cost)
        ');
        $stmt->execute([
            ':orderId' => $e->orderId,
            ':route' => $e->route,
            ':estimatedTime' => $e->estimatedTime,
            ':estimatedCost' => $e->estimatedCost,
            ':plannedAt' => $e->plannedAt
        ]);
    }

    public static function onDeliveryStarted(DeliveryStarted $e): void {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare('UPDATE order_list SET ID_status = 4 WHERE ID_order = :orderId');
        $stmt->execute([':orderId' => $e->orderId]);
    }

    public static function onLocationUpdated(LocationUpdated $e): void {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare('
            INSERT INTO order_tracking (order_id, driver_id, latitude, longitude, updated_at)
            VALUES (:orderId, :driverId, :latitude, :longitude, :updatedAt)
            ON DUPLICATE KEY UPDATE 
                latitude = VALUES(latitude),
                longitude = VALUES(longitude),
                updated_at = VALUES(updated_at)
        ');
        $stmt->execute([
            ':orderId' => $e->orderId,
            ':driverId' => $e->driverId,
            ':latitude' => $e->latitude,
            ':longitude' => $e->longitude,
            ':updatedAt' => $e->updatedAt
        ]);
    }

    public static function onDeliveryCompleted(DeliveryCompleted $e): void {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare('UPDATE order_list SET ID_status = 5 WHERE ID_order = :orderId');
        $stmt->execute([':orderId' => $e->orderId]);
    }

    public static function onDocumentGenerated(DocumentGenerated $e): void {
        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare('
            INSERT INTO order_documents (order_id, document_type, document_path, generated_at)
            VALUES (:orderId, :documentType, :documentPath, :generatedAt)
        ');
        $stmt->execute([
            ':orderId' => $e->orderId,
            ':documentType' => $e->documentType,
            ':documentPath' => $e->documentPath,
            ':generatedAt' => $e->generatedAt
        ]);
    }
}
