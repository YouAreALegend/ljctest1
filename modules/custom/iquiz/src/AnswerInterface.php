<?php

namespace Drupal\iquiz;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Quiz entities.
 *
 * @ingroup quiz
 */
interface AnswerInterface extends ContentEntityInterface, EntityOwnerInterface {

}
