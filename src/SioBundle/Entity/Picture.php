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
     *   maxSize = "25M",
     *   mimeTypes = {
     *       "image/png",
     *       "image/jpeg",
     *       "image/jpg",
     *       "image/gif"
     *   },
     *   mimeTypesMessage = "Format de l'image non supportée, assurez vous d'utiliser ces formats : png, jpg, jpeg, gif.",
     *   maxSizeMessage = "Fichier trop volumineux, l'image ne doit pas excéder 25 mo."
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
     * Get url
     *
     * @return string
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
    
    /**
     * Get tempfile
     *
     * @return string
     */
    public function getTempFile()
    {
        return $this->tempFile;
    }
    
    public function getPicture()
    {
        return $this->picture;
    }
    
   
    /**************************************
     * GESTION DES FICHIERS VIA UPLOAD
     **************************************/
    
    public function setPicture(UploadedFile $picture = null)
    {
         
        $this->picture = $picture; 
        
        // Si jamais il n'y a pas de fichier (champ facultatif), on ne fait rien
        if (null !== $this->url) {
            
            $this->tempFile = $this->url;
            $root = $this->getUploadRootDir().'/'.$this->getId().".".$this->getTempFile();
            
            if(file_exists($root) ) {
                
                unlink($root);

            }
            
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
        
        if(null === $this->picture) {
            
            return;
            
        }
        
        $this->url = $this->picture->guessExtension(); // On sauvegarde le nom de fichier dans notre attribut $url
        $this->alt = $this->picture->getClientOriginalName(); // On crée également le futur attribut alt de notre balise <img>
            
    }
    
    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload () 
    {
 
        $this->picture->move($this->getUploadRootDir(), $this->getId().".".$this->getUrl()); // On déplace le fichier envoyé dans le répertoire de notre choix
        
    }
    
    /**
     * @ORM\PreRemove()
     */
    public function preRemove () 
    {
        
        $this->tempFile = $this->getUploadRootDir()."/".$this->getId().".".$this->getUrl();
        
    }
    
    
    /**
     * @ORM\PostRemove()
     */
    public function remove () 
    {
        
        if (file_exists($this->tempFile)) {

            unlink($this->tempFile);

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
    
    public function getFileName()
    {
        
        return $this->getId().".".$this->getUrl();
        
    }
    
    
}
