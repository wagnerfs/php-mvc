<?php

namespace Core;

class Select
{
    protected $columns = [];
    protected $table = '';
    protected $join = [];
    protected $where = [];
    protected $order = [];
    protected $group = [];
    protected $limit = [];
    protected $sql = '';
    protected $sqlColumns = '';
    
    public function __construct($table) {
        $this->table = $table;
    }
    
    public function columns(array $columns)
    {
        $this->columns = $columns;
    }
    
    public function join(array $join)
    {
        $this->join = $join;
    }
    
    public function where(array $where)
    {
        $this->where = $where;
    }
    
    public function group(array $group)
    {
        $this->group = $group;
    }
    
    public function order(array $order)
    {
        $this->order = $order;
    }
    
    public function limit(array $limit)
    {
        $this->limit = $limit;
    }
    
    public function getSql()
    {
        if ($this->sql == '')
            $this->makeSql();
        return 'SELECT '.$this->sqlColumns.$this->sql;
    }
    
    public function getCountSql()
    {
        if ($this->sql == '')
            $this->makeSql ();
        return 'SELECT count(*)'.$this->sql;
    }
    
    protected function makeSql()
    {
        $columns_ = '*';
        if (count($this->columns) > 0)
        {
            $columns_ = implode(', ', $this->columns);
        }
        $this->sqlColumns = $columns_;
        
        $join_ = '';
        if (count($this->join) > 0)
        {
            $join_ = ' '.implode(' ', $this->join);
        }
        
        $where_ = '';
        if (count($this->where) > 0)
        {
            $list = Select::adjustParams($this->where);
            if (count($list) > 0)
            {
                $where_ = ' WHERE '.implode(' and ', $list);
            }
        }
        
        $group_ = '';
        if (count($this->group) > 0)
        {
            $group_ = ' GROUP BY '.implode(', ', $this->group);
        }
        
        $order_ = '';
        if (count($this->order) > 0)
        {
            $order_ = ' ORDER BY '.implode(', ', $this->order);
        }
        
        $limit_ = '';
        if (count($this->limit) > 0 && count($this->limit) <= 2)
        {
            $limit_ = ' LIMIT '.implode(', ', $this->limit);
        }
        
        $this->sql = ' FROM '.$this->table.$join_.$where_.$group_.$order_.$limit_;
    }
    
    public static function adjustParams(array $params)
    {
        $list = [];
        foreach ($params as $key => $val)
        {
            if (is_numeric($key))
            {
                $list[] = $val;
            }
            else
            {
                switch (gettype($val))
                {
                    case 'array':
                    {
                        if (gettype($val[0]) == 'string')
                        {
                            $list[] = $key . ' in (\'' . implode('\', \'', $val) . '\'';
                        }
                        else
                        {
                            $list[] = $key . ' in (' . implode(', ', $val);
                        }
                        break;
                    }
                    case 'boolean':
                    {
                        $list[] = $key.' = '.($val ? 'true' : 'false');
                        break;
                    }
                    case 'NULL':
                    {
                        $list[] = $key.' = null';
                        break;
                    }
                    case 'string':
                    {
                        $list[] = $key.' = \''.$val.'\'';
                        break;
                    }
                    default:
                    {
                        $list[] = $key.' = '.($val + 0);
                        break;
                    }
                }
            }
        }
        
        return $list;
    }
}
