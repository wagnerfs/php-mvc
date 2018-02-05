<?php

namespace Core;

class Router
{
    /**
     *
     * @var array
     */
    protected $appConfig = [];
    
    /**
     *
     * @var ServiceConfig
     */
    protected $serviceConfig = null;
    
    /**
     *
     * @var array
     */
    protected $routes = [];
    
    public function __construct(array $appConfig = array()) {
        $this->appConfig = $appConfig;
        foreach ($this->appConfig['routes'] as $key => $val)
        {
            $patterns = ['/\{([a-z]+)\}/', '/\{([a-z]+):[^\}]+\}/'];
            $capture = [];
            foreach ($patterns as $pattern)
            {
                preg_match_all($pattern, $key, $matches);
                if (count($matches[1]) > 0)
                {
                    $capture = array_merge($capture, $matches[1]);
                }
            }
            
            if (count($capture) > 0)
            {
                $val['capture'] = $capture;
            }
            
            $key = preg_replace('/\//', '\\/', $key);
            $key = preg_replace('/\{([a-z]+)\}/', '(.+)', $key);
            $key = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(\2)', $key);
            $key = '/^'.$key.'$/i';
            
            $this->routes[$key] = $val;
        }
    }
    
    public function dispatch($url)
    {
        if ($url != '')
        {
            $sessionConfig = [];
            if (isset($this->appConfig['session']))
            {
                $sessionConfig = $this->appConfig['session'];
            }
            $session = new Session($sessionConfig);
            $session->start();
            
            $render = (isset($this->appConfig['view']['render']) ? $this->appConfig['view']['render'] : 'php' );
            $ext = '.'.$render;
            
            $layout = '';
            if (isset($this->appConfig['view']['layout']))
            {
                $layout = 'App/View/'.$this->appConfig['view']['layout'].$ext;
                if (!file_exists($layout))
                {
                    throw new \Exception("Layout file not found: ".$layout);
                }
            }
            
            
            $query = '';
            $parts = explode('?', $url, 2);
            if (count($parts) > 1)
            {
                $query = $parts[1];
            }
            if (strlen($parts[0]) > 1 && substr($parts[0], -1) === '/')
            {
                $parts[0] = substr($parts[0], 0, -1);
            }
            
            $captured = [];
            $route = null;
            $matched = $this->match($parts[0], $route, $captured);
            
            if ($matched)
            {
                $controllerClass = 'App\\Controller\\'.$route['controller'].'Controller';
                $action = $route['action'].'Action';
                $queryArr = array_merge_recursive($captured, $this->queryStringToArray($query));
                $post = [];
                $post = array_merge_recursive($post, $_POST);
                $post = array_merge_recursive($post, $_FILES);
                
                if (class_exists($controllerClass))
                {
                    $routeParams = new RouteParams($queryArr, $post);
                    $controller = new $controllerClass($routeParams, $this->serviceConfig, $session);
                    $reflection = new \ReflectionMethod($controller, $action);
                    if (!$reflection->isPublic())
                    {
                        throw new \Exception(get_class($controller)."::".$action." is not public");
                    }
                    $view = $controller->$action();
                    $session->store();
                    if ($controller->redirected())
                        return;
                    if ($view instanceof View)
                    {
                        $template = $view->getTemplate();
                        if ($template == '')
                        {
                            $template = $route['action'].$ext;
                        }
                        else if ($view->getAutoext())
                        {
                            $template = $template.$ext;
                        }
                        $template = 'App/View/'.$template;
                        if (file_exists($template))
                        {
                            if ($view->isConsole())
                            {
                                $layout = '';
                            }
                            new Render($this->serviceConfig, $layout, $template, $view->getVariables(), $render, $view->getHeaders());
                        }
                        else
                        {
                            throw new \Exception("Template file not found: ".$template);
                        }
                            
                    }
                    else
                    {
                        throw new \Exception("Action must return a view: ".$controllerClass."::".$action);
                    }
                }
                else
                {
                    throw new \Exception("Controller not found: ".$controllerClass);
                }
            }
            else
            {
                if (isset($this->appConfig['view']['404']))
                {
                    $template = 'App/View/'.$this->appConfig['view']['404'].$ext;
                    if (file_exists($template))
                    {
                        new Render($this->serviceConfig, $layout, $template, ['route' => $parts[0]], $render);
                    }
                    else
                    {
                        throw new \Exception("404 template file not found: ".$template);
                    }
                }
                else
                {
                    throw new \Exception("Route not found: ".$parts[0]);
                }
            }
        }
    }
    
    protected function match($url, &$route, &$capture)
    {
        foreach ($this->routes as $route_ => $params)
        {
            if (preg_match($route_, $url, $matches))
            {
                if (count($matches) > 1)
                {
                    foreach (array_slice($matches, 1) as $key => $val)
                    {
                        $captured[$params['capture'][$key]] = $val;
                    }
                    $capture = $captured;
                }
                $route = $params;
                return true;
            }
        }
        return false;
    }
    
    protected function queryStringToArray($query)
    {
        $result = [];
        $list = explode('&', $query);
        foreach ($list as $value)
        {
            $pair = explode('=', $value);
            if (count($pair) > 1)
            {
                $result[$pair[0]] = $pair[1];
            }
            else
            {
                $result[$pair[0]] = null;
            }
        }
        return $result;
    }
    
    /**
     * 
     * @return ServiceConfig
     */
    protected function getServiceConfig()
    {
        return $this->serviceConfig;
    }
    
    public function setServiceConfig(ServiceConfig $serviceConfig)
    {
        $this->serviceConfig = $serviceConfig;
    }
}
