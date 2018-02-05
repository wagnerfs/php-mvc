<?php

namespace App\Model;

use Core\AbstractModelTable;
use Exception;

class TestTable extends AbstractModelTable
{
    protected $tableName = 'test';
    
    public function fetchAll()
    {
        return $this->getTableManager()->selectWith($this->getSelect());
    }
    
    /**
     * 
     * @param int $id
     * @return \App\Model\Test
     * @throws Exception
     */
    public function get($id)
    {
        $id = (int)$id;
        if ($id > 0)
        {
            $select = $this->getSelect();
            $select->where([ 'id' => $id ]);
            
            $result = $this->getTableManager()->selectWith($select);
            if (count($result) == 1)
            {
                return $result[0];
            }
            
            throw new Exception('Id '.$id.' not found: '.get_class($this).'::get');
        }
        throw new Exception('Id must be valid: '.get_class($this).'::get');
    }
    
    /**
     * 
     * @param \App\Model\Test $test
     * @return int Returns the last inserted ID or 0 if nothing was inserted
     */
    public function insert(Test $test)
    {
        $params = [
            'id' => $test->id,
            'name' => $test->name
        ];
        
        return $this->getTableManager()->insert($params);
    }
    
    /**
     * 
     * @param \App\Model\Test $test
     * @return boolean
     * @throws Exception
     */
    public function update(Test $test)
    {
        if (isset($test) && (int)$test->id > 0)
        {
            $params = [
                'name' => $test->name
            ];
            
            return $this->getTableManager()->update($params, [ 'id' => $test->id ]);
        }
        throw new Exception('Class must contain a valid id: '.get_class($this).'::update');
    }
    
    /**
     * 
     * @param int $id
     * @return boolean
     * @throws Exception
     */
    public function delete($id)
    {
        $id = (int)$id;
        if ($id > 0)
        {
            return $this->getTableManager()->delete([ 'id' => $id ]);
        }
        throw new Exception('Id must be valid: '.get_class($this).'::delete');
    }
}