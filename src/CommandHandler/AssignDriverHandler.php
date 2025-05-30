<?php
namespace App\CommandHandler;

use App\Command\AssignDriverCommand;
use App\Infrastructure\EventStore;
use App\Event\DriverAssigned;

class AssignDriverHandler {
  public function handle(AssignDriverCommand $c): array {
    $e = new DriverAssigned($c->orderId,$c->driverId);
    EventStore::append($e);
    return ['status'=>'DriverAssigned'];
  }
}
