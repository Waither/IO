<?php
namespace App\Query;

class GetOrderHistoryQuery {
    public function __construct(
        public string $orderId
    ) {}
}
