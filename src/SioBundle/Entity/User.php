<?php

namespace SioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User
 *
 * @UniqueEntity( 
 *      fields = {"username"},
 *      errorPath="username",
 *      message="Ce pseudo est déjà utilisé !"
 * )
 * 
 * @UniqueEntity( 
 *      fields = {"email"},
 *      errorPath="email",
 *      message = "Cette adresse email est déjà utilisée !",
 * )
 * 
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="SioBundle\Repository\UserRepository")
 */
class User implements UserInterface
{
    
    /*************************/
    /*       ATTRIBUTS       */
    /*************************/
    
    
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     * 
     * 
     * @Assert\NotBlank (
     * 
     *      message="Le nom doit être renseigné !"
     * 
     * )
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     * 
     * 
     * @Assert\NotBlank (
     * 
     *      message="Le prénom doit être renseigné !"
     * 
     * )
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     * 
     * 
     * @Assert\NotBlank (
     * 
     *      message="Veuillez choisir un pseudonyme !"
     * 
     * )
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     * 
     * 
     * @Assert\NotBlank (
     * 
     *      message="Veuillez renseigner une adresse email !"
     * 
     * )
     */
    private $email;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="password", type="string", length=255, unique=false)
     * 
     * 
     * @Assert\NotBlank (
     * 
     *      message="Saisissez votre mot de passe !"
     * 
     * )
     */
    private $password;
    
    /**
     * 
     * @var string
     */
    private $confirm_password;

    /**
     * @var bool
     *
     * @ORM\Column(name="isActive", type="boolean")
     */
    private $isActive;
    
    /**
     *
     *  
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
    
    /**
     *
     *  
     * @ORM\Column(name="lastConnexion", type="date", nullable=true)
     */
    private $lastConnexion;
    
    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    private $roles = [];
    
    /**
     * @ORM\Column(name="registerKey", type="text", nullable=true)
     */
    private $registerKey;
    
    
    public function __construct()
    {
        $this->isActive = true;
    }
    
    /*********************************/
    /*       METHODES AJOUTEES       */
    /*********************************/
    
    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setconfirmPassword($confirmPassword)
    {
        $this->confirm_password = $confirmPassword;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getconfirmPassword()
    {
        return $this->confirm_password;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * Set description
     *
     * @param string $description
     *
     * @return User
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Set lastConnexion
     *
     * @param \DateTime $lastConnexion
     *
     * @return User
     */
    public function setLastConnexion($lastConnexion)
    {
        $this->lastConnexion = $lastConnexion;

        return $this;
    }

    /**
     * Get lastConnexion
     *
     * @return \DateTime
     */
    public function getLastConnexion()
    {
        return $this->lastConnexion;
    }
    
    /**
     * Undocumented function
     *
     * @return string
     */
    public function getRegisterKey()
    {
        return $this->registerKey;
    }

    /**
     * Undocumented function
     *
     * @param [type] $registerKey
     * @return void
     */
    public function setRegisterKey($registerKey)
    {
        $this->registerKey = $registerKey;

        return $this;
    }
    
    /****************************************************/
    /*      METHODES DE L'INTERFACE USERINTERFACE       */
    /****************************************************/
    
    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
    
    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
    
    /**
     * Undocumented function
     *
     * @return void
     */
    public function getRoles()
    {
        $roles = $this->roles;
        
        return array_unique($roles);
    }
    
    /**
     * Undocumented function
     *
     * @param array $roles
     * @return void
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
        
        return $this;
        
    }
    
    /**
     * Undocumented function
     *
     * @return void
     */
    public function eraseCredentials()
    {
    }
    
    /**
     * Undocumented function
     *
     * @return void
     */
    public function getSalt()
    {
    }

}
