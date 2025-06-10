<?php
namespace App\Event;

class DocumentGenerated {
    public function __construct(
        public string $orderId,
        public string $documentType, // 'waybill', 'invoice', 'receipt'
        public string $documentPath,
        public string $generatedAt
    ) {}
}
