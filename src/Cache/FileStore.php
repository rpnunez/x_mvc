<?php

namespace XMVC\Cache;

class FileStore implements CacheStoreInterface
{
    protected $directory;

    public function __construct($directory)
    {
        $this->directory = $directory;
        if (!is_dir($this->directory)) {
            mkdir($this->directory, 0777, true);
        }
    }

    public function get($key)
    {
        $file = $this->getFilePath($key);

        if (file_exists($file)) {
            $content = file_get_contents($file);
            $data = unserialize($content);

            if ($data['expires_at'] > time()) {
                return $data['value'];
            }

            $this->forget($key);
        }

        return null;
    }

    public function put($key, $value, $seconds)
    {
        $file = $this->getFilePath($key);
        $data = [
            'value' => $value,
            'expires_at' => time() + $seconds
        ];
        return file_put_contents($file, serialize($data)) !== false;
    }

    public function forget($key)
    {
        $file = $this->getFilePath($key);
        if (file_exists($file)) {
            return unlink($file);
        }
        return false;
    }

    protected function getFilePath($key)
    {
        return $this->directory . '/' . sha1($key) . '.cache';
    }
}
