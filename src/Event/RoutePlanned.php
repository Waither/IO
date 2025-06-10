<?php
namespace App\Event;

class RoutePlanned {
    public function __construct(
        public string $orderId,
        public string $route,
        public string $estimatedTime,
        public float $estimatedCost,
        public string $plannedAt
    ) {}
}
