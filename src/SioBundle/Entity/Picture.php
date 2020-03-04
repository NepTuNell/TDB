<?php

namespace SioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * Files
 *
 * @ORM\Table(name="picture")
 * @ORM\Entity(repositoryClass="SioBundle\Repository\PictureRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Picture
{

    /**************************************
     *              ATTRIBUTS
     *************************************/

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
     * @ORM\ManyToOne(targetEntity="SioBundle\Entity\SituationDetails", inversedBy="pictures")
     */
    protected $situationDetails;
    
    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    protected $url;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255, nullable=true)
     */
    protected $alt;
    
    /**
     * 
     * @Assert\File(
     *   maxSize = "2M",
     *   mimeTypes = {
     *       "image/png",
     *       "image/jpeg",
     *       "image/jpg",
     *       "image/gif"
     *   },
     *   mimeTypesMessage = "Format de l'image non supportée, assurez vous d'utiliser ces formats : png, jpg, jpeg, gif.",
     *   maxSizeMessage = "Fichier trop volumineux, l'image ne doit pas excéder 2 mo."
     * )
     *
     * @var type 
     */
    protected $picture;
    
    /**
     * Undocumented variable
     *
     * @var string
     */
    private   $tempFile;
    
    /**************************************
     *              METHODES
     *************************************/

    /**
     * Set referentielDetails
     *
     * @param \SioBundle\Entity\SituationDetails $situationDetails
     *
     * @return Picture
     */
    public function setSituationDetails(\SioBundle\Entity\SituationDetails $situationDetails = null)
    {
        $this->situationDetails = $situationDetails;

        return $this;
    }

    /**
     * Get referentielDetails
     *
     * @return \SioBundle\Entity\SituationDetails
     */
    public function getSituationDetails()
    {
        return $this->situationDetails;
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
     * 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }    
    
    

    /**************************************
     * GESTION DES FICHIERS VIA UPLOAD
     **************************************/
    
    /**
     * Get tempfile
     *
     * @return string
     */
    public function getTempFile()
    {
        return $this->tempFile;
    }

    /**
     * Get Picture
     */
    public function getPicture()
    {
        return $this->picture;
    }
    
    /**
     * Set Picture
     */
    public function setPicture(UploadedFile $picture = null)
    {
         
        $this->picture = $picture; 

        if ( null !== $this->getUrl() ) {

            $this->tempFile = $this->getFileName();
            $this->url = null;
            $this->alt = null;

        }
        
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload () 
    {
        
        if( null === $this->getPicture() ) {
              
            return;
            
        } 
        
        $this->url = $this->picture->guessExtension();  
        $this->alt = strstr($this->picture->getClientOriginalName(), '.', true);  
            
    }
    
    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload () 
    {
        
        if( null !== $this->getTempFile() ) {

            $root = $this->getUploadRootDir().'/'.$this->getTempFile();
                
            if( file_exists($root) ) {
                    
                unlink($root);

            }

        }

        $this->picture->move($this->getUploadRootDir(), $this->getFileName()); 
        
    }

    /**
     * @ORM\PreRemove()
     */
    public function preRemove () 
    {
        
        $this->tempFile = $this->getUploadRootDir()."/".$this->getFileName();
        
    }
    
    /**
     * @ORM\PostRemove()
     */
    public function remove () 
    {
        
        if ( file_exists($this->getTempFile()) ) {

            unlink($this->getTempFile());

        }
        
    }
    
    public function getUploadDir()
    {

      // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)

      return 'uploads/img';

    }

    public function getUploadRootDir()
    {

      // On retourne le chemin relatif vers l'image pour notre code PHP

      return '/home/jimmy/html/TDB/web/'.$this->getUploadDir();

    }
    
    /**
     * Getter du nom complet du fichier uploadé
     */
    public function getFileName()
    {
        
        return $this->getAlt()."_".$this->getId().".".$this->getUrl();
        
    }
    
    
}
