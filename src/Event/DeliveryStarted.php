<?php
namespace App\Event;

class DeliveryStarted {
    public function __construct(
        public string $orderId,
        public string $driverId,
        public string $startedAt,
        public string $currentLocation
    ) {}
}
