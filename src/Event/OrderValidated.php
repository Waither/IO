<?php
namespace App\Event;

class OrderValidated {
    public function __construct(
        public string $orderId,
        public float $price,
    ) {}
}