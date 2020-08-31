<?php


namespace Books\Middleware;


use Books\Model\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authorization = $request->getHeader('Authorization');
        $user = null;

        if ($authorization && count($authorization) > 0) {
            [$username, $password] = explode(':', base64_decode(str_replace('Basic ', '', $authorization[0])));

            $users = User::getUsers();

            foreach ($users as $u) {
                if ($u['username'] === $username && $u['password'] === $password) {
                    $user = $u;
                    $request = $request->withAttribute('user', $user);
                    break;
                }
            }
        }

        if (!$user) {
            return (new Response())->withStatus(401);
        }

        return $handler->handle($request);
    }
}