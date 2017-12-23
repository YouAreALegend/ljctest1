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
class QuestionViewsData extends EntityViewsData implements EntityViewsDataInterface {
  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();
    /*
    $data['question']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Question'),
      'help' => $this->t('The Question ID.'),
    );
    */
    return $data;
  }

}
