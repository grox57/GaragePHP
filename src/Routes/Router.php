<?php

namespace Routes;

use App\Utils\Response as UtilsResponse;
use Utils\Response;

class Router
{

  private array $routes = [];
  private Response $response;

  public function __construct()
  {
    $this->response = new Response();
  }

  public function get(string $path, array $handler): void
  {
    $this->addRoute('GET', $path, $handler);
  }

  public function post(string $path, array $handler): void
  {
    $this->addRoute('POST', $path, $handler);
  }
}
