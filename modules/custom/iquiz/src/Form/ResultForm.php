<?php

namespace Drupal\iquiz\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Quiz edit forms.
 *
 * @ingroup quiz
 */
class ResultForm extends ContentEntityForm {
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\quiz\Entity\Quiz */
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

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label result.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label result.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.iquiz_result.canonical', ['iquiz_result' => $entity->id()]);
  }

}
