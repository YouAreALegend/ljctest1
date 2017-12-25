<?php
/**
 * Created by PhpStorm.
 * User: Warri
 * Date: 2017/12/25
 * Time: 9:42
 */

namespace Drupal\iquiz;

use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
/**
 * Provides a base class for quiz type plugin.
 *
 * @see \Drupal\iquiz\Annotation\QuizTypePlugin
 * @see \Drupal\iquiz\QuizTypePluginInterface
 * @see \Drupal\iquiz\QuizTypePluginBase
 * @see \Drupal\iquiz\QuizTypePluginManager
 * @see plugin_api
 */
abstract class QuizTypePluginBase extends PluginBase implements QuizTypePluginInterface, ContainerFactoryPluginInterface {

    /**
     * The FieldValidationRule ID.
     *
     * @var string
     */
    protected $uuid;


    /**
     * {@inheritdoc}
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
    }


    /**
     * {@inheritdoc}
     */
    public function getUuid() {
        return $this->uuid;
    }
} 