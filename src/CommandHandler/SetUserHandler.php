<?php

namespace App\CommandHandler;

use App\Command\SetUserCommand;

class SetUserHandler {
    public function handle(SetUserCommand $cmd): array {
        setcookie('user_spedycja', $cmd->user, time() + 100 * 86400, '/');
        return [
            'status' => 'UserSet',
            'user'   => $cmd->user
        ];
    }
}