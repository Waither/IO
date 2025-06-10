<?php
namespace App\QueryHandler;

use App\Query\GetDocumentsQuery;
use App\Connection;
use PDO;

class GetDocumentsHandler {
    public function handle(GetDocumentsQuery $q): array {
        $pdo = Connection::getInstance();
        
        $stmt = $pdo->prepare('
            SELECT 
                document_type,
                document_path,
                generated_at
            FROM order_documents 
            WHERE order_id = :orderId 
            ORDER BY generated_at DESC
        ');
        
        $stmt->execute([':orderId' => $q->orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
