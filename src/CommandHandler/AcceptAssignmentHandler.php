<?php
namespace App\CommandHandler;

use App\Command\AcceptAssignmentCommand;
use App\Infrastructure\EventStore;
use App\Event\AssignmentAccepted;

class AcceptAssignmentHandler {
    public function handle(AcceptAssignmentCommand $cmd): array {
        $e = new AssignmentAccepted($cmd->orderId, $cmd->driverId);
        EventStore::append($e);
        return ['status' => 'AssignmentAccepted'];
    }
}