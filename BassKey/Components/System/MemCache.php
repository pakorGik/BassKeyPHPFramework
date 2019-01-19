<?php

namespace BassKey\Components\System;


class MemCache
{
    private $memcache;

    public function getMemcache()
    {
        return $this->memcache;
    }

    public function get($key)
    {
        return $this->memcache->get($key);
    }

    public function set($key , $var, $expire = 120)
    {
        return $this->memcache->set($key, $var, false, $expire);
    }

    public function increment($key, $on = 1)
    {
        return $this->memcache->increment($key, $on);
    }

    public function delete($key)
    {
        return $this->memcache->delete($key);
    }

    public function addServers($config)
    {
        if(!is_array($config))
        {
            return false;
        }

        foreach($config as $server)
        {
            if($server['host'] === null || $server['port'] === null
                || $server['persistent'] === null || $server['weight'] === null)
            {
                continue;
            }

            $this->addServer($server['host'], $server['port'], $server['persistent'], $server['weight']);
        }

        return true;
    }

    public function addServer($host, $port = 11211, $persistent = true, $weight = 1)
    {
        $this->memcache->addserver($host, $port, $persistent, $weight);
    }

    public function __construct()
    {
        $this->memcache = new \Memcache();
    }

}
