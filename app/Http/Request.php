<?php

namespace App\Http;

class Request
{
    protected $uri;
    protected $method;
    protected $params;
    protected $body;
    protected $session;
    protected $cookies;
    protected $headers;
    protected $files;

    public function __construct()
    {
        $this->uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->params = $_GET;
        $this->body = $_POST;
        $this->session = new Session();
        $this->cookies = $_COOKIE;
        $this->headers = getallheaders();
        $this->files = $_FILES;
    }

    public function uri()
    {
        return $this->uri;
    }

    public function method()
    {
        return $this->method;
    }

    public function input($key, $default = null)
    {
        return $this->body[$key] ?? $this->params[$key] ?? $default;
    }

    public function all()
    {
        return array_merge($this->params, $this->body);
    }

    public function cookie($key, $default = null)
    {
        return $this->cookies[$key] ?? $default;
    }

    public function hasCookie($key)
    {
        return isset($this->cookies[$key]);
    }

    public function file($key)
    {
        return $this->files[$key] ?? null;
    }

    public function hasFile($key)
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK;
    }

    public function header($key, $default = null)
    {
        $key = str_replace('-', '_', strtoupper($key));
        return $this->headers[$key] ?? $default;
    }

    public function hasHeader($key)
    {
        $key = str_replace('-', '_', strtoupper($key));
        return isset($this->headers[$key]);
    }
}