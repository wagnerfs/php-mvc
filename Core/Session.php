<?php

namespace Core;

class Session implements \ArrayAccess
{
    protected $session = [];
    protected $config = [];
    protected $use_cookies = false;
    protected $cookie_names = [];
    protected $cookie_lifetime = 0;
    
    public function __construct(array $config = []) {
        $this->config = $config;
    }
    
    public function start()
    {
        session_start();
        $this->session = &$_SESSION;
        if (isset($this->config['use_cookies']) && $this->config['use_cookies'])
        {
            $this->use_cookies = true;
            foreach ($_COOKIE as $key => $val)
            {
                $this->session[$key] = $val;
            }
            
            if (isset($this->config['cookie_names']))
            {
                $this->cookie_names = $this->config['cookie_names'];
            }
            if (isset($this->config['cookie_lifetime']))
            {
                $this->cookie_lifetime = $this->config['cookie_lifetime'];
            }
        }
    }
    
    public function destroy()
    {
        session_unset();
        session_destroy();
        foreach ($this->cookie_names as $name)
        {
            setcookie($name, null, 1);
        }
    }
    
    public function getParams()
    {
        return $this->session;
    }
    
    public function store()
    {
        if ($this->use_cookies && count($this->cookie_names) > 0)
        {
            foreach ($this->cookie_names as $name)
            {
                if (isset($this->session[$name]))
                {
                    setcookie($name, $this->session[$name], time() + $this->cookie_lifetime, '/');
                }
            }
        }
    }

    public function offsetExists($offset) {
        return isset($this->session[$offset]);
    }

    public function offsetGet($offset) {
        return $this->session[$offset];
    }

    public function offsetUnset($offset) {
        if (in_array($offset, $this->cookie_names))
        {
            setcookie($offset, '', time() - 3600, '/');
        }
        unset($this->session[$offset]);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->session[] = $value;
        }
        else {
            $this->session[$offset] = $value;
        }
    }

}
