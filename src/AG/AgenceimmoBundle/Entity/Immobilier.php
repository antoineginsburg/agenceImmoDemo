<?php

namespace AG\AgenceimmoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use AG\AgenceimmoBundle\Validator\Decimal;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Immobilier
 *
 * @ORM\Table(name="immobilier")
 * @ORM\Entity(repositoryClass="AG\AgenceimmoBundle\Entity\ImmobilierRepository")
 */
class Immobilier
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="denomination", type="string", length=255)
     */
    private $denomination;

    /**
     * @var integer
     *
     * @ORM\Column(name="surface", type="integer")
     */
    private $surface;

    /**
     * @var boolean
     *
     * @ORM\Column(name="location", type="boolean")
     */
    private $location;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_pieces", type="integer")
     */
    private $nbPieces;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_mel", type="datetime")
     */
    private $dateMel;

    /**
     * @var integer
     *
     * @ORM\Column(name="annee", type="integer")
     */
    private $annee;

    /**
     * @var integer
     *
     * @ORM\Column(name="prix", type="integer")
	 * 
     */
    private $prix;


	/**
	* @ORM\ManyToOne(targetEntity="AG\AgenceimmoBundle\Entity\Typeimmobilier", inversedBy="immobiliers")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $typeimmobilier;

	
	/**
	* @ORM\OneToMany(targetEntity="AG\AgenceimmoBundle\Entity\Image", mappedBy="immobilier", cascade={"persist", "remove"})
	*/
	private $images;
	
	
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
     * Set denomination
     *
     * @param string $denomination
     * @return immobilier
     */
    public function setDenomination($denomination)
    {
        $this->denomination = $denomination;

        return $this;
    }

    /**
     * Get denomination
     *
     * @return string 
     */
    public function getDenomination()
    {
        return $this->denomination;
    }

    /**
     * Set surface
     *
     * @param integer $surface
     * @return immobilier
     */
    public function setSurface($surface)
    {
        $this->surface = $surface;

        return $this;
    }

    /**
     * Get surface
     *
     * @return intger 
     */
    public function getSurface()
    {
        return $this->surface;
    }

    /**
     * Set location
     *
     * @param boolean $location
     * @return immobilier
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return boolean 
     */
    public function getLocation()
    {
        return $this->location;
    }

	
    /**
     * Set nbPieces
     *
     * @param integer $nbPieces
     * @return immobilier
     */
    public function setNbPieces($nbPieces)
    {
        $this->nbPieces = $nbPieces;

        return $this;
    }

    /**
     * Get nbPieces
     *
     * @return integer 
     */
    public function getNbPieces()
    {
        return $this->nbPieces;
    }

    /**
     * Set dateMel
     *
     * @param \DateTime $dateMel
     * @return immobilier
     */
    public function setDateMel($dateMel)
    {
        $this->dateMel = $dateMel;

        return $this;
    }

    /**
     * Get dateMel
     *
     * @return \DateTime 
     */
    public function getDateMel()
    {
        return $this->dateMel;
    }

    /**
     * Set annee
     *
     * @param integer $annee
     * @return immobilier
     */
    public function setAnnee($annee)
    {
        $this->annee = $annee;

        return $this;
    }

    /**
     * Get annee
     *
     * @return integer 
     */
    public function getAnnee()
    {
        return $this->annee;
    }

    /**
     * Set prix
     *
     * @param integer $prix
     * @return immobilier
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return integer 
     */
    public function getPrix()
    {
        return $this->prix;
    }
	
	 /**
     * Set typeimmobilier
     *
     * @param \AG\AgenceimmoBundle\Entity\Typeimmobilier $typeimmobilier
     * @return Immobilier
     */
    public function setTypeimmobilier(\AG\AgenceimmoBundle\Entity\Typeimmobilier $typeimmobilier)
    {
        $this->typeimmobilier = $typeimmobilier;
    
        return $this;
    }

    /**
     * Get typeimmobilier
     *
     * @return \AG\AgenceimmoBundle\Entity\Typeimmobilier 
     */
    public function getTypeimmobilier()
    {
        return $this->typeimmobilier;
    }
		
	
	/**
     * Add image
     *
     * @param \AG\AgenceimmoBundle\Entity\Image $image
     * @return Immobilier
     */
    public function addImage(\AG\AgenceimmoBundle\Entity\Image $image)
    {
        $this->images[] = $image;
   
		$image->setImmobilier($this);
	
        return $this;
    }

    /**
     * Remove image
     *
     * @param \AG\AgenceimmoBundle\Entity\Image $image
     */
    public function removeImage(\AG\AgenceimmoBundle\Entity\Image $image)
    {
        $this->images->removeElement($image);
    }
	
	 /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImages()
    {
        return $this->images;
    }
	
	public function getDenomination20FirstCaract(){
	
		if(strlen($this->denomination)>20){
			return substr($this->denomination,0,19)." ...";
		}
		return $this->denomination;		
	}
	
	public function hasImage(){
		
		return !(count($this->images)===0);
	
	}
	
	public function removeDir($id){
		$dir=__DIR__.'/../../../../web/bundles/agagenceimmo/images/'.$id.'/';
		//recupere tous les fichiers images contenues dans le dossier
		$files = glob($dir . '*', GLOB_MARK);
		//boucle sur la liste des fichiers images
		foreach ($files as $file) 
		{
			//suppression image
			unlink($file);
		}
 		rmdir ($dir);
	}
		
}
