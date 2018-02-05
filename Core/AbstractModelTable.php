<?php

namespace Core;

class AbstractModelTable
{
    /**
     *
     * @var string
     */
    protected $tableName = '';
    
    /**
     *
     * @var string
     */
    protected $objectName = '';
    
    /**
     *
     * @var TableManager
     */
    protected $tableManager = null;
    
    /**
     * Retrieves the table name.
     * If $tableName is empty, returns the class name without the 'Table'
     * suffix and lowercased.
     * 
     * @return string
     */
    public function getTableName()
    {
        if ($this->tableName != '')
        {
            return $this->tableName;
        }
        return strtolower(preg_replace('/^(.+)Table$/', '$1', get_class($this)));
    }
    
    /**
     * Retrieves the object name.
     * If $objectName is empty, returns the class name without the 'Table'
     * suffix.
     * 
     * @return string
     */
    public function getObjectName()
    {
        if ($this->objectName != '')
        {
            return $this->objectName;
        }
        return preg_replace('/^(.+)Table$/', '$1', get_class($this));
    }
    
    /**
     * 
     * @return Select
     */
    protected function getSelect()
    {
        $select = new Select($this->getTableName());
        return $select;
    }
    
    /**
     * 
     * @return TableManager
     */
    protected function getTableManager()
    {
        if ($this->tableManager == null)
        {
            $this->tableManager = new TableManager($this);
        }
        return $this->tableManager;
    }
}
