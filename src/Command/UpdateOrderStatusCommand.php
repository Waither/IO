<?php

namespace App\Command;

class UpdateOrderStatusCommand
{
    public function __construct(
        public readonly int $orderId,
        public readonly string $status,
        public readonly int $driverId
    ) {}
}
