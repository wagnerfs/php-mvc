<?php

namespace Core;

class ErrorHandler
{
    private $config;
    private $serviceConfig = null;
    
    public function __construct(ServiceConfig &$serviceConfig = null, array $appConfig = array()) {
        if (key_exists('view', $appConfig))
        {
            $this->config = $appConfig['view'];
        }
        $this->serviceConfig = $serviceConfig;
    }
    
    public function error($level, $message, $file, $line)
    {
        throw new \ErrorException($message, 0, $level, $file, $line);
    }
    
    public function exception($exception)
    {
        ob_get_clean();
        $code = $exception->getCode();
        if ($code != 404) {
            $code = 500;
        }
        http_response_code($code);
        
        $render = (isset($this->config['render']) ? $this->config['render'] : 'php' );
        $ext = '.'.$render;
        
        $layout = '';
        if (isset($this->config['layout']))
        {
            $layout = 'App/View/'.$this->config['layout'].$ext;
        }
        
        $template = 'Core/ErrorPage.php';
        if (isset($this->config['exception']))
        {
            $template = 'App/View/'.$this->config['exception'].$ext;
        }
        
        $stack = [];
        foreach ($exception->getTrace() as $id => $trace)
        {
            $row = [];
            $exec = $trace['function'].'(';
            if (isset($trace['class']))
            {
                $exec = $trace['class'].$trace['type'].$exec;
            }
            
            $args = [];
            if (isset($trace['args']))
            {
                foreach ($trace['args'] as $arg)
                {
                    if (gettype($arg) == 'string')
                    {
                        if (strlen($arg) > 18) $arg = substr($arg, 0, 15).'...';
                        $args[] = '\''.$arg.'\'';
                    }
                    else if (is_object($arg))
                        $args[] = get_class($arg);
                    else if (gettype($arg) != 'array')
                        $args[] = $arg;
                    else
                        $args[] = 'Array';
                }
            }
            
            if (isset($trace['file'])) $row['file'] = $trace['file'];
            if (isset($trace['line'])) $row['line'] = $trace['line'];
            $row['args'] = $exec.implode(', ', $args);
            
            $stack[] = $row;
        }
        
        new Render($this->serviceConfig, $layout, $template, ['exception' => $exception, 'class' => get_class($exception), 'stack' => $stack], $render);
    }
}
