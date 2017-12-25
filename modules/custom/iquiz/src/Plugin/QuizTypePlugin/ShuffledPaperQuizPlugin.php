<?php

namespace Drupal\iquiz\Plugin\QuizTypePlugin;

use Drupal\iquiz\QuizTypePluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides Single choice plugin type.
 *
 * @QuizTypePlugin(
 *   id = "shuffled_paper_quiz",
 *   label = "Shuffled Paper Quiz Plugin",
 *   description = "Shuffled Paper Quiz Plugin"
 * )
 */
class ShuffledPaperQuizPlugin extends QuizTypePluginBase {
  public function __construct(array $container, $plugin_id, $plugin_definition) {
    parent::__construct($container, $plugin_id, $plugin_definition);
  }

  /**
   * Creates an instance of the plugin.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container to pull out services used in the plugin.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @return static
   *   Returns an instance of this plugin.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

  public function sortQuestionInstances($quiz_id = NULL) {
    $path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR . 'ShuffledPaper.xml';
    $xml = simplexml_load_file($path);
    $qids = [];
    foreach ($xml->QuestionSequence[0]->children() as $id) {
      array_push($qids, $id[0]->__toString());
    }
    $questionInstances = \Drupal::entityTypeManager()
      ->getStorage('iquiz_question_instance')
      ->loadMultiple($qids);
    return $questionInstances;
  }

  public function isShuffled() {
    return TRUE;
  }

} 