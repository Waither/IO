<?php
namespace App\Query;

class GetTrackingInfoQuery {
    public function __construct(
        public string $orderId
    ) {}
}
