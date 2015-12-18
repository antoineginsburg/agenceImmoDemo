<?php
// src/AG/AgenceimmoBundle/Validator/DecimalValidator.php

namespace AG\AgenceimmoBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DecimalValidator extends ConstraintValidator
{

	public function validate($value, Constraint $constraint)
	{
		// C'est cette ligne qui déclenche l'erreur pour le formulaire, avec en argument le message
		$this->context->addViolation($constraint->message);
	}
}