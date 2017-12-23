<?php

/**
 * @file
 * Contains \Drupal\iquiz\Entity\QuestionInstance.
 */

namespace Drupal\iquiz\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\iquiz\QuestionInstanceInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Question entity.
 *
 * @ingroup question
 *
 * @ContentEntityType(
 *   id = "iquiz_question_instance",
 *   label = @Translation("Question Instance"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\iquiz\QuestionInstanceListBuilder",
 *     "views_data" = "Drupal\iquiz\QuestionInstanceViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\iquiz\Form\QuestionInstanceForm",
 *       "add" = "Drupal\iquiz\Form\QuestionInstanceForm",
 *       "edit" = "Drupal\iquiz\Form\QuestionInstanceForm",
 *       "delete" = "Drupal\iquiz\Form\QuestionInstanceDeleteForm",
 *     },
 *     "access" = "Drupal\iquiz\QuestionInstanceAccessControlHandler",
 *   },
 *   base_table = "iquiz_question_instance",
 *   admin_permission = "administer question entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "id",
 *   },
 *   links = {
 *     "canonical" = "/admin/iquiz/question-instance/{question}",
 *     "edit-form" = "/admin/iquiz/question-instance/{question}/edit",
 *     "delete-form" = "/admin/iquiz/question-instance/{question}/delete"
 *   }
 * )
 */
class QuestionInstance extends ContentEntityBase implements QuestionInstanceInterface
{

    /**
     * {@inheritdoc}
     */
    public static function preCreate(EntityStorageInterface $storage_controller, array &$values)
    {
        parent::preCreate($storage_controller, $values);
    }

    /**
     * {@inheritdoc}
     */
    public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
    {
        $fields['id'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('ID'))
            ->setDescription(t('The ID of the Question instance entity.'))
            ->setReadOnly(TRUE);

        $fields['quiz_id'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('Quiz ID'))
            ->setDescription(t('The quiz ID of the Question instance entity.'))
            ->setRevisionable(TRUE)
            ->setSetting('target_type', 'iquiz_quiz')
            ->setSetting('handler', 'default')
//            ->setDefaultValueCallback('Drupal\iquiz\Entity\QuestionInstance::getCurrentQuizId')
            ->setTranslatable(TRUE)
            ->setDisplayOptions('form', array(
                'type' => 'entity_reference_autocomplete',
                'weight' => 5,
                'settings' => array(
                    'match_operator' => 'CONTAINS',
                    'size' => '60',
                    'autocomplete_type' => 'tags',
                    'placeholder' => '',
                ),
            ))
            ->setDisplayConfigurable('form', TRUE)
            ->setDisplayConfigurable('view', TRUE);

        $fields['question_id'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('Question ID'))
            ->setDescription(t('The question ID of the question instance entity.'))
            ->setRevisionable(TRUE)
            ->setSetting('target_type', 'iquiz_question')
            ->setSetting('handler', 'default')
//            ->setDefaultValueCallback('Drupal\iquiz\Entity\QuestionInstance::getCurrentQuestionId')
            ->setTranslatable(TRUE)
            ->setDisplayOptions('form', array(
                'type' => 'entity_reference_autocomplete',
                'weight' => 5,
                'settings' => array(
                    'match_operator' => 'CONTAINS',
                    'size' => '60',
                    'autocomplete_type' => 'tags',
                    'placeholder' => '',
                ),
            ))
            ->setDisplayConfigurable('form', TRUE)
            ->setDisplayConfigurable('view', TRUE);

        $fields['score'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('Score'))
            ->setDescription(t('How many points the question is worth.'))
            ->setDefaultValue(1)
            ->addPropertyConstraints('value', ['Range' => ['min' => 0]])
            ->setDisplayOptions('view', array(
                'label' => 'above',
                'type' => 'string',
                'weight' => -4,
            ))
            ->setDisplayOptions('form', array(
                'type' => 'string_textfield',
                'weight' => -4,
            ));

        $fields['question_number'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Question Number'))
            ->setDescription(t('The question number that to be used in the quiz.'))
            ->setSettings(array(
                'max_length' => 32,
                'text_processing' => 0,
            ))
            ->setDefaultValue('')
            ->setDisplayOptions('view', array(
                'label' => 'above',
                'type' => 'string',
                'weight' => -4,
            ))
            ->setDisplayOptions('form', array(
                'type' => 'text_textfield',
                'weight' => -4,
            ))
            ->setDisplayConfigurable('form', TRUE)
            ->setDisplayConfigurable('view', TRUE);

        $fields['weight'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('Weight'))
            ->setDescription(t('The relative weight of the question in the quiz.'))
            ->setDefaultValue(0)
            ->setDisplayOptions('view', array(
                'label' => 'above',
                'type' => 'string',
                'weight' => -3,
            ))
            ->setDisplayOptions('form', array(
                'type' => 'string_textfield',
                'weight' => -3,
            ));
        $fields['parent_id'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('Parent instance ID'))
            ->setDescription(t('The parent question instance ID.'))
            ->setDefaultValue(0)
            ->setDisplayOptions('view', array(
                'label' => 'above',
                'type' => 'string',
                'weight' => -2,
            ))
            ->setDisplayOptions('form', array(
                'type' => 'string_textfield',
                'weight' => -2,
            ));

        return $fields;
    }
}
