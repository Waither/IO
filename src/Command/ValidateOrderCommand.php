<?php
namespace App\Command;

class ValidateOrderCommand
{
    public function __construct(
        public string $orderId
    ){}
}