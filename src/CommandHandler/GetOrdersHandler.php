<?php
namespace App\QueryHandler;

use App\Query\GetOrdersQuery;
use App\Infrastructure\ReadModelRepository;

class GetOrdersHandler {
  public function handle(GetOrdersQuery $q): array {
    return ReadModelRepository::fetchAll('order_list');
  }
}
