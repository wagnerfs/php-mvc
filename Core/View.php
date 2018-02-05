<?php

namespace Core;

class View
{
    private $captureTo = 'content';
    private $variables = [];
    private $template = '';
    private $autoext = true;
    private $headers = [];
    private $console = false;
    
    public function __construct(array $variables = null)
    {
        if ($variables != null)
        {
            $this->variables = $variables;
        }
    }
    
    /**
     * Sets raw HTTP headers.
     * 
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }
    
    public function getHeaders()
    {
        return $this->headers;
    }
    
    public function getAutoext()
    {
        return $this->autoext;
    }
    
    /**
     * 
     * @param type $template The template path
     * @param type $autoext If TRUE, the extension will be appended to the
     * template path based on the render engine chosen
     */
    public function setTemplate($template, $autoext = true)
    {
        $this->template = $template;
        $this->autoext = $autoext;
    }
    
    public function getTemplate()
    {
        return $this->template;
    }
    
    public function setVariables(array $variables)
    {
        $this->variables = $variables;
    }
    
    public function getVariables()
    {
        return $this->variables;
    }
    
    public function setConsole($console)
    {
        $this->console = (bool)$console;
    }

    public function isConsole()
    {
        return $this->console;
    }
    
    public function captureTo()
    {
        return $this->captureTo;
    }

}

