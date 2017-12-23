<?php

/**
 * @file
 * Contains \Drupal\quiz\QuestionInterface.
 */

namespace Drupal\iquiz;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\user\UserInterface;

/**
 * Provides an interface for defining Question entities.
 *
 * @ingroup question
 */
interface QuestionInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {


  /**
   * Gets the bundle type of the quiz entity.
   *
   * @return string
   *    Returns the name of the bundle.
   */
  public function getType();

}
