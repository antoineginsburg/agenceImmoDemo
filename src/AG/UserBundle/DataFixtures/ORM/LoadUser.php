<?php
// src/AG/UserBundle/DataFixtures/ORM/LoadUser.php

namespace AG\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AG\UserBundle\Entity\User;

class LoadUser implements FixtureInterface
{
	public function load(ObjectManager $manager)
	{
		// Les noms d'utilisateurs à créer
		$listNames = array('Antoine', 'Michel', 'Manessa');
		foreach ($listNames as $name) {
			// On crée l'utilisateur
			$user = new User;

			// Le nom d'utilisateur et le mot de passe sont identiques
			$user->setUsername($name);
			$user->setPassword($name);

			// On ne se sert pas du sel pour l'instant
			$user->setSalt('');
			if($name=="Antoine"){
				// On met ROLE_ADMIN pour Antoine
				$user->setRoles(array('ROLE_ADMIN'));
			}else{
				//et le ROLE_USER pour les autres
				$user->setRoles(array('ROLE_USER'));
			}

			// On le persiste
			$manager->persist($user);
		}

		// On déclenche l'enregistrement
		$manager->flush();
	}
}