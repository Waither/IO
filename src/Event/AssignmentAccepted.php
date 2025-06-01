<?php
namespace App\Event;

class AssignmentAccepted {
    public function __construct(
        public string $orderId,
        public string $driverId
    ) {}
}