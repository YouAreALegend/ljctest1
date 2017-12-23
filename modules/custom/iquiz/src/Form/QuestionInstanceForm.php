<?php

/**
 * @file
 * Contains \Drupal\quiz\Entity\Form\QuestionInstanceForm.
 */

namespace Drupal\iquiz\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Question edit forms.
 *
 * @ingroup question
 */
class QuestionInstanceForm extends ContentEntityForm {
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\quiz\Entity\question */
    $form = parent::buildForm($form, $form_state);
    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array $form, FormStateInterface $form_state) {
    // Build the entity object from the submitted values.
    $entity = parent::submit($form, $form_state);

    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = $entity->save();
	$quiz_id = $entity->quiz_id->target_id;
	$form_state->setRedirect('iquiz.quiz.questions', ['iquiz_quiz' => $quiz_id]);
  }

}
