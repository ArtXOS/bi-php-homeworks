<?php

use Books\Middleware\JsonBodyParserMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Books\Model\User;
use Books\Model\Book;
use Books\Database;
use Books\Middleware\AuthMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);
$app->add(new JsonBodyParserMiddleware());

/** Add new authorised user */
$app->post('/users', function (Request $request, Response $response, $args) {

    $user = $request->getParsedBody();
    $errors = 0;
    foreach (['username', 'password'] as $key) {
        if (!array_key_exists($key, $user) || empty($user[$key])) {
            $errors++;
        }
    }

    if ($errors > 0) {
        $response = $response->withStatus(400);
    } else {
        $id = User::createUser($user['username'], $user['password']);
        if($id != Null) {
            $response = $response
                ->withStatus(201)
                ->withHeader('Location', "/users/$id");
        } else {
            $response = $response
                ->withStatus(409)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    return $response;

});

$app->post('/books', function (Request $request, Response $response, $args) {

    $book = $request->getParsedBody();
    $errors = 0;
    foreach (['name', 'author', 'publisher', 'isbn', 'pages'] as $key) {
        if (!array_key_exists($key, $book) || empty($book[$key])) {
            $errors++;
        }
    }

    if ($errors > 0) {
        $response = $response->withStatus(400);
    } else {
        $id = Book::addBook($book['name'], $book['author'], $book['publisher'], $book['isbn'], $book['pages'] );
        if($id != Null) {
            $response = $response
                ->withStatus(201)
                ->withHeader('Location', "/books/$id");
        } else {
            $response = $response
                ->withStatus(409)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    return $response;

})->addMiddleware(new AuthMiddleware());

$app->get('/books/{id}', function (Request $request, Response $response, $args){

    $book = Book::findBookById(intval($args['id']));

    if (!$book) {
        $response = $response->withStatus(404);
    } else {
        $payload = json_encode($book);
        $response->getBody()->write($payload);
        $response = $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    return $response;

});

$app->put('/books/{id}', function (Request $request, Response $response, $args) {

    $newData = $request->getParsedBody();
    $errors = 0;

    foreach (['name', 'author', 'publisher', 'isbn', 'pages'] as $key) {
        if (!array_key_exists($key, $newData) || empty($newData[$key])) {
            $errors++;
        }
    }

    if ($errors > 0) {
        $response = $response
            ->withStatus(400)
            ->withHeader('Content-Type', 'application/json');
    } else {

        $book = Book::findBookById(intval($args['id']));

        if (!$book) {
            $response = $response->withStatus(404);
        } else {
            Book::updateBookWithId($args['id'], $newData['name'], $newData['author'], $newData['publisher'], $newData['isbn'], $newData['pages']);
            $response = $response->withStatus(204);
        }

    }

    return $response;

})->addMiddleware(new AuthMiddleware());

$app->get('/books', function (Request $request, Response $response, $args) {
    $books = Book::getAllBooks();
    $payload = json_encode($books);
    $response->getBody()->write($payload);
    $response = $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    return $response;
});

$app->delete('/books/{id}', function (Request $request, Response $response, $args) {

    if(!Book::findBookById(intval($args['id']))) {
        $response = $response->withStatus(404);
    } else {
        Book::deleteBookWithId(intval($args['id']));
        $response = $response->withStatus(204);
    }

    return $response;

})->addMiddleware(new AuthMiddleware());

$app->run();
