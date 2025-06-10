<?php
namespace App\Event;

class DeliveryCompleted {
    public function __construct(
        public string $orderId,
        public string $driverId,
        public string $completedAt,
        public string $deliveryLocation,
        public ?string $signature = null
    ) {}
}
