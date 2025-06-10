<?php
namespace App\CommandHandler;

use App\Command\GenerateDocumentCommand;
use App\Event\DocumentGenerated;
use App\Infrastructure\EventStore;

class GenerateDocumentHandler {
    public function handle(GenerateDocumentCommand $cmd): array {
        // Symulacja generowania dokumentu
        $documentPath = "/documents/{$cmd->orderId}_{$cmd->documentType}_" . date('Y-m-d_H-i-s') . ".pdf";
        
        $event = new DocumentGenerated(
            $cmd->orderId,
            $cmd->documentType,
            $documentPath,
            date('Y-m-d H:i:s')
        );
        
        EventStore::append($event);
        
        return [
            'status' => 'DocumentGenerated',
            'orderId' => $cmd->orderId,
            'documentType' => $cmd->documentType,
            'documentPath' => $documentPath
        ];
    }
}
