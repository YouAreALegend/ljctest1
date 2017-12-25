<?php
/**
 * Created by PhpStorm.
 * User: Warri
 * Date: 2017/12/25
 * Time: 9:42
 */

namespace Drupal\iquiz;


use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Manages quiz type plugins.
 *
 * @see hook_quiz_type_plugins_alter()
 * @see \Drupal\iquiz\Annotation\QuestionTypePlugin
 * @see \Drupal\iquiz\QuestionTypePluginInterface
 * @see \Drupal\iquiz\QuestionTypePluginBase
 * @see plugin_api
 */
class QuizTypePluginManager extends DefaultPluginManager
{
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
    public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler)
    {
        parent::__construct('Plugin/QuizTypePlugin', $namespaces, $module_handler, 'Drupal\iquiz\QuizTypePluginInterface', 'Drupal\iquiz\Annotation\QuizTypePlugin');

        $this->alterInfo('quiz_type_plugins');
        $this->setCacheBackend($cache_backend, 'quiz_type_plugins');
    }


} 