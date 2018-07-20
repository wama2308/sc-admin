<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// src/AppBundle/Document/License.php
namespace AppBundle\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document 
*/
class License {
    /**
     * @MongoDB\Id
     */
    protected $id;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $license;
    
    /**
     * @MongoDB\Field(type="int")
     */
    protected $typelicense;  
    
    /**
     * @MongoDB\Field(type="int")
     */
    protected $usersquantity;
    
    /**
     * @MongoDB\Field(type="int")
     */
    protected $numberclients;
    
    /**
     * @MongoDB\Field(type="int")
     */
    protected $numberexams;
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $exams;
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $modules;
    
    /**
     * @MongoDB\Field(type="collection")
     */    
    protected $countries;
    
    /**
     * @MongoDB\Field(type="int")
     */    
    protected $durationtime;
    
    /**
     * @MongoDB\Field(type="int")
     */    
    protected $noticepayment;
    
    /**
     * @MongoDB\Field(type="string")
     */    
    protected $description;
    
    /**
     * @MongoDB\Field(type="float")
     */
    protected $amount;
    
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
     * Set license
     *
     * @param string $license
     * @return $this
     */
    public function setLicense($license)
    {
        $this->license = $license;
        return $this;
    }

    /**
     * Get license
     *
     * @return string $license
     */
    public function getLicense()
    {
        return $this->license;
    }

    /**
     * Set usersquantity
     *
     * @param int $usersquantity
     * @return $this
     */
    public function setUsersquantity($usersquantity)
    {
        $this->usersquantity = $usersquantity;
        return $this;
    }

    /**
     * Get usersquantity
     *
     * @return int $usersquantity
     */
    public function getUsersquantity()
    {
        return $this->usersquantity;
    }

    /**
     * Set numberclients
     *
     * @param int $numberclients
     * @return $this
     */
    public function setNumberclients($numberclients)
    {
        $this->numberclients = $numberclients;
        return $this;
    }

    /**
     * Get numberclients
     *
     * @return int $numberclients
     */
    public function getNumberclients()
    {
        return $this->numberclients;
    }

    /**
     * Set numberexams
     *
     * @param int $numberexams
     * @return $this
     */
    public function setNumberexams($numberexams)
    {
        $this->numberexams = $numberexams;
        return $this;
    }

    /**
     * Get numberexams
     *
     * @return int $numberexams
     */
    public function getNumberexams()
    {
        return $this->numberexams;
    }

    /**
     * Set exams
     *
     * @param collection $exams
     * @return $this
     */
    public function setExams($exams)
    {
        $this->exams = $exams;
        return $this;
    }

    /**
     * Get exams
     *
     * @return collection $exams
     */
    public function getExams()
    {
        return $this->exams;
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
     * Set countries
     *
     * @param collection $countries
     * @return $this
     */
    public function setCountries($countries)
    {
        $this->countries = $countries;
        return $this;
    }

    /**
     * Get countries
     *
     * @return collection $countries
     */
    public function getCountries()
    {
        return $this->countries;
    }

    /**
     * Set durationtime
     *
     * @param int $durationtime
     * @return $this
     */
    public function setDurationtime($durationtime)
    {
        $this->durationtime = $durationtime;
        return $this;
    }

    /**
     * Get durationtime
     *
     * @return int $durationtime
     */
    public function getDurationtime()
    {
        return $this->durationtime;
    }

    /**
     * Set noticepayment
     *
     * @param int $noticepayment
     * @return $this
     */
    public function setNoticepayment($noticepayment)
    {
        $this->noticepayment = $noticepayment;
        return $this;
    }

    /**
     * Get noticepayment
     *
     * @return int $noticepayment
     */
    public function getNoticepayment()
    {
        return $this->noticepayment;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Get amount
     *
     * @return float $amount
     */
    public function getAmount()
    {
        return $this->amount;
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
     * Set typelicense
     *
     * @param int $typelicense
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
     * @return int $typelicense
     */
    public function getTypelicense()
    {
        return $this->typelicense;
    }
}
