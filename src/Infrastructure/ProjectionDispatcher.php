<?php
namespace App\Infrastructure;
use App\Event\OrderSubmitted;
use App\Event\DriverAssigned;
use App\Event\OrderValidated;

class ProjectionDispatcher {
    public static function dispatch(object $e): void {
        switch(get_class($e)) {
            case OrderSubmitted::class:
                \App\Projection\OrderListProjection::onOrderSubmitted($e);
                break;
            case DriverAssigned::class:
                \App\Projection\DriverAssignmentsProjection::onDriverAssigned($e);
                break;
            case OrderValidated::class:
                \App\Projection\OrderListProjection::onOrderValidated($e);
                break;
        }
    }
}
