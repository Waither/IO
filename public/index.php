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
use App\Event\RoutePlanned;
use App\Event\DeliveryStarted;
use App\Event\LocationUpdated;
use App\Event\DeliveryCompleted;
use App\Event\DocumentGenerated;
use App\Projection\OrderListProjection;

use App\Infrastructure\CommandBus;
use App\Infrastructure\QueryBus;
use App\Command\SubmitOrderCommand;
use App\Command\AssignDriverCommand;
use App\Command\PlanRouteCommand;
use App\Command\StartDeliveryCommand;
use App\Command\UpdateLocationCommand;
use App\Command\UpdateOrderStatusCommand;
use App\Command\CompleteDeliveryCommand;
use App\Command\GenerateDocumentCommand;
use App\Query\GetOrdersQuery;
use App\Query\GetDriverAssignmentsQuery;
use App\Query\GetDriversQuery;
use App\Query\GetCompaniesQuery;
use App\Query\GetTrackingInfoQuery;
use App\Query\GetDocumentsQuery;

ProjectionDispatcher::register(OrderSubmitted::class,  [OrderListProjection::class, 'onOrderSubmitted']);
ProjectionDispatcher::register(OrderValidated::class,  [OrderListProjection::class, 'onOrderValidated']);
ProjectionDispatcher::register(DriverAssigned::class, [OrderListProjection::class, 'onDriverAssigned']);
ProjectionDispatcher::register(OrderAccepted::class, [OrderListProjection::class, 'onOrderAccepted']);
ProjectionDispatcher::register(RoutePlanned::class, [OrderListProjection::class, 'onRoutePlanned']);
ProjectionDispatcher::register(DeliveryStarted::class, [OrderListProjection::class, 'onDeliveryStarted']);
ProjectionDispatcher::register(LocationUpdated::class, [OrderListProjection::class, 'onLocationUpdated']);
ProjectionDispatcher::register(DeliveryCompleted::class, [OrderListProjection::class, 'onDeliveryCompleted']);
ProjectionDispatcher::register(DocumentGenerated::class, [OrderListProjection::class, 'onDocumentGenerated']);

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
    'POST:/api/commands/plan-route' => [
        'cmd' => PlanRouteCommand::class,
        'handler' => App\CommandHandler\PlanRouteHandler::class
    ],
    'POST:/api/commands/start-delivery' => [
        'cmd' => StartDeliveryCommand::class,
        'handler' => App\CommandHandler\StartDeliveryHandler::class
    ],    'POST:/api/commands/update-location' => [
        'cmd' => UpdateLocationCommand::class,
        'handler' => App\CommandHandler\UpdateLocationHandler::class
    ],
    'POST:/api/commands/update-order-status' => [
        'cmd' => UpdateOrderStatusCommand::class,
        'handler' => App\CommandHandler\UpdateOrderStatusHandler::class
    ],
    'POST:/api/commands/complete-delivery' => [
        'cmd' => CompleteDeliveryCommand::class,
        'handler' => App\CommandHandler\CompleteDeliveryHandler::class
    ],
    'POST:/api/commands/generate-document' => [
        'cmd' => GenerateDocumentCommand::class,
        'handler' => App\CommandHandler\GenerateDocumentHandler::class
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
    'GET:/api/queries/tracking-info' => [
        'qry' => GetTrackingInfoQuery::class,
        'handler' => App\QueryHandler\GetTrackingInfoHandler::class
    ],    'GET:/api/queries/documents' => [
        'qry' => GetDocumentsQuery::class,
        'handler' => App\QueryHandler\GetDocumentsHandler::class
    ],
]);

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$key = "$method:$path";

try {    if (isset($commandBus->routes[$key])) {
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
    error_log("API Error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    http_response_code(500);
    echo json_encode(['error'=>$e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
}
