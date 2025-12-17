<?php

namespace XMVC\Event;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use XMVC\Event\Attribute\Listen;

class EventDiscoverer
{
    /**
     * The dispatcher instance.
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * Create a new EventDiscoverer instance.
     *
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Discover and register listeners from the given directory.
     *
     * @param  string  $directory
     * @param  string|null  $cachePath
     * @return void
     */
    public function discover($directory, $cachePath = null)
    {
        if ($cachePath && file_exists($cachePath)) {
            $this->loadCached($cachePath);
            return;
        }

        $discovered = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $class = $this->getClassFromFile($file->getPathname());

                if ($class && class_exists($class)) {
                    $events = $this->getEventsFromClass($class);
                    foreach ($events as $event) {
                        $this->dispatcher->listen($event, $class);
                        $discovered[$event][] = $class;
                    }
                }
            }
        }

        if ($cachePath) {
            $this->writeCache($cachePath, $discovered);
        }
    }

    /**
     * Get events from the Listen attribute on the class.
     *
     * @param  string  $class
     * @return array
     */
    protected function getEventsFromClass($class)
    {
        $events = [];
        $reflection = new ReflectionClass($class);
        $attributes = $reflection->getAttributes(Listen::class);

        foreach ($attributes as $attribute) {
            $instance = $attribute->newInstance();
            $events[] = $instance->event;
        }
        
        return $events;
    }

    /**
     * Load listeners from the cache file.
     *
     * @param  string  $cachePath
     * @return void
     */
    protected function loadCached($cachePath)
    {
        $listeners = require $cachePath;

        foreach ($listeners as $event => $eventListeners) {
            foreach ($eventListeners as $listener) {
                $this->dispatcher->listen($event, $listener);
            }
        }
    }

    /**
     * Write the discovered listeners to the cache file.
     *
     * @param  string  $cachePath
     * @param  array  $listeners
     * @return void
     */
    protected function writeCache($cachePath, $listeners)
    {
        $content = "<?php\n\nreturn " . var_export($listeners, true) . ";\n";
        file_put_contents($cachePath, $content);
    }

    /**
     * Extract the full class name from a PHP file.
     *
     * @param  string  $path
     * @return string|null
     */
    protected function getClassFromFile($path)
    {
        $contents = file_get_contents($path);
        $tokens = token_get_all($contents);
        $namespace = '';
        $class = '';
        $gettingNamespace = false;
        $gettingClass = false;

        foreach ($tokens as $token) {
            if (is_array($token)) {
                if ($token[0] == T_NAMESPACE) {
                    $gettingNamespace = true;
                } elseif ($token[0] == T_CLASS) {
                    $gettingClass = true;
                } elseif ($token[0] == T_NAME_QUALIFIED || $token[0] == T_STRING) {
                    if ($gettingNamespace) {
                        $namespace .= $token[1];
                    } elseif ($gettingClass) {
                        $class = $token[1];
                        break;
                    }
                }
            } elseif ($token === ';') {
                $gettingNamespace = false;
            }
        }

        return $class ? ($namespace ? $namespace . '\\' . $class : $class) : null;
    }
}