<?php
namespace App\Event;

class OrderAccepted {
    public function __construct(
        public string $orderId
    ) {}
}