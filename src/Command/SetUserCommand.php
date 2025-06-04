<?php

namespace App\Command;

class SetUserCommand {
    public function __construct(
        public string $user
    ) {}
}