<?php
namespace App\CommandHandler;

use App\Command\SubmitOrderCommand;
use App\Infrastructure\EventStore;
use App\Event\OrderSubmitted;

class SubmitOrderHandler {
    public function handle(SubmitOrderCommand $c): array {
        $e = new OrderSubmitted( $c->company, $c->pickup, $c->delivery, $c->cargo, $c->weight );
        EventStore::append($e);
        return ['status' => 'OrderSubmitted'];
    }
}
