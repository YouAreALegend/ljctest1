<?php

/**
 * @file
 * Contains \Drupal\quiz\QuestionTypeListBuilder.
 */

namespace Drupal\iquiz;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Provides a listing of Question Type entities.
 */
class QuestionTypeListBuilder extends ConfigEntityListBuilder {
  use LinkGeneratorTrait;

  protected $quizId;
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Question Type');
    $header['id'] = $this->t('Machine name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /*
	$row['label'] = $this->l(
      $this->getLabel($entity),
      new Url(
        'entity.question.add_form', array(
          'question_type' => $entity->id(),
          'quiz' => $this->getQuizId(),
        )
      )
    );
	*/
    $row['id'] = $entity->id();
    // You probably want a few more properties here...
    return $row + parent::buildRow($entity);
  }


}
