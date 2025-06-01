<?php
namespace App\Event;

class DriverAssigned {
    public function __construct(
        public string $orderId,
        public string $driverId
    ) {}
}
