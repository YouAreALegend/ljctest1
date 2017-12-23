<?php

/**
 * @file
 * Contains \Drupal\iquiz\Form\QuestionTypeForm.
 */

namespace Drupal\iquiz\Form;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\BundleEntityFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class QuestionTypeForm.
 *
 * @package Drupal\iquiz\Form
 */
class QuestionTypeForm extends BundleEntityFormBase {
  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $question_type = $this->entity;
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $question_type->label(),
      '#description' => $this->t("Label for the Question Type."),
      '#required' => TRUE,
    );

    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $question_type->id(),
      '#machine_name' => array(
        'exists' => '\Drupal\iquiz\Entity\QuestionType::load',
      ),
      '#disabled' => !$question_type->isNew(),
    );

    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save'),
    );

    return $this->protectBundleIdElement($form);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $question_type = $this->entity;
    $status = $question_type->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Question Type.', [
          '%label' => $question_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Question Type.', [
          '%label' => $question_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($question_type->urlInfo('collection'));
  }

}
