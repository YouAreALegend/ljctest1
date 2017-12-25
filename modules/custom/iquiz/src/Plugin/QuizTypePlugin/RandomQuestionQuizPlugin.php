<?php
/**
 * Created by PhpStorm.
 * User: Warri
 * Date: 2017/12/25
 * Time: 21:39
 */

namespace Drupal\iquiz\Plugin\QuizTypePlugin;

use Drupal\iquiz\QuizTypePluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\iquiz\QuestionInstanceInterface;

  /**
   * Provides Single choice plugin type.
   *
   * @QuizTypePlugin(
   *   id = "random_question_quiz",
   *   label = "Random Question Quiz Plugin",
   *   description = "Random Question Quiz Plugin"
   * )
   */
class RandomQuestionQuizPlugin extends QuizTypePluginBase
{
  public function __construct(array $container, $plugin_id, $plugin_definition)
  {
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
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

  public function sortQuestionInstances($quiz_id = NULL)
  {
    $questionInstanceIds = \Drupal::entityQuery('iquiz_question_instance')
      ->condition('quiz_id', $quiz_id)
      ->execute();
    $questionInstances = \Drupal::entityTypeManager()
      ->getStorage('iquiz_question_instance')
      ->loadMultiple($questionInstanceIds);


    $shuffleGroup = [];
    foreach ($questionInstances as $key => $questionInstance) {
      $qid = $questionInstance->get('question_id')->target_id;
      $question = \Drupal::entityTypeManager()
        ->getStorage('iquiz_question')
        ->load($qid);
      if (!array_key_exists($question->bundle(), $shuffleGroup)) {
        $shuffleGroup[$question->bundle()] = [];
      }
      array_push($shuffleGroup[$question->bundle()], $key);
    }
    srand(\Drupal::currentUser()->id());
    foreach ($shuffleGroup as $group) {
      $array = [];
      foreach ($group as $index) {
        array_push($array, $questionInstances[$index]);
      }
      shuffle($array);
      foreach ($group as $key => $index) {
        $questionInstances[$index] = $array[$key];
      }
    }

    return $questionInstances;
  }

  public function isShuffled(){
    return true;
  }

} 