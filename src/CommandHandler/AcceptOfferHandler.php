<?php
namespace App\CommandHandler;

use App\Command\AcceptOfferCommand;
use App\Infrastructure\EventStore;
use App\Event\OfferAccepted;

class AcceptOfferHandler
{
    public function handle(AcceptOfferCommand $cmd): array
    {
        $event = new OfferAccepted(
            $cmd->orderId,
            $cmd->clientId
        );
        EventStore::append($event);
        return ['status' => 'OfferAccepted'];
    }
}