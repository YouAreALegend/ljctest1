<?php

namespace Drupal\iquiz\Plugin\QuestionTypePlugin;

use Drupal\iquiz\AnswerInterface;
use Drupal\iquiz\form;
use Drupal\iquiz\QuestionTypePluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\iquiz\QuestionInstanceInterface;
use Drupal\iquiz\QuestionInterface;


/**
 * Provides Single choice plugin type.
 *
 * @QuestionTypePlugin(
 *   id = "single_choice",
 *   label = "Single choice plugin",
 *   description = "Single choice plugin"
 * )
 */
class SingleChoicePlugin extends QuestionTypePluginBase
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
     *
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

    /**
     * Define the form component for quiz.
     *
     * @param array $form
     * @param FormStateInterface $form_state
     * @param QuestionInstanceInterface $questionInstance
     * @param QuestionInterface $question
     * @param $questionNumber
     * @param $isShuffled
     *
     * @return form component array
     *
     */
    public function takeQuizForm(array &$form, FormStateInterface $form_state,QuestionInstanceInterface $questionInstance,QuestionInterface $question,$questionNumber, $isShuffled)
    {
        $form[$questionInstance->id()] = array(
            '#type' => 'radios',
            '#title' => $this->t($questionNumber . "." . $question->get('question')->value),
            '#default_value' => NULL,
        );
        if ($isShuffled) {
            $form[$questionInstance->id()]['#options'] = array(
                'D' => $this->t("A." . $question->get('field_single_choice_option_d')->value),
                'C' => $this->t("B." . $question->get('field_single_choice_option_c')->value),
                'B' => $this->t("C." . $question->get('field_single_choice_option_b')->value),
                'A' => $this->t("D." . $question->get('field_single_choice_option_a')->value),
            );
            $form[$questionInstance->id()]['#weight'] = $questionNumber;
        } else {
            $form[$questionInstance->id()]['#options'] = array(
                'A' => $this->t("A." . $question->get('field_single_choice_option_a')->value),
                'B' => $this->t("B." . $question->get('field_single_choice_option_b')->value),
                'C' => $this->t("C." . $question->get('field_single_choice_option_c')->value),
                'D' => $this->t("D." . $question->get('field_single_choice_option_d')->value),
            );
            $form[$questionInstance->id()]['#weight'] = $questionInstance->get('weight')->value;
        }
    }

    /**
     * Process the form component submit.
     *
     * @param AnswerInterface $quiz_answer
     * @param $submittedAnswer
     * @param $correctAnswer
     * @param $score
     * @param $sumScore
     * @param $isEvaluated
     *
     */
    public function takeQuizFormSubmit(AnswerInterface $quiz_answer,$submittedAnswer,$correctAnswer,$score,&$sumScore,&$isEvaluated)
    {
        $quiz_answer->set("answer", $submittedAnswer);

        //Compare question correct answer with the submitted answer.If right,get score,otherwise.get 0.
        if (strcmp($submittedAnswer, $correctAnswer) == 0) {
            $quiz_answer->set('score', $score);
            $sumScore += $score;
        } else {
            $quiz_answer->set('score', 0);
        }
        $quiz_answer->set("is_evaluated", 1);

        $quiz_answer->save();
    }

    /**
     * boolean value indicate if it is auto evaluate .
     *
     */
    public function isAutoEvaluate()
    {
        return true;
    }

    /**
     * evaluate score.
     *
     * @param array $form
     * @param FormStateInterface $form_state
     * @param QuestionInstanceInterface $questionInstance
     * @param QuestionInterface $question
     * @param AnswerInterface $answer
     * @param $index
     *
     */
    public function evaluateScore(array &$form, FormStateInterface $form_state,QuestionInstanceInterface $questionInstance, QuestionInterface $question,AnswerInterface $answer, &$index)
    {
        //=================1 Question content========================
        $questionContent = $question->get('question')->value;
        $form[$answer->id()]['questionContent'] = [
            '#prefix' => "<div><b>" . $this->t('Answer No.' . $index . ':') . "</b></div><div>",
            '#suffix' => "</div>",
            '#markup' => $questionContent,
        ];
        $index++;
        //=================2 Options provided by question============
        $arr_options = [];
        array_push($arr_options, "A:");
        array_push($arr_options, $question->get('field_single_choice_option_a')->value);
        array_push($arr_options, "\r\nB:");
        array_push($arr_options, $question->get('field_single_choice_option_b')->value);
        array_push($arr_options, "\r\nC:");
        array_push($arr_options, $question->get('field_single_choice_option_c')->value);
        array_push($arr_options, "\r\nD:");
        array_push($arr_options, $question->get('field_single_choice_option_d')->value);
        $stroOptions = join("", $arr_options);
        $form[$answer->id()]['strOptions'] = [
            '#prefix' => '<div>',
            '#suffix' => '</div>',
            '#markup' => $stroOptions,
        ];

        //=================3 Correct answer of the question==========
        $questionAnswer = $question->get('answer')->value;
        if ($questionAnswer != Null && $questionAnswer != "") {
            $form[$answer->id()]['questionAnswer'] = [
                '#prefix' => "<div><b>" . $this->t('Correct Answer:') . "</b></div><div>",
                '#suffix' => "</div>",
                '#markup' => $questionAnswer,
            ];
        }

        //=================4 Submitted answer=========================
        $submittedAnswer = $answer->get('answer')->value;
        $form[$answer->id()]['submittedAnswer'] = [
            '#prefix' => "<div><b>" . $this->t('Submitted Answer:') . "</b></div><div>",
            '#suffix' => "</div>",
            '#markup' => $submittedAnswer,
        ];
    }

} 