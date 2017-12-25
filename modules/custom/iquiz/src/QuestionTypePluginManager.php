<?php

namespace Drupal\iquiz;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Manages question type plugins.
 *
 * @see hook_question_type_plugins_alter()
 * @see \Drupal\iquiz\Annotation\QuestionTypePlugin
 * @see \Drupal\iquiz\QuestionTypePluginInterface
 * @see \Drupal\iquiz\QuestionTypePluginBase
 * @see plugin_api
 */
class QuestionTypePluginManager extends DefaultPluginManager {

  /**
   * Constructs a new QuestionTypePluginManager.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/QuestionTypePlugin', $namespaces, $module_handler, 'Drupal\iquiz\QuestionTypePluginInterface', 'Drupal\iquiz\Annotation\QuestionTypePlugin');

    $this->alterInfo('question_type_plugins');
    $this->setCacheBackend($cache_backend, 'question_type_plugins');
  }

}
