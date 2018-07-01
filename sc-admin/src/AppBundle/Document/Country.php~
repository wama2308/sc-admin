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
class Country {
    /**
     * @MongoDB\Id
     */
    protected $id;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $name;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $timezone;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $acronym;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $coin;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $currencySymbol;
    
    /**
     * @MongoDB\Field(type="float")
     */
    protected $taxRate;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $telephonePrefix;

    /**
     * @MongoDB\Field(type="boolean")
     */
    protected $active;
    
    /**
     * @MongoDB\Field(type="int")
     */
    protected $languaje;

    /**
     * @MongoDB\Field(type="collection")
     */
    protected $provinces;
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $issuingbank;
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $receivingbank;
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $waytopay;

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
     * Set timezone
     *
     * @param string $timezone
     * @return $this
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
        return $this;
    }

    /**
     * Get timezone
     *
     * @return string $timezone
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Set acronym
     *
     * @param string $acronym
     * @return $this
     */
    public function setAcronym($acronym)
    {
        $this->acronym = $acronym;
        return $this;
    }

    /**
     * Get acronym
     *
     * @return string $acronym
     */
    public function getAcronym()
    {
        return $this->acronym;
    }

    /**
     * Set coin
     *
     * @param string $coin
     * @return $this
     */
    public function setCoin($coin)
    {
        $this->coin = $coin;
        return $this;
    }

    /**
     * Get coin
     *
     * @return string $coin
     */
    public function getCoin()
    {
        return $this->coin;
    }

    /**
     * Set currencySymbol
     *
     * @param string $currencySymbol
     * @return $this
     */
    public function setCurrencySymbol($currencySymbol)
    {
        $this->currencySymbol = $currencySymbol;
        return $this;
    }

    /**
     * Get currencySymbol
     *
     * @return string $currencySymbol
     */
    public function getCurrencySymbol()
    {
        return $this->currencySymbol;
    }

    /**
     * Set taxRate
     *
     * @param float $taxRate
     * @return $this
     */
    public function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;
        return $this;
    }

    /**
     * Get taxRate
     *
     * @return float $taxRate
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     * Set telephonePrefix
     *
     * @param string $telephonePrefix
     * @return $this
     */
    public function setTelephonePrefix($telephonePrefix)
    {
        $this->telephonePrefix = $telephonePrefix;
        return $this;
    }

    /**
     * Get telephonePrefix
     *
     * @return string $telephonePrefix
     */
    public function getTelephonePrefix()
    {
        return $this->telephonePrefix;
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
     * Set languaje
     *
     * @param int $languaje
     * @return $this
     */
    public function setLanguaje($languaje)
    {
        $this->languaje = $languaje;
        return $this;
    }

    /**
     * Get languaje
     *
     * @return int $languaje
     */
    public function getLanguaje()
    {
        return $this->languaje;
    }

    /**
     * Set provinces
     *
     * @param collection $provinces
     * @return $this
     */
    public function setProvinces($provinces)
    {
        $this->provinces = $provinces;
        return $this;
    }

    /**
     * Get provinces
     *
     * @return collection $provinces
     */
    public function getProvinces()
    {
        return $this->provinces;
    }
    
    /**
     * Set issuingbank
     *
     * @param collection $issuingbank
     * @return $this
     */
    public function setIssuingbank($issuingbank)
    {
        $this->issuingbank = $issuingbank;
        return $this;
    }

    /**
     * Get issuingbank
     *
     * @return collection $issuingbank
     */
    public function getIssuingbank()
    {
        return $this->issuingbank;
    }

    /**
     * Set receivingbank
     *
     * @param collection $receivingbank
     * @return $this
     */
    public function setReceivingbank($receivingbank)
    {
        $this->receivingbank = $receivingbank;
        return $this;
    }

    /**
     * Get receivingbank
     *
     * @return collection $receivingbank
     */
    public function getReceivingbank()
    {
        return $this->receivingbank;
    }

    /**
     * Set waytopay
     *
     * @param collection $waytopay
     * @return $this
     */
    public function setWaytopay($waytopay)
    {
        $this->waytopay = $waytopay;
        return $this;
    }

    /**
     * Get waytopay
     *
     * @return collection $waytopay
     */
    public function getWaytopay()
    {
        return $this->waytopay;
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
    
    public  function __toString(){
        return $this->name;
    }

}

/**
 * @MongoDB\Document 
*/
class provinces{
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

    function __construct() {
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
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
}
