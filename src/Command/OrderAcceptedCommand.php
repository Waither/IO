<?php
namespace App\Command;

class OrderAcceptedCommand {
    public function __construct(
        public string $orderId
    ) {}
}