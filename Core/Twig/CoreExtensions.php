<?php

namespace Core\Twig;

class CoreExtensions extends \Twig_Extension
{
    public function getFunctions()
    {
    }
    
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('preg_replace', [$this, 'pregReplace'])
        ];
    }
    
    public function pregReplace($subject, $pattern , $replacement)
    {
        return @preg_replace($pattern, $replacement, $subject);
    }

    public function getName()
    {
        return 'core_extensions_twig_extension';
    }
}

