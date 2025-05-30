<?php
namespace App\Command;

class AcceptOfferCommand
{
    public function __construct(
        public string $orderId,
        public string $clientId
    ) {}
}