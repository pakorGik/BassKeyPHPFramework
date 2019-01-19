<?php

namespace BassKey\Components\System;


class Redis
{
    private $redis;

    public function geRedis()
    {
        return $this->redis;
    }

    public function get($key)
    {
        return $this->redis->get($key);
    }

    public function set($key , $var, $expire = 120)
    {
        $this->redis->set($key , $var, $expire);
    }

    public function addServers($config)
    {
        foreach($config as $server)
        {
            if($server['host'] === null || $server['port'] === null
                || $server['persistent'] === null || $server['weight'] === null)
            {
                continue;
            }

            $this->addServer($server['host'], $server['port']);
        }
    }

    public function addServer($host, $port = 11211)
    {
        $this->redis->pconnect($host, $port);
    }

    public function __construct()
    {
        $this->redis = new \Redis();
    }

}
