<?php
namespace App\Event;

class OfferAccepted
{
    public function __construct(
        public string $orderId,
        public string $clientId
    ) {}
}