<?php
namespace App;

use AltoRouter;

class Router{

    private $path;

    /**
     * @var AltoRouter
     */

    private $router;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->router = new AltoRouter();
    }

    public function get(string $url , string $PathLink , string $name):self{

        $this->router->map('GET', $url, $PathLink, $name);
        return $this;
    }

    public function post(string $url , string $PathLink , string $name):self{

        $this->router->map('POST', $url, $PathLink, $name);
        return $this;
    }
    public function url(string $title , array $params=[]) : string
    {
        return $this->router->generate($title, $params);
    }

    public function run()
    {
        $match = $this->router->match();
        if($match === false){
            require $this->path.DIRECTORY_SEPARATOR.'404.php';
        }else{
            $view = $match['target'];
            require $this->path.DIRECTORY_SEPARATOR.$view.'.php';
        }
    }
}