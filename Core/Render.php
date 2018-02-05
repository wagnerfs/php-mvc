<?php

namespace Core;

class Render
{
    private $__data;
    
    public function __construct(ServiceConfig &$config = null, $layout, $template, array $data, $render = 'php', array $headers = array()) {
        $this->__data = $data;
        
        foreach ($headers as $key => $val)
        {
            header($key.': '.$val);
        }
        
        switch ($render)
        {
            case 'twig':
                $this->renderTwig($config, $layout, $template);
                break;
            case 'php':
            default:
                $this->renderPhp($config, $layout, $template);
                break;
        }
            
    }
    
    private function renderPhp(ServiceConfig &$config = null, $layout, $template, array $headers = array())
    {
        if ($layout != '')
        {
            $this->__data['content'] = $this->getFileContent($template);
            require $layout;
        }
        else
        {
            echo $this->getFileContent($template);
        }
    }
    
    private function renderTwig(ServiceConfig &$config = null, $layout, $template, array $headers = array())
    {
        $twigConfig = [];
        if ($config != null)
        {
            $twigConfig = $config->get('twig');
        }
        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem('/'), $twigConfig);
        $twig->addExtension(new \Twig_Extension_Debug());
        $twig->addExtension(new \Core\Twig\CoreExtensions());
        
        if ($layout != '')
        {            
            $text = file_get_contents($template);
            $template = $twig->createTemplate("{% extends '".$layout."' %}\n".$text);
            echo $template->render($this->__data);
        }
        else
        {
            echo $twig->render($template, $this->__data);
        }
    }
    
    public function __get($name)
    {
        if (!array_key_exists($name, $this->__data))
        {
            throw new \Exception("Undefined variable: ".$name);
        }
        return $this->__data[$name];
    }
    
    public function __isset($name)
    {
        return array_key_exists($name, $this->__data);
    }
    
    private function getFileContent($file){
        ob_start();
        include $file;
        return ob_get_clean();
    }
}