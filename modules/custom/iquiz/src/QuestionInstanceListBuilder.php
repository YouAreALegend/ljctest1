<?php

namespace Drupal\iquiz;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Question entities.
 *
 * @ingroup question
 */
class QuestionInstanceListBuilder extends EntityListBuilder {
  use LinkGeneratorTrait;

  protected $ids;
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Question instance ID');
    $header['quiz'] = $this->t('Quiz');
	$header['question'] = $this->t('Question');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\quiz\Entity\question */
    $row['id'] = $entity->id();
    $row['quiz'] = $this->l(
      $entity->quiz_id->entity->name->value,
      new Url(
        'entity.iquiz_quiz.edit_form', array(
          'iquiz_quiz' => $entity->quiz_id->target_id,
        )
      )
    );
    $row['question'] = $this->l(
      $entity->question_id->entity->question->value,
      new Url(
        'entity.iquiz_question.edit_form', array(
          'iquiz_question' => $entity->question_id->target_id,
        )
      )
    );	
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function setIds($ids) {
    $this->ids = $ids;
  }

  public function getIds() {
    return $this->ids;
  }

  protected function getEntities() {
    $ids = $this->getIds();
    return $this->storage->loadMultiple($ids);
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build['table'] = array(
      '#type' => 'table',
      '#header' => $this->buildHeader(),
      '#title' => $this->getTitle(),
      '#rows' => array(),
      '#empty' => $this->t('There is no @label yet.', array('@label' => $this->entityType->getLabel())),
      '#cache' => [
        'contexts' => $this->entityType->getListCacheContexts(),
        'tags' => $this->entityType->getListCacheTags(),
      ],
    );
    foreach ($this->getEntities() as $entity) {
      if ($row = $this->buildRow($entity)) {
        $build['table']['#rows'][$entity->id()] = $row;
      }
    }

    // Only add the pager if a limit is specified.
    if ($this->limit) {
      $build['pager'] = array(
        '#type' => 'pager',
      );
    }
    return $build;
  }
}
