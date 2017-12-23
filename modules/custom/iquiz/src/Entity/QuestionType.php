<?php

/**
 * @file
 * Contains \Drupal\iquiz\Entity\QuestionType.
 */

namespace Drupal\iquiz\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\iquiz\QuestionTypeInterface;

/**
 * Defines the Question Type entity.
 *
 * @ConfigEntityType(
 *   id = "iquiz_question_type",
 *   label = @Translation("Question Type"),
 *   handlers = {
 *     "list_builder" = "Drupal\iquiz\QuestionTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\iquiz\Form\QuestionTypeForm",
 *       "edit" = "Drupal\iquiz\Form\QuestionTypeForm",
 *       "delete" = "Drupal\iquiz\Form\QuestionTypeDeleteForm"
 *     }
 *   },
 *   bundle_of = "iquiz_question",
 *   config_prefix = "iquiz_question_type",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "canonical" = "/admin/iquiz/question-types/{iquiz_question_type}",
 *     "add-form" = "/admin/iquiz/question-types/add",
 *     "edit-form" = "/admin/iquiz/question-types/manage/{iquiz_question_type}/edit",
 *     "delete-form" = "/admin/iquiz/question-types/manage/{iquiz_question_type}/delete",
 *     "collection" = "/admin/iquiz/question-types"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *   } 
 * )
 */
class QuestionType extends ConfigEntityBundleBase implements QuestionTypeInterface {
  /**
   * The Question Type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Question Type label.
   *
   * @var string
   */
  protected $label;

  /**
   * The description.
   *
   * @var string
   */
  protected $description;
  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }
  
}
