<?php
namespace App\Infrastructure;

class CommandBus {
  /** @var array<string,array{cmd:string,handler:string}> */
  public array $routes;
  public function __construct(array $routes){ $this->routes = $routes; }

  public function dispatch(object $cmd, string $handlerCls): mixed {
    $h = new $handlerCls();
    return $h->handle($cmd);
  }
}
