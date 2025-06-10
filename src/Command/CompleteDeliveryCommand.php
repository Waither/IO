<?php
namespace App\Command;

class CompleteDeliveryCommand {
    public function __construct(
        public string $orderId,
        public string $driverId,
        public ?string $signature = null
    ) {}
}
