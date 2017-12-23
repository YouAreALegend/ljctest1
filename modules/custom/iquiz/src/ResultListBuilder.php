<?php

namespace Drupal\iquiz;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Quiz entities.
 *
 * @ingroup quiz
 */
class ResultListBuilder extends EntityListBuilder {
  use LinkGeneratorTrait;
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Result ID');
    //$header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\quiz\Entity\Result */
    $row['id'] = $entity->id();
	/*
    $row['name'] = $this->l(
      $this->getLabel($entity),
      new Url(
        'entity.quiz.canonical', array(
          'quiz' => $entity->id(),
        )
      )
    );
	*/

    return $row + parent::buildRow($entity);
  }

}
