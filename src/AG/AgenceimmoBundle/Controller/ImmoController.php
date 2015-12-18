<?php

namespace AG\AgenceimmoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AG\AgenceimmoBundle\Entity\Immobilier;
use AG\AgenceimmoBundle\Form\ImmobilierType;
use Symfony\Component\HttpFoundation\Request;
use AG\AgenceimmoBundle\Entity\Image;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImmoController extends Controller
{
    public function accueilAction(Request $request)
    {
		$imgs = $this->getDoctrine()->getManager()->getRepository('AGAgenceimmoBundle:Image')->getImgAccueil();
		return $this->render('AGAgenceimmoBundle:Immo:accueil.html.twig',array('imgs' => $imgs));
    }
	
	public function listAction($type)
	{
		// On récupère la liste des biens
		$listImmobiliers = $this->getDoctrine()
		  ->getManager()
		  ->getRepository('AGAgenceimmoBundle:Immobilier')
		  ->getimmobiliers($type)
		;
		return $this->render('AGAgenceimmoBundle:Immo:list.html.twig', array('type' => $type, 'listImmobiliers'=>$listImmobiliers));
	}
	
	public function addAction(Request $request)
	{	
		$immo = new Immobilier;
		$form = $this->get('form.factory')->create(new ImmobilierType, $immo);
		
		if($form->handleRequest($request)->isValid()){
			$date =  new \DateTime();
			$immo->setDateMel($date);
			if($immo->hasImage()){
				//cacul le last id sil existe pour creer le dossier 
				//dans lequel on va stocker les images (voir détail de la fonction Image->upload($id))
				$em = $this->getDoctrine()->getManager();
				$last_immo = $em->createQueryBuilder()
				->select('i')
				->from('AGAgenceimmoBundle:Immobilier', 'i')
				->orderBy('i.id', 'DESC')
				->setMaxResults(1)
				->getQuery()
				->getOneOrNullResult();
				
				$last_id = ($last_immo!=null)?$last_immo->getId():0;
				$last_id++;
				foreach($immo->getImages() as $img){
					$img->upload($last_id);
				}
			}
			$em = $this->getDoctrine()->getManager();
			$em->persist($immo);
			$em->flush();

			$request->getSession()->getFlashBag()->add('notice', 'Bien immo correctement enregistré.');
			
			//Après persistance en base de données on redirige vers la page de visualisation du bien
			return $this->redirect($this->generateUrl('ag_agenceimmo_view', array('id' => $immo->getId())));
			
		}
		
		return $this->render('AGAgenceimmoBundle:Immo:add.html.twig', array('form' => $form->createView(),));
	}
	
	public function connexionAction()
	{
		return $this->render('AGAgenceimmoBundle:Immo:connexion.html.twig');
	}
	
	public function viewAction($id)
	{
		//recuperation de l'entityManager
		$em = $this->getDoctrine()->getManager();
		
		// On récupère l'anonnce correspondant à l'id
		$immobilier = $em->getRepository('AGAgenceimmoBundle:Immobilier')->find($id);
		
		return $this->render('AGAgenceimmoBundle:Immo:view.html.twig', array('immobilier'=>$immobilier));

	}
	
	public function searchAction($search){
		
		$words = explode("+",$search,10);
		// On récupère la liste des biens correspondant à la recherche
		$listImmobiliers = $this->getDoctrine()
		  ->getManager()
		  ->getRepository('AGAgenceimmoBundle:Immobilier')
		  ->searchImmobilier($words)
		;
		return $this->render('AGAgenceimmoBundle:Immo:searchresult.html.twig', array('listImmobiliers'=>$listImmobiliers, 'mots' => $search));
	}
	
	
	public function editAction(Request $request, $id){
		
		$em = $this->getDoctrine()->getManager();
		$immo= $em->getRepository('AGAgenceimmoBundle:Immobilier')->find($id);
		//on supprime les images
		/*$lesImages = $immo->getImages();
		foreach($lesImages as $img){
			$immo->removeImage($img);
		}*/
		$form = $this->get('form.factory')->create(new ImmobilierType, $immo);
		
		if($form->handleRequest($request)->isValid()){
			$em->flush();
			$request->getSession()->getFlashBag()->add('notice', 'Les modifications ont été prises en compte.');
			return $this->redirect($this->generateUrl('ag_agenceimmo_view', array('id' => $immo->getId())));
		}

		return $this->render('AGAgenceimmoBundle:Immo:edit.html.twig', array('form' => $form->createView(),'id'=>$id, 'edit'=>'edit'));
	}
	
	public function deleteAction(Request $request, $id){
		
		$em = $this->getDoctrine()->getManager();

		// On récupère l'annonce $id
		$immo = $em->getRepository('AGAgenceimmoBundle:Immobilier')->find($id);
		
		if (null === $immo) {
			throw new NotFoundHttpException("Le bien d'id ".$id." n'existe pas.");
		}

		// On crée un formulaire vide, qui ne contiendra que le champ CSRF
		// Cela permet de protéger la suppression de bien contre cette faille
		$form = $this->createFormBuilder()->getForm();

		if ($form->handleRequest($request)->isValid()) {
			$type="";
			if($immo->getLocation()){
				$type="location"; 
			}else{
				$type="achat";
			}
			//supprime les fichiers images du dossier et le dossier
			$immo->removeDir($id);
			$em->remove($immo);
			$em->flush();

			$request->getSession()->getFlashBag()->add('notice', "Le bien a été supprimé correctement.");

			return $this->redirect($this->generateUrl('ag_agenceimmo_list', array("type"=>$type)));
		}

		// Si la requête est en GET, on affiche une page de confirmation avant de supprimer
		return $this->render('AGAgenceimmoBundle:Immo:delete.html.twig', array(
			'immo' => $immo,
			'form'   => $form->createView()
		));

	}
	
	public function supprimgAction(Request $request, $id){
		//on recupere lentitymanager de doctrine
		$em = $this->getDoctrine()->getManager();
		// On récupère le bien immo ayant l'id $id
		$immo = $em->getRepository('AGAgenceimmoBundle:Immobilier')->find($id);
		//initialise le compteur d images à supprimer
		$cpt=-1;
		//on recupere les images cochees sil y a eu validation du formulaire
		if(isset($_POST["validation"])){
			$cpt=0;
			foreach($_POST as $key=>$value){
				if($value=="on"){
					//incremente le compteur
					$cpt++;
					//recupere l'image en base à partir de son id
					$img=$em->getRepository('AGAgenceimmoBundle:Image')->find($key);
					//supprimme l'image du tableau d'images de l'objet Immobilier
					$immo->removeImage($img);
					//supprime en base l'image
					$em->remove($img);
					//supprime le fichier image
					$img->delete($img->getUrl());					
				}
			}
		}
		if($cpt>0){
			$em->flush();
			$request->getSession()->getFlashBag()->add('notice', 'les images ont bien été supprimées');
			return $this->redirect($this->generateUrl('ag_agenceimmo_view', array('id' => $immo->getId())));
		}else if($cpt==0){
			$request->getSession()->getFlashBag()->add('notice', 'Vous n\'avez sélectionné aucune image');
		}
		return $this->render('AGAgenceimmoBundle:Immo:supprimg.html.twig', array(
			'immobilier' => $immo
		));
	}
	
	public function ajoutimgAction(Request $request, $id){
		$img = new Image();
		
 
		// On crée le FormBuilder grâce au service form factory
		$formBuilder = $this->get('form.factory')->createBuilder('form', $img);

		// On ajoute les champs de l'entité que l'on veut à notre formulaire
		$formBuilder
		  ->add('file','file')
		  ->add('Ajouter','submit')
		;
		
		// À partir du formBuilder, on génère le formulaire
		$form = $formBuilder->getForm();

    	if($form->handleRequest($request)->isValid()){
			$img->upload($id);
			$em = $this->getDoctrine()->getManager();
			$immo = $em->getRepository('AGAgenceimmoBundle:Immobilier')->find($id);
			$immo->addImage($img);
			$em->persist($immo);
			$em->flush();
			$request->getSession()->getFlashBag()->add('notice', 'L\'image a bien été ajoutée.');
			return $this->redirect($this->generateUrl('ag_agenceimmo_view', array('id' => $id)));
		}
		return $this->render('AGAgenceimmoBundle:Immo:ajoutimg.html.twig', array(
			'id' => $id,
			'form' => $form->createView()
		));
	}
}
