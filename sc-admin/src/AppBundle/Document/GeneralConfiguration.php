<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// src/AppBundle/Document/Country.php
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document 
*/
class GeneralConfiguration
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $name;
    
    /**
     * @MongoDB\Field(type="boolean")
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
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $languajes;    
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $duration;    
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $categoryexams;    
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $modules;    
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $typelicense;    
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $typemedicalcenter;    
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $sectormedicalcenter;    
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $secret_questions;    
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $methods;    
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $permits;    
    
    function __construct() 
    {
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
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set active
     *
     * @param boolean $active
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
     * @return boolean $active
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

    /**
     * Set languajes
     *
     * @param collection $languajes
     * @return $this
     */
    public function setLanguajes($languajes)
    {
        $this->languajes = $languajes;
        return $this;
    }

    /**
     * Get languajes
     *
     * @return collection $languajes
     */
    public function getLanguajes()
    {
        return $this->languajes;
    }
    
   public  function __toString(){
        return $this->name;
    }

    /**
     * Set duration
     *
     * @param collection $duration
     * @return $this
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * Get duration
     *
     * @return collection $duration
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set categoryexams
     *
     * @param collection $categoryexams
     * @return $this
     */
    public function setCategoryexams($categoryexams)
    {
        $this->categoryexams = $categoryexams;
        return $this;
    }

    /**
     * Get categoryexams
     *
     * @return collection $categoryexams
     */
    public function getCategoryexams()
    {
        return $this->categoryexams;
    }
    
    /**
     * Set modules
     *
     * @param collection $modules
     * @return $this
     */
    public function setModules($modules)
    {
        $this->modules = $modules;
        return $this;
    }

    /**
     * Get modules
     *
     * @return collection $modules
     */
    public function getModules()
    {
        return $this->modules;
    }
    
   

    /**
     * Set typelicense
     *
     * @param collection $typelicense
     * @return $this
     */
    public function setTypelicense($typelicense)
    {
        $this->typelicense = $typelicense;
        return $this;
    }

    /**
     * Get typelicense
     *
     * @return collection $typelicense
     */
    public function getTypelicense()
    {
        return $this->typelicense;
    }

    /**
     * Set typemedicalcenter
     *
     * @param collection $typemedicalcenter
     * @return $this
     */
    public function setTypemedicalcenter($typemedicalcenter)
    {
        $this->typemedicalcenter = $typemedicalcenter;
        return $this;
    }

    /**
     * Get typemedicalcenter
     *
     * @return collection $typemedicalcenter
     */
    public function getTypemedicalcenter()
    {
        return $this->typemedicalcenter;
    }

    /**
     * Set sectormedicalcenter
     *
     * @param collection $sectormedicalcenter
     * @return $this
     */
    public function setSectormedicalcenter($sectormedicalcenter)
    {
        $this->sectormedicalcenter = $sectormedicalcenter;
        return $this;
    }

    /**
     * Get sectormedicalcenter
     *
     * @return collection $sectormedicalcenter
     */
    public function getSectormedicalcenter()
    {
        return $this->sectormedicalcenter;
    }

    /**
     * Set secretQuestions
     *
     * @param collection $secretQuestions
     * @return $this
     */
    public function setSecretQuestions($secretQuestions)
    {
        $this->secret_questions = $secretQuestions;
        return $this;
    }

    /**
     * Get secretQuestions
     *
     * @return collection $secretQuestions
     */
    public function getSecretQuestions()
    {
        return $this->secret_questions;
    }

    /**
     * Set methods
     *
     * @param collection $methods
     * @return $this
     */
    public function setMethods($methods)
    {
        $this->methods = $methods;
        return $this;
    }

    /**
     * Get methods
     *
     * @return collection $methods
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Set permits
     *
     * @param collection $permits
     * @return $this
     */
    public function setPermits($permits)
    {
        $this->permits = $permits;
        return $this;
    }

    /**
     * Get permits
     *
     * @return collection $permits
     */
    public function getPermits()
    {
        return $this->permits;
    }
}
