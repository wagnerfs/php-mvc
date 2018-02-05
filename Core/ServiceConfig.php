<?php

namespace Core;

class ServiceConfig
{
    /**
     *
     * @var array
     */
    protected $config;
    
    public function __construct(array $config = array())
    {
        $this->config = $config;
    }

    /**
     * Retrieves the autoloaded config array.
     * 
     * @param string $group
     * @return array
     * @throws \Exception If $group is not empty and not found in the config
     * directory
     */
    public function get($group = '')
    {
        if ($group != '')
        {
            if (isset($this->config[$group]))
                return $this->config[$group];
            throw new \Exception($group.' group not found in the autoload files: '.get_class($this).'::getConfig');
        }
        return $this->config;
    }
}

