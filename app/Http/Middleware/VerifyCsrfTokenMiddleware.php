<?php

namespace App\Http\Middleware;

use App\Http\Request;
use XMVC\Service\Session;
use XMVC\Service\Config;

class VerifyCsrfTokenMiddleware
{
    protected $config;
    protected $tokenName;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->tokenName = $this->config->get('security.CsrfTokenName', '_token');
    }

    public function handle(Request $request, $next)
    {
        Session::start();

        if ($request->method() === 'POST') {
            if (!$this->verifyCsrfToken($request)) {
                throw new \Exception('CSRF token mismatch.');
            }
        } else {
            // For GET requests, ensure a CSRF token exists in the session
            if (
                !Session::has($this->tokenName) ||
                !is_string(Session::get($this->tokenName)) ||
                empty(Session::get($this->tokenName))
            ) {
                $this->generateCsrfToken();
            }
        }

        return $next($request);
    }

    protected function verifyCsrfToken(Request $request)
    {
        $token = $request->input($this->tokenName);
        $sessionToken = Session::get($this->tokenName);

        if (empty($token) || empty($sessionToken)) {
            return false;
        }

        return hash_equals($token, $sessionToken);
    }

    protected function generateCsrfToken()
    {
        $token = bin2hex(random_bytes(32));
        Session::set($this->tokenName, $token);
        return $token;
    }
}