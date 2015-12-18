<?php

namespace AG\AgenceimmoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Typeimmobilier
 *
 * @ORM\Table(name="type_immobilier")
 * @ORM\Entity(repositoryClass="AG\AgenceimmoBundle\Entity\TypeimmobilierRepository")
 */
class Typeimmobilier
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
	 *@ORM\oneToMany(targetEntity="AG\AgenceimmoBundle\Entity\Immobilier", mappedBy="typeimmobilier")
	 */
	private $immobiliers;

	 /**
     * Add immobilier
     *
     * @param \AG\AgenceimmoBundle\Entity\Immobilier $immobilier
     * @return Typeimmobilier
     */
    public function addImmobilier(\AG\AgenceimmoBundle\Entity\Immobilier $immo)
    {
        $this->immobiliers[] = $immo;
   
		$immo->setTypeimmobilier($this);
	
        return $this;
    }

    /**
     * Remove adverts
     *
     * @param \AG\AgenceimmoBundle\Entity\Immobilier $immobilier
     */
    public function removeImmobilier(\AG\AgenceimmoBundle\Entity\Immobilier $immo)
    {
        $this->immobiliers->removeElement($immo);
    }

    /**
     * Get immobiliers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImmobiliers()
    {
        return $this->immobiliers;
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
     * Set denomination
     *
     * @param string $denomination
     * @return type_immobilier
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
}
