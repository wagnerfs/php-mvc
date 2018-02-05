<?php

namespace Core;

class TableManager
{
    /**
     *
     * @var AbstractModelTable
     */
    protected $parent = null;
    
    public function __construct(AbstractModelTable $parent) {
        $this->parent = $parent;
    }
    
    public function update(array $params, array $where)
    {
        $db = Database::getInstace();
        
        $params_ = '';
        if (count($params) > 0)
        {
            $list = [];
            foreach ($params as $key => $val)
            {
                if (is_numeric($key))
                {
                    $list[] = $val;
                }
                else if ($val === null)
                {
                    $list[] = $key.' = null';
                }
                else
                {
                    $list[] = $key.' = '.(gettype($val) == 'string' ? '\''.$val.'\'' : $val + 0);
                }
            }
            if (count($list) > 0)
            {
                $params_ = implode(', ', $list);
            }
        }
        
        $where_ = '';
        if (count($where) > 0)
        {
            $list = [];
            foreach ($where as $key => $val)
            {
                if (is_numeric($key))
                {
                    $list[] = $val;
                }
                else if (gettype($val) == 'array' && count($val) > 0)
                {
                    $list[] = $key.' in ('.(gettype($val[0]) == 'string' ? '\''.implode('\', \'', $val).'\'' : implode(', ', $val)).')';
                }
                else
                {
                    $list[] = $key.' = '.(gettype($val) == 'string' ? '\''.$val.'\'' : $val + 0);
                }
            }
            if (count($list) > 0)
            {
                $where_ = implode(' and ', $list);
            }
        }
        
        $query = 'UPDATE '.$this->parent->getTableName().' SET '.$params_.' WHERE '.$where_;
        return $db->query($query) != false;
    }
    
    public function delete(array $where)
    {
        $db = Database::getInstace();
        
        $where_ = '';
        if (count($where) > 0)
        {
            $list = [];
            foreach ($where as $key => $val)
            {
                if (is_numeric($key))
                {
                    $list[] = $val;
                }
                else if (gettype($val) == 'array' && count($val) > 0)
                {
                    $list[] = $key.' in ('.(gettype($val[0]) == 'string' ? '\''.implode('\', \'', $val).'\'' : implode(', ', $val)).')';
                }
                else
                {
                    $list[] = $key.' = '.(gettype($val) == 'string' ? '\''.$val.'\'' : $val + 0);
                }
            }
            if (count($list) > 0)
            {
                $where_ = implode(' and ', $list);
            }
        }
        
        $query = 'DELETE FROM '.$this->parent->getTableName().' WHERE '.$where_;
        return $db->query($query) != false;
    }
    
    public function insert(array $params)
    {
        $db = Database::getInstace();
        
        $columns_ = '';
        $values_ = '';
        if (count($params) > 0)
        {
            $columns = [];
            $values = [];
            foreach ($params as $key => $val)
            {
                $columns[] = $key;
                if ($val === null)
                {
                    $values[] = 'null';
                }
                else
                {
                    $values[] = (gettype($val) == 'string' ? '\''.$val.'\'' : $val + 0);
                }
            }
            $columns_ = implode(', ', $columns);
            $values_ = implode(', ', $values);
        }
        
        $query = 'INSERT INTO '.$this->parent->getTableName().' ('.$columns_.') VALUES ('.$values_.')';
        $db->query($query);
        return $db->lastInsertId();
    }
    
    public function selectWith(Select $select)
    {
        $db = Database::getInstace();
        $objectName = $this->parent->getObjectName();
        
        $objects = [];
        $req = $db->query($select->getSql());
        
        return $req->fetchAll(\PDO::FETCH_CLASS, $objectName);
    }
    
    public function countWith(Select $select)
    {
        $db = Database::getInstace();        
        $result = 0;
        $req = $db->query($select->getCountSql());
        foreach ($req->fetchAll() as $row)
        {
            $result = (int)$row[0];
        }
        
        return $result;
    }
    
    /**
     * Creates a Select object for the for this table object.
     * 
     * @return Select Returns NULL if this object has no parent
     */
    public function getSelect()
    {
        $select = null;
        if ($this->parent != null)
        {
            $select = new Select($this->parent->getTableName());
        }
        return $select;
    }
}
