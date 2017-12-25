<?php

/**
 * @file
 * Contains \Drupal\iquiz\Controller\QuizController.
 */

namespace Drupal\iquiz\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\Entity;
use Drupal\iquiz\QuestionTypeInterface;
use Drupal\iquiz\QuizTypeInterface;
use Drupal\iquiz\QuizInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Form;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * Class QuizController.
 *
 * @package Drupal\iquiz\Controller
 */
class IQuizController extends ControllerBase implements ContainerInjectionInterface
{

    /**
     * The renderer service.
     *
     * @var \Drupal\Core\Render\RendererInterface
     */
    protected $renderer;

    /**
     * @param \Drupal\Core\Render\RendererInterface $renderer
     */
    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('renderer')
        );
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addQuestionPage()
    {
        $build = [
            '#theme' => 'question_add_list',
            '#cache' => [
                'tags' => $this->entityTypeManager()
                    ->getDefinition('iquiz_question_type')
                    ->getListCacheTags(),
            ],
        ];

        $content = [];

        // Only use iquiz_question_type the user has access to.
        foreach ($this->entityTypeManager()
                     ->getStorage('iquiz_question_type')
                     ->loadMultiple() as $type) {
            $access = $this->entityTypeManager()
                ->getAccessControlHandler('iquiz_question_type')
                ->createAccess($type->id(), NULL, [], TRUE);
            if ($access->isAllowed()) {
                $content[$type->id()] = $type;
            }
            $this->renderer->addCacheableDependency($build, $access);
        }

        // Bypass the iquiz_question/add listing if only one content type is available.
        if (count($content) == 1) {
            $type = array_shift($content);
            return $this->redirect('iquiz_question.add', ['iquiz_question_type' => $type->id()]);
        }

        $build['#content'] = $content;

        return $build;
    }

    public function addQuestion(QuestionTypeInterface $iquiz_question_type)
    {
        $iquiz_question = $this->entityTypeManager()
            ->getStorage('iquiz_question')
            ->create([
                'type' => $iquiz_question_type->id(),
            ]);

        $form = $this->entityFormBuilder()->getForm($iquiz_question);

        return $form;
    }

    public function addQuizPage()
    {
        $build = [
            '#theme' => 'quiz_add_list',
            '#cache' => [
                'tags' => $this->entityTypeManager()
                    ->getDefinition('iquiz_quiz_type')
                    ->getListCacheTags(),
            ],
        ];

        $content = [];

        // Only use iquiz_quiz_type the user has access to.
        foreach ($this->entityTypeManager()
                     ->getStorage('iquiz_quiz_type')
                     ->loadMultiple() as $type) {
            $access = $this->entityTypeManager()
                ->getAccessControlHandler('iquiz_quiz_type')
                ->createAccess($type->id(), NULL, [], TRUE);
            if ($access->isAllowed()) {
                $content[$type->id()] = $type;
            }
            $this->renderer->addCacheableDependency($build, $access);
        }

        // Bypass the iquiz_question/add listing if only one content type is available.
        if (count($content) == 1) {
            $type = array_shift($content);
            return $this->redirect('iquiz.quiz.add', ['iquiz_quiz_type' => $type->id()]);
        }

        $build['#content'] = $content;

        return $build;
    }

    public function addQuiz(QuizTypeInterface $iquiz_quiz_type)
    {
        $iquiz_quiz = $this->entityTypeManager()->getStorage('iquiz_quiz')->create([
            'type' => $iquiz_quiz_type->id(),
        ]);

        $form = $this->entityFormBuilder()->getForm($iquiz_quiz);

        return $form;
    }

    public function manageQuizQuestionsPage(QuizInterface $iquiz_quiz)
    {
        //First part ：add question links
        $items = [];
        foreach ($this->entityTypeManager()
                     ->getStorage('iquiz_question_type')
                     ->loadMultiple() as $type) {
            $add_question_link = Link::fromTextAndUrl(
                $type->label(),
                new Url(
                    'iquiz_question.add',
                    ['iquiz_question_type' => $type->id()], ['query' => array('quiz_id' => $iquiz_quiz->id())]
                )
            );

            $items[] = [
                '#markup' => '' . $add_question_link->toString(),
                '#wrapper_attributes' => [
                    'class' => ['custom-item-class'],
                ],
            ];
        }
        $build['add_links'] = [
            '#theme' => 'item_list',
            '#items' => $items,
        ];


        //Second part ：question instance list
        $header = [
            'question' => t('Question'),
            'score' => t('Score'),
            'weight' => t('Weight'),
            'question_number' => t('Question number'),
            'edit' => t('Edit'),
        ];
        $rows = [];
        $question_instance_ids = \Drupal::entityQuery('iquiz_question_instance')
            ->condition('quiz_id', $iquiz_quiz->id())
            ->execute();
        $iquiz_question_instances = $this->entityTypeManager()
            ->getStorage('iquiz_question_instance')
            ->loadMultiple($question_instance_ids);
        foreach ($iquiz_question_instances as $iquiz_question_instance) {
            $edit_link = Link::fromTextAndUrl(t('Edit'), new Url('entity.iquiz_question_instance.edit_form', ['iquiz_question_instance' => $iquiz_question_instance->id()]));


            $rows[] = [
                'question' => $iquiz_question_instance->question_id->entity->question->value,
                'score' => $iquiz_question_instance->score->value,
                'weight' => $iquiz_question_instance->weight->value,
                'question_number' => $iquiz_question_instance->question_number->value,
                'edit' => $edit_link->toString(),
            ];
        }

        $build['question_instances'] = [
            '#type' => 'table',
            '#header' => $header,
            '#rows' => $rows,
            '#attributes' => [
                'id' => 'question-instances-table',
            ],
        ];

        return $build;
    }

    //Output taking quiz page.Including submit result and answer data to their table.
    public function takeQuiz($quiz_id, $quiz_type)
    {
        $build['form'] = \Drupal::formBuilder()
            ->getForm('\Drupal\iquiz\Form\TakeQuizForm', $quiz_id, $quiz_type);
        return $build;
    }

    public function getQuizTitle($quiz_id)
    {
        $current_path = \Drupal::service('path.current')->getPath();
        $url_object = \Drupal::service('path.validator')
            ->getUrlIfValid($current_path);
        $route_name = $url_object->getRouteName();
        if (strcmp($route_name, "iquiz.take_quiz") == 0) {
            return \Drupal::entityTypeManager()
                ->getStorage('iquiz_quiz')
                ->load($quiz_id)
                ->get('name')
                ->value;
        }
        if (strcmp($route_name, "iquiz.quiz_results") == 0) {
            $quizName = \Drupal::entityTypeManager()
                ->getStorage('iquiz_quiz')
                ->load($quiz_id)
                ->get('name')
                ->value;
            return $quizName . $this->t("Answered paper results page.");
        }
    }

    //Show a quiz's results list.
    public function getResultsList($quiz_id)
    {
        //First part ：Results for which finished evaluating.
        $caption_finished = "Quiz Results.";
        $header_finished = [
            'quizResultInfo' => t('User'),
            'quizResultState' => t('Scores'),
            'evaluateButton' => t('Operation'),
        ];
        $rows_finished = [];

        //Second part ：Results for which not finished yet.
        $caption = "Results wait to operate.";
        $header = [
            'quizResultInfo' => t('User'),
            'quizResultState' => t('State'),
            'evaluateButton' => t('Operation'),
        ];
        $rows = [];

        $arrRids = \Drupal::entityQuery('iquiz_result')
            ->condition('quiz_id', $quiz_id)
            ->execute();
        $arrResults = \Drupal::entityTypeManager()
            ->getStorage('iquiz_result')
            ->loadMultiple($arrRids);
        foreach ($arrResults as $quizResult) {
            //Result id linked to page for giving points to unevaluated answers.
            $userId = $quizResult->get('uid')->target_id;
            $quizResultInfo = $this->t($quizResult->label() . ".User id:" . $userId);

            //Operation links.
            $url = new Url('iquiz.evaluate_result', ['result_id' => $quizResult->id()]);
            //Result state.
            $numQuizResultState = $quizResult->get('state')->value;
            $quizResultState = "";
            switch ($numQuizResultState) {
                case 1:
                    $quizResultState = $this->t("Exam is over.But answers need to be evaluated.");
                case 2:
                    //Evaluate button linked to page for giving points to unevaluated answers.
                    //TODO Change with button style.
                    $evaluateButton = Link::fromTextAndUrl($this->t("Evaluate"), $url);
                    $rows[] = [
                        'quizResultInfo' => $quizResultInfo,
                        'quizResultState' => (strcmp($quizResultState, "") != 0 ? $quizResultState : $this->t("Exam answers are evaluating.")) . $this->t('Score:') . $quizResult->get('score')->value,
                        'evaluateButton' => $evaluateButton,
                    ];
                    break;
                case 3:
                    //Evaluate button linked to page for giving points to unevaluated answers.
                    //TODO Change with button style.
                    $evaluateButton = Link::fromTextAndUrl($this->t("Edit"), $url);
                    $quizResultState = $this->t("Score:");
                    $rows_finished[] = [
                        'quizResultInfo' => $quizResultInfo,
                        'quizResultState' => $quizResultState . $quizResult->get('score')->value,
                        'evaluateButton' => $evaluateButton,
                    ];
                    break;
            }

        }
        //First part ：Results for which finished evaluating.
        $build['iquiz_result_finished'] = [
            '#type' => 'table',
            '#caption' => $caption_finished,
            '#header' => $header_finished,
            '#rows' => $rows_finished,
            '#attributes' => [
                'id' => 'iquiz-result-finished-table',
            ],
        ];
        //Second part ：Results for which not finished yet.
        $build['iquiz_result'] = [
            '#type' => 'table',
            '#caption' => $caption,
            '#header' => $header,
            '#rows' => $rows,
            '#attributes' => [
                'id' => 'iquiz-result-table',
            ],
        ];
        return $build;
    }

    public function evaluateResult($result_id)
    {
        $build['form'] = \Drupal::formBuilder()
            ->getForm('\Drupal\iquiz\Form\ResultEvaluationForm', $result_id);
        return $build;
    }

    public function generateRandomQuiz()
    {
        $quizes = \Drupal::entityTypeManager()
            ->getStorage('iquiz_quiz')
            ->loadMultiple();

        if (count($quizes) != 0) {
            $random_id = array_rand($quizes, 1);
            $random_qid = $quizes[$random_id]->id();

            $questionInstanceIds = \Drupal::entityQuery('iquiz_question_instance')
                ->condition('quiz_id', $random_qid)
                ->execute();
            $questionInstances = \Drupal::entityTypeManager()
                ->getStorage('iquiz_question_instance')
                ->loadMultiple($questionInstanceIds);

            $shuffleGroup = [];
            foreach ($questionInstances as $key=>$questionInstance) {
                $qid = $questionInstance->get('question_id')->target_id;
                $question = \Drupal::entityTypeManager()->getStorage('iquiz_question')->load($qid);
                if(!array_key_exists($question->bundle(),$shuffleGroup)){
                    $shuffleGroup[$question->bundle()] = [];
                }
                array_push($shuffleGroup[$question->bundle()], $key);
            }
            foreach ($shuffleGroup as $group) {
                $array = [];
                foreach($group as $index){
                    array_push($array,$questionInstances[$index]);
                }
                shuffle($array);
                foreach($group as $key=>$index){
                    $questionInstances[$index] = $array[$key];
                }
            }

            try {
                $path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR . 'ShuffledPaper.xml';
                $xml = simplexml_load_file($path);
                $xml->Id[0] = $random_qid;
                $xml->QuestionSequence[0] = null;
                foreach ($questionInstances as $key => $shuffledQInstance) {
                    $xml->QuestionSequence[0]->addChild("qid", $shuffledQInstance->id());
                }
                $xml->asXML($path);
            } catch (FileNotFoundException $e) {
                drupal_set_message($e);
            } finally {
                drupal_set_message($this->t("Quiz Generated Successfully."));
                return $this->redirect('iquiz.admin_iquiz');
            }
        } else {
            drupal_set_message($this->t("There are no quiz generated yet."));
            return $this->redirect('iquiz.admin_iquiz');
        }
    }

    public function getRandomQuiz()
    {
        $quizes = \Drupal::entityTypeManager()
            ->getStorage('iquiz_quiz')
            ->loadMultiple();
        $random_id = array_rand($quizes, 1);
        $random_qid = $quizes[$random_id]->id();
        return $this->redirect(
            'iquiz.take_quiz',
            [
                'quiz_id' => $random_qid,
                'quiz_type' => 'random_quiz',
            ]);
    }

    public function getShuffledPaper()
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR . 'ShuffledPaper.xml';
        $xml = simplexml_load_file($path);
        if ($xml->Id[0] != null && $xml->Id[0] != "") {
            $random_qid = $xml->Id[0];
            return $this->redirect(
                'iquiz.take_quiz',
                [
                    'quiz_id' => $random_qid,
                    'quiz_type' => 'shuffled_paper_quiz',
                ]);
        }
        drupal_set_message($this->t("There are no quiz generated yet."));
        return $this->redirect('<front>');
    }

}