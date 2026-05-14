<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<string, callable> */
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET ' . $this->normalize($path)] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST ' . $this->normalize($path)] = $handler;
    }

    public function dispatch(string $method, string $uri): mixed
    {
        $path = $this->normalize(parse_url($uri, PHP_URL_PATH) ?: '/');
        $key  = strtoupper($method) . ' ' . $path;

        if (isset($this->routes[$key])) {
            return ($this->routes[$key])([]);
        }

        foreach ($this->routes as $route => $handler) {
            [$m, $pattern] = explode(' ', $route, 2);
            if ($m !== strtoupper($method)) {
                continue;
            }
            $regex = '#^' . preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $pattern) . '$#';
            if (preg_match($regex, $path, $m2)) {
                $params = array_filter($m2, 'is_string', ARRAY_FILTER_USE_KEY);
                return $handler($params);
            }
        }

        http_response_code(404);
        return $this->notFound();
    }

    private function normalize(string $p): string
    {
        $p = '/' . trim($p, '/');
        return $p === '' ? '/' : $p;
    }

    private function notFound(): string
    {
        return View::render('pages/404', ['title' => 'Nie znaleziono strony']);
    }
}
