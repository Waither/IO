<?php
namespace App\CommandHandler;

use App\Command\StartDeliveryCommand;
use App\Event\DeliveryStarted;
use App\Infrastructure\EventStore;

class StartDeliveryHandler {
    public function handle(StartDeliveryCommand $cmd): array {
        $event = new DeliveryStarted(
            $cmd->orderId,
            $cmd->driverId,
            date('Y-m-d H:i:s'),
            "Punkt startowy" // W rzeczywistej aplikacji pobrane z GPS
        );
        
        EventStore::append($event);
        
        return [
            'status' => 'DeliveryStarted',
            'orderId' => $cmd->orderId,
            'driverId' => $cmd->driverId
        ];
    }
}
