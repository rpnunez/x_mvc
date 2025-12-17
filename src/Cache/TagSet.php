<?php

namespace XMVC\Cache;

class TagSet
{
    /**
     * The cache store implementation.
     *
     * @var CacheStoreInterface
     */
    protected $store;

    /**
     * The tag names.
     *
     * @var array
     */
    protected $names;

    /**
     * Create a new TagSet instance.
     *
     * @param  CacheStoreInterface  $store
     * @param  array  $names
     */
    public function __construct(CacheStoreInterface $store, array $names)
    {
        $this->store = $store;
        $this->names = $names;
    }

    /**
     * Reset all tags in the set.
     *
     * @return void
     */
    public function reset()
    {
        foreach ($this->names as $name) {
            $this->store->forget($this->tagKey($name));
        }
    }

    /**
     * Get the unique tag identifier for a given tag.
     *
     * @param  string  $name
     * @return string
     */
    public function tagId($name)
    {
        $key = $this->tagKey($name);
        $id = $this->store->get($key);

        if (is_null($id)) {
            $id = uniqid('', true);
            $this->store->put($key, $id, 315360000); // ~10 years
        }

        return $id;
    }

    /**
     * Get the namespace for the tag set.
     *
     * @return string
     */
    public function getNamespace()
    {
        return implode('|', array_map([$this, 'tagId'], $this->names));
    }

    /**
     * Get the tag identifier key.
     *
     * @param  string  $name
     * @return string
     */
    public function tagKey($name)
    {
        return 'tag_version_' . $name;
    }
}