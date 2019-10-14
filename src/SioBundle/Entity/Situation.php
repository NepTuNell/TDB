<?php

namespace SioBundle\Entity;

use SioBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use AdminBundle\Entity\Referentiel;
use SioBundle\Entity\SituationDetails;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as Serializer;

/**
 * Situation
 * 
 * @ORM\Table(name="Situation")
 * @ORM\Entity(repositoryClass="SioBundle\Repository\SituationRepository")
 */
class Situation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     *
     * @var type 
     * 
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255)
     * @Assert\Length(
     *      min=4,
     *      minMessage = "Le libelle est trop court !",
     *      maxMessage = "Le libelle est trop long !"
     * )
     * @Assert\NotBlank(message="Veuillez saisir le libellé de la situation !")
     */
    private $libelle;

    
    /**************************************
     *              RELATION
     *************************************/
    

    /**
     * @var int
     * 
     * @ORM\ManyToMany(targetEntity="AdminBundle\Entity\Referentiel")
     */
    private $referentiels;

    /**
     * @var int
     * @ORM\OneToMany(targetEntity=SituationDetails::class, mappedBy="situation", cascade={"persist", "remove"})
     * 
     */
    private $situationDetails;
    
    /**
     * Undocumented function
     */
    public function __construct()
    {

        $this->referentiels     = new ArrayCollection();
        $this->situationDetails = new ArrayCollection();

    }

    /**
     * 
     */
    public function addReferentiel(Referentiel $referentiel)
    {
        $this->referentiels[] = $referentiel;

        return $this;
    }

    /**
     * 
     */
    public function removeReferentiel(Referentiel $referentiel)
    {
        $this->referentiels->removeElement($referentiel);
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return Situation
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set user
     *
     * @param \SioBundle\Entity\User $user
     *
     * @return User
     */
    public function setUser(\SioBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \SioBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Undocumented function
     *
     * @return ArrayCollection
     */
    public function getReferentiels()
    {
        return $this->referentiels;
    }

    /**
     * Undocumented function
     * 
     * @return ArrayCollection
     */
    public function getSituationDetails()
    {
        return $this->situationDetails;
    }
    
}
