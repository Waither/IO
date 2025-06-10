<?php
namespace App\CommandHandler;

use App\Command\UpdateLocationCommand;
use App\Event\LocationUpdated;
use App\Infrastructure\EventStore;

class UpdateLocationHandler {
    public function handle(UpdateLocationCommand $cmd): array {
        $event = new LocationUpdated(
            $cmd->orderId,
            $cmd->driverId,
            $cmd->latitude,
            $cmd->longitude,
            date('Y-m-d H:i:s')
        );
        
        EventStore::append($event);
        
        return [
            'status' => 'LocationUpdated',
            'orderId' => $cmd->orderId,
            'latitude' => $cmd->latitude,
            'longitude' => $cmd->longitude
        ];
    }
}
