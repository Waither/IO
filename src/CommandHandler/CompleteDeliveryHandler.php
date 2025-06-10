<?php
namespace App\CommandHandler;

use App\Command\CompleteDeliveryCommand;
use App\Event\DeliveryCompleted;
use App\Infrastructure\EventStore;

class CompleteDeliveryHandler {
    public function handle(CompleteDeliveryCommand $cmd): array {
        $event = new DeliveryCompleted(
            $cmd->orderId,
            $cmd->driverId,
            date('Y-m-d H:i:s'),
            "Punkt docelowy", // W rzeczywistej aplikacji pobrane z GPS
            $cmd->signature
        );
        
        EventStore::append($event);
        
        return [
            'status' => 'DeliveryCompleted',
            'orderId' => $cmd->orderId,
            'completedAt' => date('Y-m-d H:i:s')
        ];
    }
}
