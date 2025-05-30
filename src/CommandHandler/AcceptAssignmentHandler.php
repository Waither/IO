<?php
namespace App\CommandHandler;

use App\Command\AcceptAssignmentCommand;
use App\Infrastructure\EventStore;
use App\Event\AssignmentAccepted;

class AcceptAssignmentHandler
{
    public function handle(AcceptAssignmentCommand $cmd): array
    {
        $event = new AssignmentAccepted(
            $cmd->orderId,
            $cmd->driverId
        );
        EventStore::append($event);
        return ['status' => 'AssignmentAccepted'];
    }
}