<?php

namespace XMVC\Cache;

class TaggedCache implements CacheStoreInterface
{
    /**
     * The cache store implementation.
     *
     * @var CacheStoreInterface
     */
    protected $store;

    /**
     * The tag set instance.
     *
     * @var TagSet
     */
    protected $tags;

    /**
     * Create a new TaggedCache instance.
     *
     * @param  CacheStoreInterface  $store
     * @param  TagSet  $tags
     */
    public function __construct(CacheStoreInterface $store, TagSet $tags)
    {
        $this->store = $store;
        $this->tags = $tags;
    }

    /**
     * Retrieve an item from the cache.
     */
    public function get($key)
    {
        return $this->store->get($this->taggedItemKey($key));
    }

    /**
     * Store an item in the cache.
     */
    public function put($key, $value, $seconds)
    {
        return $this->store->put($this->taggedItemKey($key), $value, $seconds);
    }

    /**
     * Remove an item from the cache.
     */
    public function forget($key)
    {
        return $this->store->forget($this->taggedItemKey($key));
    }

    /**
     * Flush all items in the tag set.
     */
    public function flush()
    {
        $this->tags->reset();
    }

    /**
     * Get the fully qualified key for a tagged item.
     *
     * @param  string  $key
     * @return string
     */
    protected function taggedItemKey($key)
    {
        return sha1($this->tags->getNamespace()) . ':' . $key;
    }
}