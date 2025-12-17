<?php

namespace XMVC\Listener;

use XMVC\Event\Attribute\Listen;
use XMVC\Event\CsrfTokenGenerated;

#[Listen(CsrfTokenGenerated::class)]
class LogCsrfToken
{
    /**
     * Handle the event.
     *
     * @param  CsrfTokenGenerated  $event
     * @return void
     */
    public function handle(CsrfTokenGenerated $event)
    {
        $logFile = BASE_PATH . '/storage/logs/csrf.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) mkdir($logDir, 0777, true);

        file_put_contents($logFile, '[' . date('Y-m-d H:i:s') . '] Token generated: ' . $event->token . PHP_EOL, FILE_APPEND);
    }
}