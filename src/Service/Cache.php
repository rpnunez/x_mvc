<?php

namespace XMVC\Service;

use Exception;
use XMVC\Cache\CacheStoreInterface;
use XMVC\Cache\TaggedCache;
use XMVC\Cache\TagSet;

/**
 * Service for managing cache operations.
 */
class Cache
{
    /**
     * The cache manager instance.
     *
     * @var CacheManager
     */
    protected $manager;

    /**
     * The active cache store implementation.
     *
     * @var CacheStoreInterface
     */
    protected $store;

    /**
     * Cache constructor.
     *
     * @param CacheManager $manager The cache manager service.
     */
    public function __construct(CacheManager $manager)
    {
        $this->manager = $manager;
        $this->store = $manager->store();
    }

    /**
     * Retrieve an item from the cache.
     *
     * @param string $key     The cache key.
     * @param mixed  $default The default value to return if the key does not exist or is expired.
     *
     * @return mixed The cached value or the default value.
     */
    public function get($key, $default = null)
    {
        return $this->store->get($key) ?? $default;
    }

    /**
     * Store an item in the cache.
     *
     * @param string $key     The cache key.
     * @param mixed  $value   The value to store.
     * @param int    $seconds The number of seconds to store the item.
     *
     * @return bool True on success, false on failure.
     */
    public function put($key, $value, $seconds = 3600)
    {
        return $this->store->put($key, $value, $seconds);
    }

    /**
     * Remove an item from the cache.
     *
     * @param string $key The cache key.
     *
     * @return bool True if the file was deleted, false otherwise.
     */
    public function forget($key): bool
    {
        return $this->store->forget($key);
    }

    /**
     * Begin executing a new tags operation.
     *
     * @param  array|mixed  $names
     * @return TaggedCache
     */
    public function tags($names)
    {
        $names = is_array($names) ? $names : func_get_args();
        return new TaggedCache($this->store, new TagSet($this->store, $names));
    }
}