<?php

/**
 * @file
 * Contains \Drupal\quiz\Entity\Form\questionForm.
 */

namespace Drupal\iquiz\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form;
use Drupal\Core\Language\Language;
use Drupal\iquiz\QuestionInterface;
use Drupal\iquiz\QuestionTypeInterface;

/**
 * Form controller for Question edit forms.
 *
 * @ingroup question
 */
class QuestionForm extends ContentEntityForm
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        /* @var $entity \Drupal\quiz\Entity\question */
        $entity = $this->entity;
        // $_GET['quiz_id='] ,增加表单元素quiz_id, 类型value
        /*
        drupal_set_message($_GET['quiz_id']);
        if (isset($_GET['quiz_id'])) {
          //$message->rm_id->value = $_GET['rm_id'];
          drupal_set_message('123:' . $_GET['quiz_id']);
          $form['quiz_id'] = array(
            '#type' => 'value',
            '#value' => $_GET['quiz_id'],
          );
        }
    */
        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function form(array $form, FormStateInterface $form_state)
    {
        //TODO add validation and strtoupper
        /* @var $entity \Drupal\quiz\Entity\Question */
        $entity = $this->entity;
        $form = parent::form($form, $form_state);
        if (strcmp($entity->getType(), 'short_answer') == 0) {
            $form['answer']['widget'][0]['value']['#type'] = 'textarea';
        }
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submit(array $form, FormStateInterface $form_state)
    {
        // Build the entity object from the submitted values.
        $entity = parent::submit($form, $form_state);
        //$quiz_id = $form_state->get('quiz_id');
        //if($quiz_id){
        //自动生成一个question instance 实体
        //quiz_id, question_id, score,  weight, parent_id:0
        //}
        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $form, FormStateInterface $form_state)
    {
        $entity = $this->entity;
        $status = $entity->save();


        switch ($status) {
            case SAVED_NEW:
                drupal_set_message($this->t('Created the %label Question.', [
                    '%label' => $entity->label(),
                ]));
                break;

            default:
                drupal_set_message($this->t('Saved the %label Question.', [
                    '%label' => $entity->label(),
                ]));
        }
        //$quiz_id = $form_state->get('quiz_id');
        //drupal_set_message('save:' . $_GET['quiz_id']);
        //drupal_set_message('save123:' . $quiz_id);
        $quiz_id = isset($_GET['quiz_id']) ? $_GET['quiz_id'] : null;
        //检查quiz是否存在，只有存在才有意义
        $quiz = \Drupal::entityTypeManager()->getStorage('iquiz_quiz')->load($quiz_id);
        drupal_set_message('save:' . $quiz->name->value);
        if (!empty($quiz)) {
            //自动生成一个question instance 实体
            //quiz_id, question_id, score,  weight, parent_id:0
            //$question_instance = Drupal::entityTypeManager()->getStorage('iquiz_question_instance')->create([]);
            $question_instance = \Drupal::entityTypeManager()->getStorage('iquiz_question_instance')->create([]);

            $question_instance->quiz_id->target_id = $quiz_id;
            $question_instance->question_id->target_id = $entity->id();
            $question_instance->score->value = $entity->score->value;
            $question_instance->save();
            $form_state->setRedirect('iquiz.quiz.questions', ['iquiz_quiz' => $quiz_id]);

        }
        /*
        if ($entity instanceof QuestionInterface) {
          $form_state->setRedirect('entity.quiz.canonical_admin', ['quiz' => $entity->get('quiz')->target_id]);
        }
        else {
          $form_state->setRedirect('entity.quiz.canonical', ['quiz' => \Drupal::request()->attributes->get('quiz')]);
        }
        */
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state);
        $entity = $form_state->getFormObject()->getEntity();
        $answer = $form_state->getValue('answer')[0]['value'];

        if (!$form_state->isValueEmpty('answer')) {
            //Single-choice answers' validation method.
            if (strcmp($entity->bundle(), 'single_choice') == 0) {
                if (strlen($answer) != 1) {
                    $form_state->setErrorByName('answer', t('The single choice answer is one letter in A,B,C,D.'));
                    return;
                }
                $answer = strtoupper($answer);
                if (strpos("ABCD", $answer) === false) {
                    $form_state->setErrorByName('answer', t('The single choice answer is one letter in A,B,C,D.'));
                    return;
                }
                $form_state->getValue('answer')[0]['value'] = $answer;
                return;
            }
            //Multiple-choice answers' validation method.
            if (strcmp($entity->bundle(), 'multiple_choice') == 0) {
                //Uppercase answers
                $answer = strtoupper($answer);
                //Get rid of non-word mark.
                $pattern = '/\W/i';
                $replacement = '';
                $answer = preg_replace($pattern, $replacement, $answer);
                //Sort answers alphabetically.
                $answer = str_split($answer, 1);
                sort($answer);
                //Get rid of repeated characters in answer.
                $answer = array_unique($answer);
                foreach($answer as $character){
                    if (strpos("ABCDE", $character) === false) {
                        $form_state->setErrorByName('answer', t('The multiple choice answers should be in A,B,C,D,E.'));
                        return;
                    }
                }
                $answer = join(',', $answer);
                $form_state->getValue('answer')[0]['value'] = $answer;
                return;
            }
        }
    }

}
