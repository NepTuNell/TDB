<?php

namespace SioBundle\Entity;

use SioBundle\Entity\Picture;
use Doctrine\ORM\Mapping as ORM;
use AdminBundle\Entity\Competences;
use JMS\Serializer\Annotation\MaxDepth;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * SituationDetails
 *
 * @ORM\Table(name="situation_details")
 * @ORM\Entity(repositoryClass="SioBundle\Repository\SituationDetailsRepository")
 */
class SituationDetails
{
    
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Groups({"details"})
     */
    private $id;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity=Situation::class, inversedBy="situationDetails")
     */
    private $situation;
   
    /**
     * @var string
     * @Assert\NotBlank(
     *      message="Veuillez renseigner votre situation !"
     * )
     * @ORM\Column(name="description", type="text")
     * @Serializer\Groups({"details"})
     */
    private $description;
    
    /**
     * 
     * @ORM\OneToMany(targetEntity="SioBundle\Entity\Picture", mappedBy="situationDetails", cascade={"persist", "remove"})
     * @Serializer\Groups({"details"})
     */ 
    protected $pictures;

    /**
     * 
     * @ORM\ManyToMany(targetEntity="AdminBundle\Entity\Competences")
     * @Serializer\Groups({"details"})
     */ 
    private $competences;
    
    /**
     * @var boolean
     * @ORM\Column(name="validated", type="boolean", options={"default" : 0})
     * @Serializer\Groups({"details"})
     */
    private $validated;
    
    /**
     * Undocumented function
     */
    public function __construct()
    {
        $this->pictures     = new ArrayCollection();
        $this->competences  = new ArrayCollection();
    }
     
    /**************************************
     *      METHODS AUTOGENERATED
     **************************************/
    
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
     * Set description
     *
     * @param string $description
     *
     * @return SituationDetails
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
     * Set referentiel
     *
     *
     * @return SituationDetails
     */
    public function setSituation(\SioBundle\Entity\Situation $situation = null)
    {
        $this->situation = $situation;

        return $this;
    }

    /**
     * Get referentiel
     *
     *
     */
    public function getSituation()
    {
        return $this->situation;
    }
      
    /**
     * Set validated 
     * 
     * @return boolean
     */
    public function setValidated($validated)
    {
        $this->validated = $validated;
        
        return $this;
    }

    /**
     * Get validated
     * 
     * @return boolean
     */
    public function getValidated()
    {
        return $this->validated;
    }
    
    /******************************************************
     *      TRAITEMENT DE LA COLLECTION D'IMAGES
     *****************************************************/
    
    
    /**
     * Add picture
     *
     * @param \SioBundle\Entity\Picture $picture
     *
     * @return SituationDetails
     */
    public function addPicture(\SioBundle\Entity\Picture $picture)
    {
        $this->pictures->add($picture);

        return $this;
    }

    /**
     * Remove picture
     *
     * @param \SioBundle\Entity\Picture $picture
     */
    public function removePicture(\SioBundle\Entity\Picture $picture)
    {
        $this->pictures->removeElement($picture);
    }

    /**
     * Get pictures
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPictures()
    {
        return $this->pictures;
    }
    
    /******************************************************
     *      TRAITEMENT DE LA COLLECTION DE COMPETENCES
     *****************************************************/

    /**
     * Undocumented function
     *
     * @param [type] $competence
     * @return void
     */
    public function addCompetence(Competences $competence)
    {
        $this->competences[] = $competence;
    }
    
    /**
     * Undocumented function
     *
     * @param [type] $competence
     * @return void
     */
    public function removeCompetence(Competences $competence)
    {
        $this->competences->removeElement($competence);
    }

     /**
     * Undocumented function
     *
     * @return ArrayCollection
     */
    public function getCompetences()
    {
        return $this->competences;
    }
    

}
