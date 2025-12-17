<?php

namespace XMVC\Service;

/**
 * Service for logging messages to the filesystem.
 */
class Log
{
    /**
     * The configuration service instance.
     *
     * @var Config
     */
    protected $config;

    /**
     * Create a new Log instance.
     *
     * @param Config $config The configuration service.
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Write a message to the log.
     *
     * @param string $level   The log level (e.g., 'info', 'error').
     * @param string $message The message to log.
     *
     * @return void
     */
    public function write($level, $message)
    {
        $channel = $this->config->get('log.default');
        $logFile = $this->config->get("log.channels.{$channel}.path");

        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $message = sprintf(
            "[%s] %s: %s" . PHP_EOL,
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $message
        );

        file_put_contents($logFile, $message, FILE_APPEND);
    }

    /**
     * Log an informational message.
     *
     * @param string $message The message to log.
     *
     * @return void
     */
    public function info($message)
    {
        $this->write('info', $message);
    }

    /**
     * Log an error message.
     *
     * @param string $message The message to log.
     *
     * @return void
     */
    public function error($message)
    {
        $this->write('error', $message);
    }
}