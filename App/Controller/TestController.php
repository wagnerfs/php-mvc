<?php

namespace App\Controller;

use Core\AbstractController;
use Core\View;

use App\Model\Test;
use App\Model\TestTable;

class TestController extends AbstractController
{
    public function homeAction()
    {
        return new View();
    }
    
    public function numberAction()
    {
        $get = $this->getParams()->getQuery();
        
        return new View([ 'number' => $get['number'] ]);
    }
    
    public function testListAction()
    {
        $get = $this->getParams()->getQuery();
        
        $testTable = new TestTable();
        $tests = $testTable->fetchAll();
        
        $query = [ 'tests' => $tests ];
        if (isset($get['error']))
        {
            $query['error'] = $get['error'];
        }
        
        return new View($query);
    }
    
    public function testInsertAction()
    {
        // This is a rediretion type action, no View should be returned.
        
        $query = [];
        if ($this->getParams()->isPost())
        {
            $post = $this->getParams()->getPost();
            $test = new Test();
            $testTable = new TestTable();
            
            $test->name = $post['name'];
            
            if (!$testTable->insert($test))
            {
                $query['error'] = 1;
            }
        }
        else
        {
            $query['error'] = 1;
        }
        $this->redirect('/test/list', $query);
    }
    
    public function testDeleteAction()
    {
        // This is a rediretion type action, no View should be returned.
        
        $get = $this->getParams()->getQuery();
        
        if (isset($get['id']))
        {
            $testTable = new TestTable();
            $testTable->delete($get['id']);
        }
        
        $this->redirect('/test/list');
    }
    
    public function sessionAction()
    {
        $session = $this->getSession();
        
        $session['session'] = rand();
        $session['cookie'] = rand();
        
        $query = [
            'session' => $session['session'],
            'cookie' => $session['cookie']
        ];
        
        // Use unset to remove session or cookie variables
        //unset($session['session']);
        //unset($session['cookie']);
        
        return new View($query);
    }
    
    public function configAction()
    {
        // If no parameter is passed here, $configs will contain all loaded configs.
        $configs = $this->getServiceConfig()->get('example');
        $query = [
            'configs' => json_encode($configs)
        ];
        
        $view = new View();
        $view->setVariables($query);
        $view->setTemplate('example');
        
        return $view;
    }
}

