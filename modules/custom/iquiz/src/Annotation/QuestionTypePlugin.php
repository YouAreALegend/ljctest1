<?php

namespace Drupal\iquiz\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a question type plugin annotation object.
 *
 * Plugin Namespace: Plugin\QuestionType
 *
 *
 * @see hook_question_type_info_alter()
 * @see \Drupal\iquiz\QuestionTypePluginInterface
 * @see \Drupal\iquiz\QuestionTypePluginBase
 * @see \Drupal\iquiz\QuestionTypePluginManager
 * @see plugin_api
 *
 * @Annotation
 */
class QuestionTypePlugin extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the question type plugin.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $label;

  /**
   * A brief description of the question type plugin.
   *
   * This will be shown when adding or configuring this question type plugin.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation (optional)
   */
  public $description = '';

}
