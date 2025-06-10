<?php
namespace App\CommandHandler;

use App\Command\PlanRouteCommand;
use App\Event\RoutePlanned;
use App\Infrastructure\EventStore;

class PlanRouteHandler {
    public function handle(PlanRouteCommand $cmd): array {
        // Symulacja planowania trasy (tutaj mogłaby być integracja z API map)
        $route = "Warszawa -> Kraków -> Katowice";
        $estimatedTime = "4:30";
        $estimatedCost = 450.00;
        
        $event = new RoutePlanned(
            $cmd->orderId,
            $route,
            $estimatedTime,
            $estimatedCost,
            date('Y-m-d H:i:s')
        );
        
        EventStore::append($event);
        
        return [
            'status' => 'RoutePlanned',
            'orderId' => $cmd->orderId,
            'route' => $route,
            'estimatedTime' => $estimatedTime,
            'estimatedCost' => $estimatedCost
        ];
    }
}
