<?php
namespace App\Command;

class AcceptAssignmentCommand {
    public function __construct(
        public string $orderId,
        public string $driverId
    ) {}
}