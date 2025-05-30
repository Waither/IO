<?php
namespace App\Command;

class AssignDriverCommand {
  public function __construct(
    public string $orderId,
    public string $driverId
  ){}
}
