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
 *   id = "short_answer",
 *   label = "Short Answer plugin",
 *   description = "Short Answer plugin"
 * )
 */
class ShortAnswerPlugin extends QuestionTypePluginBase
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
        if ($isShuffled) {
            $form[$questionInstance->id()] = array(
                '#type' => 'textarea',
                '#title' => $this->t($questionNumber . "." . $question->get('question')->value),
                '#default_value' => NULL,
                '#weight' => $questionNumber,
            );
        } else {
            $form[$questionInstance->id()] = array(
                '#type' => 'textarea',
                '#title' => $this->t($questionNumber . "." . $question->get('question')->value),
                '#default_value' => NULL,
                '#weight' => $questionInstance->get('weight')->value,
            );
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
    public function takeQuizFormSubmit(AnswerInterface $quiz_answer, $submittedAnswer, $correctAnswer, $score, &$sumScore, &$isEvaluated)
    {
        $quiz_answer->set("answer", $submittedAnswer);

        $quiz_answer->set('score', 0);
        $quiz_answer->set("is_evaluated", 0);

        $quiz_answer->save();
    }


    /**
     * boolean value indicate if it is auto evaluate .
     *
     */
    public function isAutoEvaluate()
    {
        return false;
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
        //Callback function name:Select a way to show score form element judging by state:is_evaluated.
        if (!$answer->get('is_evaluated')->value) {
            $scoreHandler = "evaluate";
        } else {
            $scoreHandler = "edit";
        }
        //=================1 Question content========================
        $questionContent = $question->get('question')->value;
        $form[$answer->id()]['questionContent'] = [
            '#prefix' => "<div><b>" . $this->t('Answer No.' . $index . ':') . "</b></div><div>",
            '#suffix' => "</div>",
            '#markup' => $questionContent,
        ];
        $index++;

        //=================2 Correct answer of the question==========
        $questionAnswer = $question->get('answer')->value;
        if ($questionAnswer != Null && $questionAnswer != "") {
            $form[$answer->id()]['questionAnswer'] = [
                '#prefix' => "<div><b>" . $this->t('Correct Answer:') . "</b></div><div>",
                '#suffix' => "</div>",
                '#markup' => $questionAnswer,
            ];
        }

        //=================3 Submitted answer=========================
        $submittedAnswer = $answer->get('answer')->value;
        $form[$answer->id()]['submittedAnswer'] = [
            '#prefix' => "<div><b>" . $this->t('Submitted Answer:') . "</b></div><div>",
            '#suffix' => "</div>",
            '#markup' => $submittedAnswer,
        ];
        //=================5 Evaluate score===========================
        //1)evaluateScore:When form is saved,pass result id to submitForm function.Effect to multiple answers' score.
        //2)editScore:Add edit button.Won't call submitForm function.Responsible for one answer's score which operated now.
        $form = call_user_func_array([$this, $scoreHandler], array($form, $answer, $questionInstance));
    }

    //Callback function.Return score form element showing when first evaluation started.
    public function evaluate(array $form, AnswerInterface $answer, QuestionInstanceInterface $questionInstance)
    {
        $form[$answer->id()][$answer->id()] = array(
            '#prefix' => "<div><b>" . $this->t('Need to evaluate this answer\'s score.') . "</b></div><div>",
            '#suffix' => "</div>",
            '#type' => 'number',
            '#default_value' => 0,
            '#min' => 0,
            '#max' => $questionInstance->get('score')->value,
        );
        return $form;
    }

    //Callback function.Return score form element showing when evaluation has been finished.
    public function edit(array $form, AnswerInterface $answer, QuestionInstanceInterface $questionInstance)
    {
        $form[$answer->id()][$answer->id()] = array(
            '#prefix' => "<div><b>" . $this->t('Edit this answer\'s score.') . "</b></div><div>",
            '#suffix' => "</div>",
            '#type' => 'number',
            '#default_value' => $answer->get('score')->value,
            '#min' => 0,
            '#max' => $questionInstance->get('score')->value,
        );

        $form[$answer->id()]['edit'] = [
            '#type' => 'submit',
            '#value' => $this->t('edit'),
            "#executes_submit_callback" => false,
            '#ajax' => array(
                'callback' => [$this, 'editScoreCallback'],
                'event' => 'click',
            ),
        ];
        return $form;
    }

    //Callback function when editing one answer's score.
    public function editScoreCallback(array &$form, FormStateInterface $form_state)
    {
        //Save changed answer's score.
        $answerId = $form_state->getTriggeringElement()['#array_parents'][0];
        $answer = \Drupal::entityTypeManager()->getStorage('iquiz_answer')->load($answerId);
        $aPreScore = $answer->get('score')->value;
        $aChangedScore = $form[$answerId][$answerId]['#value'];
        $valueDiff = $aChangedScore - $aPreScore;
        $answer->set('score', $aChangedScore);
        $answer->save();

        //Save changed total score to the result.
        $result = \Drupal::entityTypeManager()->getStorage('iquiz_result')->load($form['resultId']['#value']);
        $rPreScore = $result->get('score')->value;
        $rChangedScore = $rPreScore + $valueDiff;
        $result->set('score', $rChangedScore);
        $result->save();
        drupal_set_message("Score has been edited successfully.");
    }

} 