<?php
namespace App\Command;

class SubmitOrderCommand {
    public function __construct(
        public string $pickup,
        public string $delivery,
        public string $cargo
    ) {}
}
