<?php
declare(strict_types=1);
if (parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) === '/' || parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) === '') {
    header('Location: index.html');
    exit;
}
require __DIR__.'/../vendor/autoload.php';

use App\Infrastructure\ProjectionDispatcher;
use App\Event\OrderSubmitted;
use App\Event\OrderValidated;
use App\Event\DriverAssigned;
use App\Event\OrderAccepted;
use App\Projection\OrderListProjection;

use App\Infrastructure\CommandBus;
use App\Infrastructure\QueryBus;
use App\Command\SubmitOrderCommand;
use App\Command\AssignDriverCommand;
use App\Query\GetOrdersQuery;
use App\Query\GetDriverAssignmentsQuery;
use App\Query\GetDriversQuery;
use App\Query\GetCompaniesQuery;

ProjectionDispatcher::register(OrderSubmitted::class,  [OrderListProjection::class, 'onOrderSubmitted']);
ProjectionDispatcher::register(OrderValidated::class,  [OrderListProjection::class, 'onOrderValidated']);
ProjectionDispatcher::register(DriverAssigned::class, [OrderListProjection::class, 'onDriverAssigned']);
ProjectionDispatcher::register(OrderAccepted::class, [OrderListProjection::class, 'onOrderAccepted']);

$commandBus = new CommandBus([
    'POST:/api/commands/set-user' => [
        'cmd' => \App\Command\SetUserCommand::class,
        'handler' => \App\CommandHandler\SetUserHandler::class,
    ],
    'POST:/api/commands/submit-order' => [
        'cmd' => SubmitOrderCommand::class,
        'handler' => App\CommandHandler\SubmitOrderHandler::class
    ],
    'POST:/api/commands/validate-order' => [
        'cmd' => \App\Command\ValidateOrderCommand::class,
        'handler' => \App\CommandHandler\ValidateOrderHandler::class,
    ],
    'POST:/api/commands/accept-offer' => [
        'cmd' => \App\Command\OrderAcceptedCommand::class,
        'handler' => \App\CommandHandler\OrderAcceptedHandler::class
    ],
    'POST:/api/commands/assign-driver' => [
        'cmd' => AssignDriverCommand::class,
        'handler' => App\CommandHandler\AssignDriverHandler::class
    ],
]);

$queryBus = new QueryBus([
    'GET:/api/queries/orders' => [
        'qry' => GetOrdersQuery::class,
        'handler' => App\QueryHandler\GetOrdersHandler::class
    ],
    'GET:/api/queries/driver-assign'=> [
        'qry' => GetDriverAssignmentsQuery::class,
        'handler' => App\QueryHandler\GetDriverAssignmentsHandler::class
    ],
    'GET:/api/queries/drivers' => [
        'qry' => GetDriversQuery::class,
        'handler' => \App\QueryHandler\GetDriversHandler::class,
    ],
    'GET:/api/queries/companies' => [
        'qry' => GetCompaniesQuery::class,
        'handler' => \App\QueryHandler\GetCompaniesHandler::class,
    ],
]);

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$key = "$method:$path";

try {
    if (isset($commandBus->routes[$key])) {
        $cfg = $commandBus->routes[$key];
        $data = json_decode(file_get_contents('php://input'), true) ?: [];
        $dto = (new ReflectionClass($cfg['cmd']))->newInstanceArgs(array_values($data));
        $res = $commandBus->dispatch($dto, $cfg['handler']);
    }
    elseif (isset($queryBus->routes[$key])) {
        $cfg = $queryBus->routes[$key];
        $data = $_GET;
        $dto = (new ReflectionClass($cfg['qry']))->newInstanceArgs(array_values($data));
        $res = $queryBus->dispatch($dto, $cfg['handler']);
    }
    else {
        http_response_code(404);
        exit('Not Found');
    }

    header('Content-Type: application/json');
    echo json_encode($res);
}
catch (Throwable $e){
    http_response_code(500);
    echo json_encode(['error'=>$e->getMessage()]);
}
