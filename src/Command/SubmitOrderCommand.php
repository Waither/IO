<?php
namespace App\Command;

class SubmitOrderCommand {
  public function __construct(
    public string $orderId,
    public string $pickup,
    public string $delivery,
    public string $cargo
  ){}
}
