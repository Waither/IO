<?php
namespace App\CommandHandler;

use App\Command\ValidateOrderCommand;
use App\Infrastructure\EventStore;
use App\Event\OrderValidated;

class ValidateOrderHandler {
    public function handle(ValidateOrderCommand $cmd): array {
        $e = new OrderValidated($cmd->orderId);
        EventStore::append($e);
        return ['status' => 'OrderValidated'];
    }
}