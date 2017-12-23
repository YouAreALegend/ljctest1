<?php

/**
 * @file
 * Contains \Drupal\quiz\Entity\QuizType.
 */

namespace Drupal\iquiz\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\iquiz\QuizTypeInterface;

/**
 * Defines the Quiz type entity.
 *
 * @ConfigEntityType(
 *   id = "iquiz_quiz_type",
 *   label = @Translation("Quiz type"),
 *   handlers = {
 *     "list_builder" = "Drupal\iquiz\QuizTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\iquiz\Form\QuizTypeForm",
 *       "edit" = "Drupal\iquiz\Form\QuizTypeForm",
 *       "delete" = "Drupal\iquiz\Form\QuizTypeDeleteForm"
 *     }
 *   },
 *   config_prefix = "iquiz_quiz_type",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   bundle_of = "iquiz_quiz",
 *
 *   links = {
 *     "canonical" = "/admin/iquiz/quiz_type/{quiz_type}",
 *     "edit-form" = "/admin/iquiz/quiz_type/{quiz_type}/edit",
 *     "delete-form" = "/admin/iquiz/quiz_type/{quiz_type}/delete",
 *     "collection" = "/admin/iquiz/quiz_type"
 *   }
 * )
 */
class QuizType extends ConfigEntityBundleBase implements QuizTypeInterface {
  /**
   * The Quiz type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Quiz type label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Answer type uuid.
   *
   * @var string
   */
  protected $uuid;
}
