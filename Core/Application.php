<?php

namespace Core;

class Application
{
    /**
     *
     * @var Router
     */
    protected $router;
    
    /**
     *
     * @var ServiceConfig
     */
    protected $serviceConfig;
    
    public function __construct()
    {
        $config = [];
        foreach (glob('config/autoload/*.php') as $filename)
        {
            $included = (include $filename);
            if (is_array($included))
            {
                $config = $this->array_merge_recursive_distinct($config, $included);
            }
        }
        $this->serviceConfig = new ServiceConfig($config);
        $appConfig = (require 'config/application.php');
        
        $errorHandler = new ErrorHandler($this->serviceConfig, $appConfig);
        set_error_handler([$errorHandler, 'error']);
        set_exception_handler([$errorHandler, 'exception']);
        
        if (isset($config['db']))
        {
            Database::setParams($config['db']['dsn'], $config['db']['username'], $config['db']['password']);
        }
        $this->router = new Router($appConfig);
        $this->router->setServiceConfig($this->serviceConfig);
    }
    
    /**
     * Restarts the application using HTTPS.
     * 
     * If server/port/https key is set in the autoloaded configs, it'll use it
     * instead of the default port
     */
    public function forceHTTPS()
    {
        if (!isset($_SERVER['HTTPS']))
        {
            $port = 443;
            $config = $this->serviceConfig->get();
            if (isset($config['server']['port']['https']))
                $port = $config['server']['port']['https'];

            if ($_SERVER['SERVER_PORT'] != $port)
            {
                $server = 'https://'.$_SERVER['SERVER_NAME'].':'.$port.$_SERVER["REQUEST_URI"];
                header('Location: '.$server);
            }
        }
    }
    
    public function run()
    {
        if (isset($this->router))
        {
            $this->router->dispatch($_SERVER['REQUEST_URI']);
        }
    }
    
    protected function array_merge_recursive_distinct(array &$array1, array &$array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value)
        {
            if (is_array($value) && isset($merged [$key]) && is_array($merged [$key]))
            {
                $merged [$key] = $this->array_merge_recursive_distinct($merged [$key], $value);
            }
            else
            {
                $merged [$key] = $value;
            }
        }

        return $merged;
    }

}
