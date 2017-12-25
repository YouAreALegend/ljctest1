<?php
/**
 * Created by PhpStorm.
 * User: Warri
 * Date: 2017/12/25
 * Time: 9:56
 */

namespace Drupal\iquiz\Annotation;


use Drupal\Component\Annotation\Plugin;
/**
 * Defines a quiz type plugin annotation object.
 *
 * Plugin Namespace: Plugin\QuizTypePlugin
 *
 *
 * @see hook_quiz_type_info_alter()
 * @see \Drupal\iquiz\QuizTypePluginInterface
 * @see \Drupal\iquiz\QuizTypePluginBase
 * @see \Drupal\iquiz\QuizTypePluginManager
 * @see plugin_api
 *
 * @Annotation
 */
class QuizTypePlugin extends Plugin{

    /**
     * The plugin ID.
     *
     * @var string
     */
    public $id;

    /**
     * The human-readable name of the quiz type plugin.
     *
     * @ingroup plugin_translatable
     *
     * @var \Drupal\Core\Annotation\Translation
     */
    public $label;

    /**
     * A brief description of the quiz type plugin.
     *
     * This will be shown when adding or configuring this quiz type plugin.
     *
     * @ingroup plugin_translatable
     *
     * @var \Drupal\Core\Annotation\Translation (optional)
     */
    public $description = '';
} 