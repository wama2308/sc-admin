<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// src/AppBundle/Document/Patients.php
namespace AppBundle\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document 
*/
class Patients {
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
     * @MongoDB\Field(type="string")
     */
    protected $civil_state;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $sex;    
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $birth_date;
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $phone;
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $email;
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $province;
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $district;  
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $address;    
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $photo;    
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $branchoffices_register;    
    
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
     * Set civilState
     *
     * @param string $civilState
     * @return $this
     */
    public function setCivilState($civilState)
    {
        $this->civil_state = $civilState;
        return $this;
    }

    /**
     * Get civilState
     *
     * @return string $civilState
     */
    public function getCivilState()
    {
        return $this->civil_state;
    }

    /**
     * Set sex
     *
     * @param string $sex
     * @return $this
     */
    public function setSex($sex)
    {
        $this->sex = $sex;
        return $this;
    }

    /**
     * Get sex
     *
     * @return string $sex
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Set birthDate
     *
     * @param string $birthDate
     * @return $this
     */
    public function setBirthDate($birthDate)
    {
        $this->birth_date = $birthDate;
        return $this;
    }

    /**
     * Get birthDate
     *
     * @return string $birthDate
     */
    public function getBirthDate()
    {
        return $this->birth_date;
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
     * Set email
     *
     * @param collection $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get email
     *
     * @return collection $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set province
     *
     * @param collection $province
     * @return $this
     */
    public function setProvince($province)
    {
        $this->province = $province;
        return $this;
    }

    /**
     * Get province
     *
     * @return collection $province
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * Set district
     *
     * @param collection $district
     * @return $this
     */
    public function setDistrict($district)
    {
        $this->district = $district;
        return $this;
    }

    /**
     * Get district
     *
     * @return collection $district
     */
    public function getDistrict()
    {
        return $this->district;
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
     * Set photo
     *
     * @param string $photo
     * @return $this
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
        return $this;
    }

    /**
     * Get photo
     *
     * @return string $photo
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set branchofficesRegister
     *
     * @param string $branchofficesRegister
     * @return $this
     */
    public function setBranchofficesRegister($branchofficesRegister)
    {
        $this->branchoffices_register = $branchofficesRegister;
        return $this;
    }

    /**
     * Get branchofficesRegister
     *
     * @return string $branchofficesRegister
     */
    public function getBranchofficesRegister()
    {
        return $this->branchoffices_register;
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
