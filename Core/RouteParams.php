<?php

namespace Core;

class RouteParams
{
    protected $query = [];
    protected $post = [];
    
    public function __construct(array $query, array $post)
    {
        $this->query = $query;
        $this->post = $post;
    }
    
    /**
     * Checks if the data set method is POST
     * 
     * @return boolean
     */
    public function isPost()
    {
        return count($this->post) > 0;
    }
    
    /**
     * Retrieves GET data by name
     * 
     * @param string $name
     * @return mixed
     */
    public function fromQuery($name)
    {
        if (key_exists($name, $this->query))
        {
            return $this->query[$name];
        }
        return null;
    }
    
    /**
     * Retrieves POST data by name
     * 
     * @param string $name
     * @return mixed
     */
    public function fromPost($name)
    {
        if (key_exists($name, $this->query))
        {
            return $this->post[$name];
        }
        return null;
    }
    
    /**
     * Retrieves all data set as POST
     * 
     * @return array
     */
    public function getPost()
    {
        return $this->post;
    }
    
    /**
     * Retrieves all data set as GET
     * 
     * @return array
     */
    public function getQuery()
    {
        return $this->query;
    }
}
