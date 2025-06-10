<?php

namespace App\CommandHandler;

use App\Command\UpdateOrderStatusCommand;
use App\Connection;

class UpdateOrderStatusHandler
{
    public function handle(UpdateOrderStatusCommand $command): array
    {
        try {
            $pdo = Connection::getInstance();
            
            // Sprawdź czy zlecenie istnieje i czy kierowca ma uprawnienia
            $stmt = $pdo->prepare("
                SELECT ID_order, ID_status, ID_driver 
                FROM order_list 
                WHERE ID_order = ? AND (ID_driver = ? OR ID_driver IS NULL)
            ");
            $stmt->execute([$command->orderId, $command->driverId]);
            $order = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$order) {
                return ['error' => 'Zlecenie nie istnieje lub nie masz uprawnień do jego edycji'];
            }
            
            // Mapowanie statusów tekstowych na ID
            $statusMapping = [
                'pending' => 1,           // Utworzone
                'approved' => 2,          // Zatwierdzone przez spedytora  
                'ready_for_delivery' => 3, // Zaakceptowane przez klienta
                'in_progress' => 4,       // W trakcie (nowy status)
                'pickup_completed' => 5,  // Pobrano ładunek (nowy status)
                'delivered' => 6,         // Dostarczone (nowy status)
                'cancelled' => 7          // Anulowane (nowy status)
            ];
            
            $newStatusId = $statusMapping[$command->status] ?? null;
            if (!$newStatusId) {
                return ['error' => "Nieznany status: {$command->status}"];
            }            
            // Walidacja przejścia statusu (opcjonalne)
            $currentStatusId = (int)$order['ID_status'];
            
            // Aktualizuj status w bazie danych
            $updateStmt = $pdo->prepare("
                UPDATE order_list 
                SET ID_status = ? 
                WHERE ID_order = ?
            ");
            $updateStmt->execute([$newStatusId, $command->orderId]);
            
            // Mapowanie ID statusów na nazwy dla komunikatów
            $statusNames = [
                1 => 'Utworzone',
                2 => 'Zatwierdzone przez spedytora',
                3 => 'Zaakceptowane przez klienta',
                4 => 'W trakcie',
                5 => 'Pobrano ładunek',
                6 => 'Dostarczone',
                7 => 'Anulowane'
            ];
            
            $oldStatusName = $statusNames[$currentStatusId] ?? "Status #{$currentStatusId}";
            $newStatusName = $statusNames[$newStatusId] ?? "Status #{$newStatusId}";
            
            // Zapisz event do event store (opcjonalne)
            // $eventData = [
            //     'orderId' => $command->orderId,
            //     'oldStatus' => $oldStatusName,
            //     'newStatus' => $newStatusName,
            //     'driverId' => $command->driverId,
            //     'timestamp' => date('Y-m-d H:i:s')
            // ];
            // $this->eventStore->append('OrderStatusUpdated', $eventData);
            
            return [
                'success' => true,
                'message' => "Status zlecenia #{$command->orderId} został zaktualizowany z '{$oldStatusName}' na '{$newStatusName}'",
                'orderId' => $command->orderId,
                'oldStatus' => $oldStatusName,
                'newStatus' => $newStatusName
            ];
            
        } catch (\Exception $e) {
            error_log("Błąd podczas aktualizacji statusu zlecenia: " . $e->getMessage());
            return ['error' => 'Błąd podczas aktualizacji statusu zlecenia: ' . $e->getMessage()];
        }
    }
}
