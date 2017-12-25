<?php

namespace Drupal\iquiz;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\iquiz\FieldValidationRuleSetInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the interface for Question Type Plugin.
 *
 * @see \Drupal\iquiz\Annotation\QuestionTypePlugin
 * @see \Drupal\iquiz\QuestionTypePluginInterface
 * @see \Drupal\iquiz\QuestionTypePluginBase
 * @see \Drupal\iquiz\QuestionTypePluginManager
 * @see plugin_api
 */
interface QuestionTypePluginInterface extends PluginInspectionInterface{

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
  public function takeQuizForm(array &$form, FormStateInterface $form_state,QuestionInstanceInterface $questionInstance,QuestionInterface $question, $questionNumber, $isShuffled);

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
  public function takeQuizFormSubmit(AnswerInterface $quiz_answer,$submittedAnswer,$correctAnswer,$score,&$sumScore,&$isEvaluated);

  /**
   * boolean value indicate if it is auto evaluate .
   * 
   */
  public function isAutoEvaluate();
  
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
  public function evaluateScore(array &$form, FormStateInterface $form_state,QuestionInstanceInterface $questionInstance, QuestionInterface $question,AnswerInterface $answer, &$index);

  //question view
  
  //answer view
}
