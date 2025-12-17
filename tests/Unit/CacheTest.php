<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use XMVC\Cache\ArrayStore;
use XMVC\Cache\FileStore;

class CacheTest extends TestCase
{
    public function test_array_store()
    {
        $store = new ArrayStore();
        $store->put('foo', 'bar', 10);
        $this->assertEquals('bar', $store->get('foo'));
        
        $store->forget('foo');
        $this->assertNull($store->get('foo'));
    }

    public function test_file_store()
    {
        $path = sys_get_temp_dir() . '/xmvc_cache_test';
        $store = new FileStore($path);
        
        $store->put('foo', 'bar', 10);
        $this->assertEquals('bar', $store->get('foo'));
        
        $store->forget('foo');
        $this->assertNull($store->get('foo'));
        
        // Clean up
        array_map('unlink', glob("$path/*"));
        rmdir($path);
    }
}
