<?php

namespace Drupal\iquiz\Plugin\QuizTypePlugin;

use Drupal\iquiz\QuizTypePluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\iquiz\QuestionInstanceInterface;

/**
 * Provides Single choice plugin type.
 *
 * @QuizTypePlugin(
 *   id = "random_quiz",
 *   label = "Random Quiz Plugin",
 *   description = "Random Quiz Plugin"
 * )
 */
class RandomQuizPlugin extends QuizTypePluginBase
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
        usort($questionInstances, [$this, 'resortQuestionInstance']);
        return $questionInstances;
    }

    //根据questionInstance的weight属性比较的方式，对questionInstances的数组进行自定义排序
    public function resortQuestionInstance(QuestionInstanceInterface $qi1, QuestionInstanceInterface $qi2)
    {
        if ($qi1->get('weight')->value == $qi2->get('weight')->value) {
          return $qi1->get('question_id')->target_id > $qi2->get('question_id')->target_id ? 1 : -1;
        }
        return $qi1->get('weight')->value > $qi2->get('weight')->value ? 1 : -1;
    }

    public function isShuffled(){
        return false;
    }

} 