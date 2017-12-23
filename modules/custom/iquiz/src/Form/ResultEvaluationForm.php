<?php

namespace Drupal\iquiz\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form;
use Drupal\iquiz\AnswerInterface;
use Drupal\iquiz\QuestionInstanceInterface;

/**
 * Class ResultEvaluationForm.
 */
class ResultEvaluationForm extends FormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'result_evaluation_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, $result_id = NULL)
    {
        //Load result entity.
        $result = \Drupal::entityTypeManager()->getStorage('iquiz_result')->load($result_id);
        $result->set('state', 2);

        //Load answers belonged to the current result.
        $arrAids = \Drupal::entityQuery('iquiz_answer')
            ->condition('result_id', $result_id)
            ->execute();
        $arrAnswers = \Drupal::entityTypeManager()->getStorage('iquiz_answer')->loadMultiple($arrAids);
        $form = [];
        $index = 1;

        //Resort answers order by each questionInstance's weight
        usort($arrAnswers,[$this,'resortAnswers']);
        //Show each answer's information.
        foreach ($arrAnswers as $answer) {
            //Load data which form element needed.
            $questionInstance = \Drupal::entityTypeManager()
                ->getStorage('iquiz_question_instance')
                ->load($answer->get('question_instance_id')->target_id);
            $question = \Drupal::entityTypeManager()
                ->getStorage('iquiz_question')
                ->load($questionInstance->get('question_id')->target_id);
            $form[$answer->id()] = [
                '#prefix' => '<p>',
                '#suffix' => '</p>',
            ];

            $questionType = $question->bundle();
            $questionPluginManager = \Drupal::service('plugin.manager.iquiz.question_type_plugin');
            $questionPluginInstance = $questionPluginManager->createInstance($questionType);
            $questionPluginInstance->evaluateScore($form, $form_state, $questionInstance, $question, $answer, $index);
        }

        $form['resultId'] = [
            '#type' => "hidden",
            '#value' => $result_id,
        ];
        //=====================6 The save button===========================
        //Only showing up when result state is not evaluated,which means it won't show on score's edition situation.
        if (!$result->get('is_evaluated')->value) {
            $form['submit'] = [
                '#type' => 'submit',
                '#value' => $this->t('Save'),
            ];
            $result->save();
        } else {
            $form['submit'] = [
                '#type' => 'submit',
                '#value' => $this->t('Back to the results page'),
                '#submit' => array([$this, 'getBackToResultsPage']),
            ];
        }

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        //Only handle the first evaluation of the score,not responsible for editing.
        //Load result entity and it's property.
        $result_id = \Drupal::request()->request->get('resultId');
        $result = \Drupal::entityTypeManager()
            ->getStorage('iquiz_result')
            ->load($result_id);
        $resultScore = $result->get('score')->value;

        //Load answer entities and its' property.
        $aids = \Drupal::entityQuery('iquiz_answer')
            ->condition('result_id', $result_id)
            ->execute();
        $answers = \Drupal::entityTypeManager()
            ->getStorage('iquiz_answer')
            ->loadMultiple($aids);

        //==========1 Save answers' score and calculate the sum.===============
        //Get the sum of the score which answer was not evaluated automatically.
        $sumEvaluatedPoint = 0;
        foreach ($answers as $answer) {
            $is_evaluated = $answer->get('is_evaluated')->value;
            if (!$is_evaluated) {
                $score = \Drupal::request()->request->get($answer->id());
                $answer->set('score', $score);
                $answer->set('is_evaluated', 1);
                $answer->save();
                $sumEvaluatedPoint += $score;
            }
        }
        //==========2 Then add to the result's score which was calculated automatically.==========
        $result->set('score', $resultScore + $sumEvaluatedPoint);
        $result->set('is_evaluated', 1);
        $result->set('state', 3);
        $result->save();
        //==========3 Get back to the results page.==========
        $form_state->setRedirect('iquiz.quiz_results', ['quiz_id' => $result->get('quiz_id')->target_id]);
    }

    //Get back to the results page.
    public function getBackToResultsPage(array &$form, FormStateInterface $form_state)
    {
        $result = \Drupal::entityTypeManager()
            ->getStorage('iquiz_result')
            ->load($form['resultId']['#value']);
        $form_state->setRedirect('iquiz.quiz_results', ['quiz_id' => $result->get('quiz_id')->target_id]);
    }

    //Resort answers order by each questionInstance's weight
    public function resortAnswers(AnswerInterface $a1,AnswerInterface $a2){
        $qi1 = \Drupal::entityTypeManager()
            ->getStorage('iquiz_question_instance')
            ->load($a1->get('question_instance_id')->target_id);
        $qi2 = \Drupal::entityTypeManager()
            ->getStorage('iquiz_question_instance')
            ->load($a2->get('question_instance_id')->target_id);
        if($qi1->get('weight')->value==$qi2->get('weight')->value){
            return 0;
        }
        return $qi1->get('weight')->value>$qi2->get('weight')->value?1:-1;
    }

}
