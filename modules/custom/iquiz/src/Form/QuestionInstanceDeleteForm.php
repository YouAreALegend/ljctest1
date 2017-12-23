<?php

namespace Drupal\iquiz\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a form for deleting Question entities.
 *
 * @ingroup question
 */
class QuestionInstanceDeleteForm extends ContentEntityConfirmFormBase {
  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete question instance %name?', array('%name' => $this->entity->label()));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
	$entity = $this->entity;
	$quiz_id = $entity->quiz_id->target_id;
    return new Url('iquiz.quiz.questions', ['iquiz_quiz' => $quiz_id]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
   //todo
    $this->entity->delete();
    /*
    drupal_set_message(
      $this->t('Deleted Quiz @label.',
        [
          '@label' => $this->entity->label(),
        ]
        )
    ); */

    $form_state->setRedirectUrl($this->getCancelUrl());   
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t('This action cannot be undone and will also delete all the answers given to this question instance.');
  }

}
