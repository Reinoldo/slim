<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


require '../vendor/autoload.php';
require '../config/config.php';


$app = new \Slim\App(["settings" => $config]);

$container = $app->getContainer();

$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$app->get('/', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Pagina Principal");
    $this->logger->addInfo("vc acessou a pagina principal");
   

    return $response;
});

$app->get('/tickets', function (Request $request, Response $response) {
    $this->logger->addInfo("Ticket list page");
    //$mapper = new TicketMapper($this->db);
    //$tickets = $mapper->getTickets();
    $tickets = new stdClass();
    $tickets->id = 1;
    $tickets->nome = "jhonatan";
    $response->getBody()->write(var_export($tickets, true));
    return $response;
});


$app->get('/ticket/{id}', function (Request $request, Response $response, $args) {
    $ticket_id = (int)$args['id'];
    $mapper = new TicketMapper($this->db);
    $ticket = $mapper->getTicketById($ticket_id);

    $response->getBody()->write(var_export($ticket, true));
    return $response;
});

$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");
    $this->logger->addInfo("vc acessou o /name");
   

    return $response;
});



$app->run();