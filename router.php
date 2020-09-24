<?php
class Router
{
    private $parentRoute;
    private $alreadyExecuted = false;

    public function __construct(string $parentRoute = '/')
    {
        $this->parentRoute = $parentRoute;
    }

    public function new(string $route, $callback, array $methods = ['GET'], array $options = []): ?bool
    {
        if ($this->alreadyExecuted) {
            return false;
        }

        $methodCheck = in_array($_SERVER['REQUEST_METHOD'], $methods);

        if (!$methodCheck) {
            return false;
        }

        $urlPieces = array_filter(explode('/', $route), fn ($value) => !is_null($value) && $value !== '');

        $url = $this->parentRoute . implode('/', array_map(function ($element) {
            return preg_replace('/\{(.*?)\}/', '?(?<' . trim($element, '{}') . '>[a-zA-Z0-9]*)', $element);
        }, $urlPieces));

        $pattern = '^' . str_replace('/', '\/', preg_replace('/\{(.*?)\}/', '(?<param>.*)', $url)) . '\/?$';

        if (1 !== preg_match('/' . $pattern . '/', $_SERVER['REQUEST_URI'], $params)) {
            return false;
        }

        if (isset($options['DEFAULT_VALUES'])) {
            foreach ($options['DEFAULT_VALUES'] as $k => $v) {
                if (isset($params[$k]) === false || $params[$k] == '') {
                    $params[$k] = $v;
                }
            }
        }

        $response = $callback($params);

        $this->alreadyExecuted = true;
        return true;
    }
}