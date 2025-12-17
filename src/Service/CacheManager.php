<?php

namespace XMVC\Service;

use Exception;
use XMVC\Cache\ArrayStore;
use XMVC\Cache\CacheStoreInterface;
use XMVC\Cache\FileStore;

class CacheManager
{
    /**
     * The configuration service.
     *
     * @var Config
     */
    protected $config;

    /**
     * The array of resolved cache stores.
     *
     * @var array
     */
    protected $stores = [];

    /**
     * Create a new CacheManager instance.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Get a cache store instance by name.
     *
     * @param  string|null  $name
     * @return CacheStoreInterface
     */
    public function store($name = null)
    {
        $name = $name ?: $this->config->get('cache.default', 'file');

        if (!isset($this->stores[$name])) {
            $this->stores[$name] = $this->resolve($name);
        }

        return $this->stores[$name];
    }

    /**
     * Resolve the given store.
     *
     * @param  string  $name
     * @return CacheStoreInterface
     * @throws Exception
     */
    protected function resolve($name)
    {
        $config = $this->config->get("cache.stores.{$name}");

        if (!$config) {
            throw new Exception("Cache store [{$name}] is not defined.");
        }

        $driver = $config['driver'] ?? 'file';

        switch ($driver) {
            case 'ArrayStore':
            case 'array':
                return new ArrayStore();
            case 'file':
                return new FileStore($config['path']);
            default:
                throw new Exception("Cache driver [{$driver}] is not supported.");
        }
    }
}