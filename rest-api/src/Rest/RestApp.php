<?php declare(strict_types=1);

namespace Books\Rest;

use Books\BookRepository;
use Books\Middleware\AuthMiddleware;
use Books\Middleware\JsonBodyParserMiddleware;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class RestApp
{
    private ?App $app = null;

    public function configure(): void
    {
        $this->app = AppFactory::create();

        $this->app->addRoutingMiddleware();
        $this->app->addErrorMiddleware(true, true, true);
        $this->app->add(new JsonBodyParserMiddleware());

        $this->app->get('/', function (Request $request, Response $response) {
            $response->getBody()->write('Funguje to! Ale nic tady není.');
            return $response;
        });

        $this->app->get('/books', function (Request $request, Response $response) {
            $bookRepository = new BookRepository();
            $books = $bookRepository->getAllBooks();
            $response->getBody()->write(json_encode($books));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        });

        $this->app->get('/books/{id}', function (Request $request, Response $response, array $args) {
            $bookRepository = new BookRepository();
            $book = $bookRepository->getBookById($args['id']);

            if ($book === null) {
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
            } else if (empty($book)) {
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);
            } else {
                $response->getBody()->write(json_encode($book));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            }
        });

        $this->app->post('/books', function (Request $request, Response $response) {
            $bookRepository = new BookRepository();
            $book = $bookRepository->createBook($request->getParsedBody());
            if (empty($book)) {
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
            } else {
                $response->getBody()->write(json_encode($book));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(201)
                    ->withHeader('Location', '/books/' . $book['id']);
            }
        })->add(new AuthMiddleware());

        $this->app->put('/books/{id}', function (Request $request, Response $response, array $args) {
            $bookRepository = new BookRepository();
            $book = $bookRepository->updateBook($request->getParsedBody(), $args['id']);
            if ($book === null) {
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);
            } else if (empty($book)) {
                return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
            } else {
                $response->getBody()->write(json_encode($book));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(204);
            }
        })->add(new AuthMiddleware());

        $this->app->delete('/books/{id}', function (Request $request, Response $response, array $args) {
            $bookRepository = new BookRepository();
            $book = $bookRepository->deleteBook($args['id']);
            if ($book === null) {
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);
            } else {
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(204);
            }
        })->add(new AuthMiddleware());
    }

    public function run(): void {
        $this->app->run();
    }

    public function getApp(): App {
        return $this->app;
    }
}
