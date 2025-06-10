<?php
namespace App\Event;

class LocationUpdated {
    public function __construct(
        public string $orderId,
        public string $driverId,
        public float $latitude,
        public float $longitude,
        public string $updatedAt
    ) {}
}
