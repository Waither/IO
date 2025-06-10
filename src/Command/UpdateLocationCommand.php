<?php
namespace App\Command;

class UpdateLocationCommand {
    public function __construct(
        public string $orderId,
        public string $driverId,
        public float $latitude,
        public float $longitude
    ) {}
}
