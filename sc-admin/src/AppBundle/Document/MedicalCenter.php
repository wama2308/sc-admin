<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// src/AppBundle/Document/MedicalCenter.php
namespace AppBundle\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document 
*/
class MedicalCenter {
    /**
     * @MongoDB\Id
     */
    protected $id;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $countryid;
    
    /**
     * @MongoDB\Field(type="int")
     */
    protected $provinceid;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $coordinates;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $coordinates1;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $name;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $code;
    
    /**
     * @MongoDB\Field(type="int")
     */
    protected $type;
    
    /**
     * @MongoDB\Field(type="int")
     */
    protected $sector;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $address;

    /**
     * @MongoDB\Field(type="collection")
     */
    protected $phone;
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $master;
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $social_networks;
    
    /**
     * @MongoDB\Field(type="string")
     */    
    protected $contac1;
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $contac1phone;
    
    /**
     * @MongoDB\Field(type="string")
     */    
    protected $contac2;
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $contac2phone;
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $branchoffices;
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $licenses;
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $roles;
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $payments;
    
    /**
     * @MongoDB\Field(type="string")
     */    
    protected $paymentstatus;   
    
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
     * Set countryid
     *
     * @param string $countryid
     * @return $this
     */
    public function setCountryid($countryid)
    {
        $this->countryid = $countryid;
        return $this;
    }

    /**
     * Get countryid
     *
     * @return string $countryid
     */
    public function getCountryid()
    {
        return $this->countryid;
    }

    /**
     * Set provinceid
     *
     * @param int $provinceid
     * @return $this
     */
    public function setProvinceid($provinceid)
    {
        $this->provinceid = $provinceid;
        return $this;
    }

    /**
     * Get provinceid
     *
     * @return int $provinceid
     */
    public function getProvinceid()
    {
        return $this->provinceid;
    }

    /**
     * Set coordinates
     *
     * @param string $coordinates
     * @return $this
     */
    public function setCoordinates($coordinates)
    {
        $this->coordinates = $coordinates;
        return $this;
    }

    /**
     * Get coordinates
     *
     * @return string $coordinates
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * Set coordinates1
     *
     * @param string $coordinates1
     * @return $this
     */
    public function setCoordinates1($coordinates1)
    {
        $this->coordinates1 = $coordinates1;
        return $this;
    }

    /**
     * Get coordinates1
     *
     * @return string $coordinates1
     */
    public function getCoordinates1()
    {
        return $this->coordinates1;
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
     * Set code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string $code
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set type
     *
     * @param int $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return int $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set sector
     *
     * @param int $sector
     * @return $this
     */
    public function setSector($sector)
    {
        $this->sector = $sector;
        return $this;
    }

    /**
     * Get sector
     *
     * @return int $sector
     */
    public function getSector()
    {
        return $this->sector;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return $this
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Get address
     *
     * @return string $address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set phone
     *
     * @param collection $phone
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Get phone
     *
     * @return collection $phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set master
     *
     * @param collection $master
     * @return $this
     */
    public function setMaster($master)
    {
        $this->master = $master;
        return $this;
    }

    /**
     * Get master
     *
     * @return collection $master
     */
    public function getMaster()
    {
        return $this->master;
    }

    /**
     * Set socialNetworks
     *
     * @param collection $socialNetworks
     * @return $this
     */
    public function setSocialNetworks($socialNetworks)
    {
        $this->social_networks = $socialNetworks;
        return $this;
    }

    /**
     * Get socialNetworks
     *
     * @return collection $socialNetworks
     */
    public function getSocialNetworks()
    {
        return $this->social_networks;
    }

    /**
     * Set contac1
     *
     * @param string $contac1
     * @return $this
     */
    public function setContac1($contac1)
    {
        $this->contac1 = $contac1;
        return $this;
    }

    /**
     * Get contac1
     *
     * @return string $contac1
     */
    public function getContac1()
    {
        return $this->contac1;
    }

    /**
     * Set contac1phone
     *
     * @param collection $contac1phone
     * @return $this
     */
    public function setContac1phone($contac1phone)
    {
        $this->contac1phone = $contac1phone;
        return $this;
    }

    /**
     * Get contac1phone
     *
     * @return collection $contac1phone
     */
    public function getContac1phone()
    {
        return $this->contac1phone;
    }

    /**
     * Set contac2
     *
     * @param string $contac2
     * @return $this
     */
    public function setContac2($contac2)
    {
        $this->contac2 = $contac2;
        return $this;
    }

    /**
     * Get contac2
     *
     * @return string $contac2
     */
    public function getContac2()
    {
        return $this->contac2;
    }

    /**
     * Set contac2phone
     *
     * @param collection $contac2phone
     * @return $this
     */
    public function setContac2phone($contac2phone)
    {
        $this->contac2phone = $contac2phone;
        return $this;
    }

    /**
     * Get contac2phone
     *
     * @return collection $contac2phone
     */
    public function getContac2phone()
    {
        return $this->contac2phone;
    }

    /**
     * Set branchoffices
     *
     * @param collection $branchoffices
     * @return $this
     */
    public function setBranchoffices($branchoffices)
    {
        $this->branchoffices = $branchoffices;
        return $this;
    }

    /**
     * Get branchoffices
     *
     * @return collection $branchoffices
     */
    public function getBranchoffices()
    {
        return $this->branchoffices;
    }

    /**
     * Set licenses
     *
     * @param collection $licenses
     * @return $this
     */
    public function setLicenses($licenses)
    {
        $this->licenses = $licenses;
        return $this;
    }

    /**
     * Get licenses
     *
     * @return collection $licenses
     */
    public function getLicenses()
    {
        return $this->licenses;
    }

    /**
     * Set payments
     *
     * @param collection $payments
     * @return $this
     */
    public function setPayments($payments)
    {
        $this->payments = $payments;
        return $this;
    }

    /**
     * Get payments
     *
     * @return collection $payments
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * Set paymentstatus
     *
     * @param string $paymentstatus
     * @return $this
     */
    public function setPaymentstatus($paymentstatus)
    {
        $this->paymentstatus = $paymentstatus;
        return $this;
    }

    /**
     * Get paymentstatus
     *
     * @return string $paymentstatus
     */
    public function getPaymentstatus()
    {
        return $this->paymentstatus;
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
     * Set roles
     *
     * @param collection $roles
     * @return $this
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * Get roles
     *
     * @return collection $roles
     */
    public function getRoles()
    {
        return $this->roles;
    }
}
