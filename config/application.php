<?php

return array(
    'routes' => array(
        '/' => array(
            'controller' => 'Test',
            'action' => 'home'
        ),
        '/home' => array(
            'controller' => 'Test',
            'action' => 'home'
        ),
        '/number/{number:\d+}' => array(
            'controller' => 'Test',
            'action' => 'number'
        ),
        '/test/list' => array(
            'controller' => 'Test',
            'action' => 'testList'
        ),
        '/test/insert' => array(
            'controller' => 'Test',
            'action' => 'testInsert'
        ),
        '/test/delete/{id:\d+}' => array(
            'controller' => 'Test',
            'action' => 'testDelete'
        ),
        '/session' => array(
            'controller' => 'Test',
            'action' => 'session'
        ),
        '/config' => array(
            'controller' => 'Test',
            'action' => 'config'
        ),
    ),
    
    'view' => array(
        'render' => 'twig',
        
        'layout' => 'layout',
        '404' => '404',
        'exception' => 'error',
    ),
    
    'session' => array(
        'use_cookies' => true,
        'cookie_names' => array(
            'cookie'
        ),
        'cookie_lifetime' => 315360000
    ),
);