<?php

namespace Drupal\iquiz;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Question Type entities.
 */
interface QuestionTypeInterface extends ConfigEntityInterface {
  // Add get/set methods for your configuration properties here.
  /**
   * Returns the description of the request message type.
   *
   * @return string
   *   The description of the type of this request message.
   */
  public function getDescription();
}
