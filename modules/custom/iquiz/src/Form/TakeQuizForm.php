<?php

namespace Drupal\iquiz\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\iquiz\Entity\QuestionInstance;
use Drupal\iquiz\QuestionInstanceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class TakeQuizForm.
 */
class TakeQuizForm extends FormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'take_quiz_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, $quiz_id = NULL, $random = NULL)
    {
        $form['#attached']['library'][] = 'iquiz/iquiz.jQuery';
        $index = 1;
        $isShuffled = 0;

        if (!$random && rand(0, 1)) {
            $questionInstances = $this->shuffleQuestion();
            $isShuffled = 1;
        } else {
            $questionInstanceIds = \Drupal::entityQuery('iquiz_question_instance')
                ->condition('quiz_id', $quiz_id)
                ->execute();
            $questionInstances = \Drupal::entityTypeManager()
                ->getStorage('iquiz_question_instance')
                ->loadMultiple($questionInstanceIds);
            usort($questionInstances, [$this, 'resortQuestionInstance']);
        }
        foreach ($questionInstances as $questionInstance) {
            $question = \Drupal::entityTypeManager()
                ->getStorage('iquiz_question')
                ->load($questionInstance->get('question_id')->target_id);

            $questionPluginManager = \Drupal::service('plugin.manager.iquiz.question_type_plugin');
            $questionPluginInstance = $questionPluginManager->createInstance($question->get('type')->target_id);
            $questionPluginInstance->takeQuizForm($form, $form_state, $questionInstance, $question, $index, $isShuffled);

            $index++;
        }
        $quiz = \Drupal::entityTypeManager()->getStorage('iquiz_quiz')->load($quiz_id);

        $form['quizId'] = [
            '#type' => 'hidden',
            '#value' => $quiz_id,
        ];
        $form['takeQuizCountdown'] = [
            '#type' => 'hidden',
            '#value' => $quiz->get('time')->value,
            '#default_value' => '0',
            '#prefix' => "<div id='takeQuizCountdownDiv'>",
            '#suffix' => '</div>',
        ];
        $form['takeQuizStartTime'] = [
            '#type' => 'hidden',
            '#default_value' => '0',
            '#prefix' => "<div id='takeQuizStartTimeDiv'>",
            '#suffix' => '</div>',
        ];
        $form['takeQuizEndTime'] = [
            '#type' => 'hidden',
            '#default_value' => '0',
            '#prefix' => "<div id='takeQuizEndTimeDiv'>",
            '#suffix' => '</div>',
        ];
        $form['takeQuizSubmitButton'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
            '#prefix' => "<div id='takeQuizSubmitButtonDiv'>",
            '#suffix' => '</div>',
            '#weight' => $index,
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        //Save result of a quiz.
        $quiz_result = \Drupal::entityTypeManager()->getStorage('iquiz_result')->create([]);
        $quiz_result->uid->target_id = \Drupal::currentUser()->id();
        $quiz_result->set("time_start", \Drupal::request()->request->get('takeQuizStartTime'));
        $quiz_result->set("time_end", \Drupal::request()->request->get('takeQuizEndTime'));
        $quiz_result->set("quiz_id", \Drupal::request()->request->get('quizId'));
        $quiz_result->set("score", 0);
        $quiz_result->save();

        //Get current quiz's question instances' id to load themselves.
        $questionInstanceIds = \Drupal::entityQuery('iquiz_question_instance')
            ->condition('quiz_id', \Drupal::request()->request->get('quizId'))
            ->execute();
        $questionInstances = \Drupal::entityTypeManager()
            ->getStorage('iquiz_question_instance')
            ->loadMultiple($questionInstanceIds);

        $isEvaluated = True;
        $sumScore = 0;
        foreach ($questionInstances as $questionInstance) {
            //Answer of the current question which submitted by user.
            $submittedAnswer = \Drupal::request()->request->get($questionInstance->id());

            //Create the answer entity which to save data submitted by user.
            $quiz_answer = \Drupal::entityTypeManager()
                ->getStorage('iquiz_answer')
                ->create([]);
            $quiz_answer->question_instance_id->target_id = $questionInstance->id();
            $quiz_answer->result_id->target_id = $quiz_result->id();
            $quiz_answer->uid->target_id = \Drupal::currentUser()->id();
            $quiz_answer->set("created", \Drupal::time()->getCurrentTime() / 1000);
            $quiz_answer->set("changed", \Drupal::time()->getCurrentTime() / 1000);

            //Get the question entity which stored correct answers and scores by admin previously.
            $question = \Drupal::entityTypeManager()
                ->getStorage('iquiz_question')
                ->load($questionInstance->get('question_id')->target_id);
            $correctAnswer = $question->get('answer')->value;
            $score = $questionInstance->get('score')->value;

            //Question answer's evaluation and save.
            $questionPluginManager = \Drupal::service('plugin.manager.iquiz.question_type_plugin');
            $questionPluginInstance = $questionPluginManager->createInstance($question->get('type')->target_id);
            $questionPluginInstance->takeQuizFormSubmit($quiz_answer, $submittedAnswer, $correctAnswer, $score, $sumScore, $isEvaluated);
            if ($isEvaluated && !$questionPluginInstance->isAutoEvaluate()) {
                $isEvaluated = false;
            }
        }

        //If there are questions all can be evaluated automatically set state to finished(state's value set to 3)
        //state value:{0:answering question;1:submitted question;2:evaluating result;3:quiz finished}
        if ($isEvaluated) {
            $quiz_result->set("state", 3);
            $quiz_result->set("is_evaluated", 1);
        } else {
            $quiz_result->set("state", 1);
            $quiz_result->set("is_evaluated", 0);
        }
        $quiz_result->set('score', $sumScore);
        $quiz_result->save();
        $form_state->setRedirect('<front>');
    }

    //根据questionInstance的weight属性比较的方式，对questionInstances的数组进行自定义排序
    public function resortQuestionInstance(QuestionInstanceInterface $qi1, QuestionInstanceInterface $qi2)
    {
        if ($qi1->get('weight')->value == $qi2->get('weight')->value) {
            dpm($qi1->id().','.$qi2);
            return 0;
        }
        return $qi1->get('weight')->value > $qi2->get('weight')->value ? 1 : -1;
    }

    public function shuffleQuestion()
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR . 'ShuffledPaper.xml';
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
}
