<?php

namespace Drupal\iquiz;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Quiz entities.
 *
 * @ingroup quiz
 */
interface ResultInterface extends ContentEntityInterface, EntityOwnerInterface {

}
