<?php
namespace App\CommandHandler;

use App\Command\ValidateOrderCommand;
use App\Infrastructure\EventStore;
use App\Event\OrderValidated;

class ValidateOrderHandler
{
    public function handle(ValidateOrderCommand $cmd): array
    {
        $event = new OrderValidated($cmd->orderId);
        EventStore::append($event);
        return ['status' => 'OrderValidated'];
    }
}