<?php
namespace App\Command;

class PlanRouteCommand {
    public function __construct(
        public string $orderId
    ) {}
}
