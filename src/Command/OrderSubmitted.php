<?php
namespace App\Event;

class OrderSubmitted {
    public function __construct(
        public string $company,
        public string $pickup,
        public string $delivery,
        public string $cargo,
        public string $weight
    ) {}
}
