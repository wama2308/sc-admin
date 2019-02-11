<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// src/AppBundle/Document/ExternalStaff.php
namespace AppBundle\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document 
*/
class ExternalStaff {
    /**
     * @MongoDB\Id
     */
    protected $id;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $type_identity;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $dni;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $names;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $surnames;
    
    /**
     * @MongoDB\Field(type="int")
     */
    protected $active;
    
    /**
     * @MongoDB\Field(type="date")
     */
    protected $created_at;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $created_by;

    /**
     * @MongoDB\Field(type="date")
     */
    protected $updated_at;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $updated_by;

    function __construct() {
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }      
    

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set typeIdentity
     *
     * @param string $typeIdentity
     * @return $this
     */
    public function setTypeIdentity($typeIdentity)
    {
        $this->type_identity = $typeIdentity;
        return $this;
    }

    /**
     * Get typeIdentity
     *
     * @return string $typeIdentity
     */
    public function getTypeIdentity()
    {
        return $this->type_identity;
    }

    /**
     * Set dni
     *
     * @param string $dni
     * @return $this
     */
    public function setDni($dni)
    {
        $this->dni = $dni;
        return $this;
    }

    /**
     * Get dni
     *
     * @return string $dni
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * Set names
     *
     * @param string $names
     * @return $this
     */
    public function setNames($names)
    {
        $this->names = $names;
        return $this;
    }

    /**
     * Get names
     *
     * @return string $names
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * Set surnames
     *
     * @param string $surnames
     * @return $this
     */
    public function setSurnames($surnames)
    {
        $this->surnames = $surnames;
        return $this;
    }

    /**
     * Get surnames
     *
     * @return string $surnames
     */
    public function getSurnames()
    {
        return $this->surnames;
    }

    /**
     * Set active
     *
     * @param int $active
     * @return $this
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Get active
     *
     * @return int $active
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set createdAt
     *
     * @param date $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return date $createdAt
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set createdBy
     *
     * @param string $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy)
    {
        $this->created_by = $createdBy;
        return $this;
    }

    /**
     * Get createdBy
     *
     * @return string $createdBy
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * Set updatedAt
     *
     * @param date $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return date $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set updatedBy
     *
     * @param string $updatedBy
     * @return $this
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updated_by = $updatedBy;
        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return string $updatedBy
     */
    public function getUpdatedBy()
    {
        return $this->updated_by;
    }
}
