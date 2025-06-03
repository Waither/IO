<?php
namespace App\Infrastructure;
use App\Event\OrderSubmitted;
use App\Event\DriverAssigned;
use App\Event\OrderValidated;

class ProjectionDispatcher {
    private static array $listeners = [];

    public static function register(string $eventClass, callable $listener): void {
        self::$listeners[$eventClass][] = $listener;
    }

    public static function dispatch(object $event): void {
        $cls = get_class($event);
        foreach (self::$listeners[$cls] ?? [] as $listener) {
            $listener($event);
        }
    }
}
