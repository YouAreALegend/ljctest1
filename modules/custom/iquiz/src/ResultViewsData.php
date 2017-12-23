<?php

/**
 * @file
 * Contains \Drupal\iquiz\Entity\question.
 */

namespace Drupal\iquiz;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Question entities.
 */
class ResultViewsData extends EntityViewsData implements EntityViewsDataInterface {
  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    return $data;
  }

}
