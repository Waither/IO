<?php
namespace App\Query;

class GetDocumentsQuery {
    public function __construct(
        public string $orderId
    ) {}
}
