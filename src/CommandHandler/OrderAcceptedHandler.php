<?php
namespace App\CommandHandler;

use App\Command\OrderAcceptedCommand;
use App\Infrastructure\EventStore;
use App\Event\OrderAccepted;

class OrderAcceptedHandler {
    public function handle(OrderAcceptedCommand $cmd): array {
        $e = new OrderAccepted($cmd->orderId);
        EventStore::append($e);
        return ['status' => 'OfferAccepted'];
    }
}