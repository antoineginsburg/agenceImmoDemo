<?php
// src/AG/AgenceimmoBundle/Validator/Decimal.php

namespace AG\AgenceimmoBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Decimal extends Constraint
{
  public $message = "Vous avez déjà posté un message il y a moins de 15 secondes, merci d'attendre un peu.";
}