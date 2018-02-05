<?php

namespace Core;

class AbstractController
{
    /**
     *
     * @var ServiceConfig
     */
    private $serviceConfig = null;
    
    /**
     *
     * @var RouteParams
     */
    private $params = null;
    
    /**
     *
     * @var boolean
     */
    private $redirect = false;
    
    /**
     *
     * @var Session
     */
    private $session = null;
    
    public function __construct(RouteParams $params = null, ServiceConfig $serviceConfig = null, Session $session = null)
    {
        $this->params = $params;
        $this->serviceConfig = $serviceConfig;
        $this->session = $session;
    }
    
    /**
     * 
     * @return ServiceConfig
     */
    protected function getServiceConfig()
    {
        return $this->serviceConfig;
    }
    
    /**
     * 
     * @return RouteParams
     */
    protected function getParams()
    {
        return $this->params;
    }
    
    public function __call($name, $arguments) {
        $method = $name;
        if (method_exists($this, $method))
        {
            call_user_func_array([$this, $method], $arguments);
        }
        else
        {
            throw new \Exception("Method not found: ".get_class($this)."::".$method);
        }
    }
    
    /**
     * Forces page redirection with optional added data.
     * 
     * @param string $route
     * @param array $query The data to be passed as GET
     * @param type $https Whether to redirect to HTTPS or not
     */
    public function redirect($route, array $query = array(), $https = false)
    {
        $queryList = [];
        foreach ($query as $key => $val)
        {
            $queryList[] = $key.'='.$val;
        }
        if (count($queryList) > 0)
        {
            $route .= '?'.implode('&', $queryList);
        }
        
        $server = '';
        if ($https && !isset($_SERVER['HTTPS']))
        {
            try
            {
                $serverConf = $this->serviceConfig->get('server');
                if ($_SERVER['SERVER_PORT'] != $serverConf['port']['https'])
                {
                    $server = 'https://'.$_SERVER['SERVER_NAME'].':'.$serverConf['port']['https'];
                }
            }
            catch (\Exception $e)
            {

            }
        }
        
        $this->redirect = true;
        header('Location: '.$server.$route);
    }
    
    /**
     * Checks if the method redirect was called
     * 
     * @return boolean
     */
    public function redirected()
    {
        return $this->redirect;
    }
    
    /**
     * 
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }
}
