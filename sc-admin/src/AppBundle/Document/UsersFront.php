<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// src/AppBundle/Document/UsersFront.php
namespace AppBundle\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document 
*/
class UsersFront {
    /**
     * @MongoDB\Id
     */
    protected $id;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $username;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $email;
    
    /**
     * @MongoDB\Field(type="int")
     */
    protected $enabled;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $password;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $profile_is_default;
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $profile;
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $modules;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $secret_question1;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $secret_question2;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $secret_question3;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $secret_answer1;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $secret_answer2;
    
    /**
     * @MongoDB\Field(type="string")
     */
    protected $secret_answer3;
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $last_login;
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $reset_password;
    
    /**
     * @MongoDB\Field(type="collection")
     */
    protected $unlock_user;
    
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
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Get username
     *
     * @return string $username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
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
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set enabled
     *
     * @param int $enabled
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return int $enabled
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get password
     *
     * @return string $password
     */
    public function getPassword()
    {
        return $this->password;
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
     * Set lastLogin
     *
     * @param collection $lastLogin
     * @return $this
     */
    public function setLastLogin($lastLogin)
    {
        $this->last_login = $lastLogin;
        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return collection $lastLogin
     */
    public function getLastLogin()
    {
        return $this->last_login;
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
     * Set secretQuestion1
     *
     * @param string $secretQuestion1
     * @return $this
     */
    public function setSecretQuestion1($secretQuestion1)
    {
        $this->secret_question1 = $secretQuestion1;
        return $this;
    }

    /**
     * Get secretQuestion1
     *
     * @return string $secretQuestion1
     */
    public function getSecretQuestion1()
    {
        return $this->secret_question1;
    }

    /**
     * Set secretQuestion2
     *
     * @param string $secretQuestion2
     * @return $this
     */
    public function setSecretQuestion2($secretQuestion2)
    {
        $this->secret_question2 = $secretQuestion2;
        return $this;
    }

    /**
     * Get secretQuestion2
     *
     * @return string $secretQuestion2
     */
    public function getSecretQuestion2()
    {
        return $this->secret_question2;
    }

    /**
     * Set secretQuestion3
     *
     * @param string $secretQuestion3
     * @return $this
     */
    public function setSecretQuestion3($secretQuestion3)
    {
        $this->secret_question3 = $secretQuestion3;
        return $this;
    }

    /**
     * Get secretQuestion3
     *
     * @return string $secretQuestion3
     */
    public function getSecretQuestion3()
    {
        return $this->secret_question3;
    }

    /**
     * Set secretAnswer1
     *
     * @param string $secretAnswer1
     * @return $this
     */
    public function setSecretAnswer1($secretAnswer1)
    {
        $this->secret_answer1 = $secretAnswer1;
        return $this;
    }

    /**
     * Get secretAnswer1
     *
     * @return string $secretAnswer1
     */
    public function getSecretAnswer1()
    {
        return $this->secret_answer1;
    }

    /**
     * Set secretAnswer2
     *
     * @param string $secretAnswer2
     * @return $this
     */
    public function setSecretAnswer2($secretAnswer2)
    {
        $this->secret_answer2 = $secretAnswer2;
        return $this;
    }

    /**
     * Get secretAnswer2
     *
     * @return string $secretAnswer2
     */
    public function getSecretAnswer2()
    {
        return $this->secret_answer2;
    }

    /**
     * Set secretAnswer3
     *
     * @param string $secretAnswer3
     * @return $this
     */
    public function setSecretAnswer3($secretAnswer3)
    {
        $this->secret_answer3 = $secretAnswer3;
        return $this;
    }

    /**
     * Get secretAnswer3
     *
     * @return string $secretAnswer3
     */
    public function getSecretAnswer3()
    {
        return $this->secret_answer3;
    }

    /**
     * Set resetPassword
     *
     * @param collection $resetPassword
     * @return $this
     */
    public function setResetPassword($resetPassword)
    {
        $this->reset_password = $resetPassword;
        return $this;
    }

    /**
     * Get resetPassword
     *
     * @return collection $resetPassword
     */
    public function getResetPassword()
    {
        return $this->reset_password;
    }

    /**
     * Set unlockUser
     *
     * @param collection $unlockUser
     * @return $this
     */
    public function setUnlockUser($unlockUser)
    {
        $this->unlock_user = $unlockUser;
        return $this;
    }

    /**
     * Get unlockUser
     *
     * @return collection $unlockUser
     */
    public function getUnlockUser()
    {
        return $this->unlock_user;
    }

    

    /**
     * Set profile
     *
     * @param collection $profile
     * @return $this
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;
        return $this;
    }

    /**
     * Get profile
     *
     * @return collection $profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Set profileIsDefault
     *
     * @param string $profileIsDefault
     * @return $this
     */
    public function setProfileIsDefault($profileIsDefault)
    {
        $this->profile_is_default = $profileIsDefault;
        return $this;
    }

    /**
     * Get profileIsDefault
     *
     * @return string $profileIsDefault
     */
    public function getProfileIsDefault()
    {
        return $this->profile_is_default;
    }
}
