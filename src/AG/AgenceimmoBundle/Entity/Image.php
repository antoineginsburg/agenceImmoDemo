<?php

namespace AG\AgenceimmoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * Image
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AG\AgenceimmoBundle\Entity\ImageRepository")
 */
class Image
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
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255)
     */
    private $alt;

	/**
	* @ORM\ManyToOne(targetEntity="AG\AgenceimmoBundle\Entity\Immobilier", inversedBy="images")
	* @ORM\JoinColumn(nullable=false)
	*/
	private $immobilier;	

	
	private $file;
	
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
     * Set url
     *
     * @param string $url
     * @return Image
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
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
     * Set alt
     *
     * @param string $alt
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
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
     * Set immobilier
     *
     * @param string $immobilier
     * @return Image
     */
    public function setImmobilier($immobilier)
    {
        $this->immobilier = $immobilier;

        return $this;
    }
	
	public function getFile(){
		return $this->file;
	}
	
	public function setFile(UploadedFile $file=null){
		
		$this->file=$file;
		
	}
	
	public function delete($url){
		unlink ($this->getUploadRootDir()."/".$url);
	}
	public function upload($id)
	{
		// Si jamais il n'y a pas de fichier (champ facultatif), on ne fait rien
		if (null === $this->file) {
			return "c null";
		}

		// On récupère le nom original du fichier de l'internaute
		$name = "".$this->file->getClientOriginalName();
		
		error_log("lalal");

		//on recupere le nom du dossier a creer
		$dirName = $this->getUploadRootDir()."/".$id;
		
		//si le dossier n'existe pas encore on le creer
		if(!is_dir($dirName)){
			error_log("le dossier ".$dirName." n existe pas on le cree");
			mkdir($dirName);
		}else{
			error_log("le dossier ".$dirName." existe deja on ne le cree pas");
		}
		
		// On déplace le fichier envoyé dans le répertoire de notre choix
		$this->file->move($dirName, $name);

		// On sauvegarde le nom de fichier dans notre attribut $url
		$this->url = $id."/".$name;

		// On crée également le futur attribut alt de notre balise <img>
		$this->alt = $name;
		
		return "c pas null";
	}

	public function getUploadDir()
	{
		// On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
		return 'bundles/agagenceimmo/images';
	}

	protected function getUploadRootDir()
	{
		// On retourne le chemin relatif vers l'image pour notre code PHP
		return __DIR__.'/../../../../web/'.$this->getUploadDir();
	}

}
