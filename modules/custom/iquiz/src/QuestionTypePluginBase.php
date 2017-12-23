<?php

/**
 * @file
 * Contains \Drupal\field_validation\FieldValidationRuleBase.
 */

namespace Drupal\iquiz;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;

/**
 * Provides a base class for question type plugin.
 *
 * @see \Drupal\iquiz\Annotation\QuestionTypePlugin
 * @see \Drupal\iquiz\QuestionTypePluginInterface
 * @see \Drupal\iquiz\QuestionTypePluginBase
 * @see \Drupal\iquiz\QuestionTypePluginManager
 * @see plugin_api
 */
abstract class QuestionTypePluginBase extends PluginBase implements QuestionTypePluginInterface, ContainerFactoryPluginInterface {

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
