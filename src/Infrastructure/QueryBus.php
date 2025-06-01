<?php
namespace App\Infrastructure;

class QueryBus {
    public array $routes;
    public function __construct(array $routes){ $this->routes = $routes; }

    public function dispatch(object $qry, string $handlerCls): mixed {
        $h = new $handlerCls();
        return $h->handle($qry);
    }
}
