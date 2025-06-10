<?php
namespace App\Command;

class GenerateDocumentCommand {
    public function __construct(
        public string $orderId,
        public string $documentType
    ) {}
}
