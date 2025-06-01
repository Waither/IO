<?php
namespace App\CommandHandler;

use App\Command\AcceptOfferCommand;
use App\Infrastructure\EventStore;
use App\Event\OfferAccepted;

class AcceptOfferHandler {
    public function handle(AcceptOfferCommand $cmd): array {
        $e = new OfferAccepted($cmd->orderId, $cmd->clientId);
        EventStore::append($e);
        return ['status' => 'OfferAccepted'];
    }
}