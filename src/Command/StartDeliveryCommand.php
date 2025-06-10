<?php
namespace App\Command;

class StartDeliveryCommand {
    public function __construct(
        public string $orderId,
        public string $driverId
    ) {}
}
