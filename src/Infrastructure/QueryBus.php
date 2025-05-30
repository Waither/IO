<?php
namespace App\Infrastructure;

class QueryBus {
  /** @var array<string,array{qry:string,handler:string}> */
  public array $routes;
  public function __construct(array $routes){ $this->routes = $routes; }

  public function dispatch(object $qry, string $handlerCls): mixed {
    $h = new $handlerCls();
    return $h->handle($qry);
  }
}
