<?php

/**
 * @file
 * Contains \Drupal\iquiz\Entity\Question.
 */

namespace Drupal\iquiz\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\iquiz\QuestionInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Question entity.
 *
 * @ingroup question
 *
 * @ContentEntityType(
 *   id = "iquiz_question",
 *   label = @Translation("Question"),
 *   bundle_label = "[Question Type Label]",
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\iquiz\QuestionListBuilder",
 *     "views_data" = "Drupal\iquiz\QuestionViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\iquiz\Form\QuestionForm",
 *       "add" = "Drupal\iquiz\Form\QuestionForm",
 *       "edit" = "Drupal\iquiz\Form\QuestionForm",
 *       "delete" = "Drupal\iquiz\Form\QuestionDeleteForm",
 *     },
 *     "access" = "Drupal\iquiz\QuestionAccessControlHandler",
 *   },
 *   base_table = "iquiz_question",
 *   admin_permission = "administer question entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "question",
 *     "uuid" = "uuid",
 *     "bundle" = "type",
 *     "published" = "status",
 *   },
 *   bundle_entity_type = "iquiz_question_type",
 *   field_ui_base_route = "entity.iquiz_question_type.edit_form",
 *   permission_granularity = "bundle",
 *   links = {
 *     "canonical" = "/admin/iquiz/question/{iquiz_question}",
 *     "edit-form" = "/admin/iquiz/question/{iquiz_question}/edit",
 *     "delete-form" = "/admin/iquiz/question/{iquiz_question}/delete"
 *   }
 * )
 */
class Question extends ContentEntityBase implements QuestionInterface
{
    use EntityChangedTrait;
    use EntityPublishedTrait;

    /**
     * {@inheritdoc}
     */
    public static function preCreate(EntityStorageInterface $storage_controller, array &$values)
    {
        parent::preCreate($storage_controller, $values);
        $values += array(
            'user_id' => \Drupal::currentUser()->id(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedTime()
    {
        return $this->get('created')->value;
    }

    /**
     * {@inheritdoc}
     */
    public function getOwner()
    {
        return $this->get('user_id')->entity;
    }

    /**
     * {@inheritdoc}
     */
    public function getOwnerId()
    {
        return $this->get('user_id')->target_id;
    }

    /**
     * {@inheritdoc}
     */
    public function setOwnerId($uid)
    {
        $this->set('user_id', $uid);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setOwner(UserInterface $account)
    {
        $this->set('user_id', $account->id());
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->bundle();
    }


    /**
     * {@inheritdoc}
     */
    public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
    {
        $fields['id'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('ID'))
            ->setDescription(t('The ID of the Question entity.'))
            ->setReadOnly(TRUE);

        $fields['uuid'] = BaseFieldDefinition::create('uuid')
            ->setLabel(t('UUID'))
            ->setDescription(t('The UUID of the Question entity.'))
            ->setReadOnly(TRUE);


        $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('Authored by'))
            ->setDescription(t('The user ID of author of the Question entity.'))
            ->setRevisionable(TRUE)
            ->setSetting('target_type', 'user')
            ->setSetting('handler', 'default')
            ->setDefaultValueCallback('Drupal\node\Entity\Node::getCurrentUserId')
            ->setTranslatable(TRUE)
            ->setDisplayOptions('view', array(
                'label' => 'hidden',
                'type' => 'author',
                'weight' => 91,
            ))
            ->setDisplayOptions('form', array(
                'type' => 'entity_reference_autocomplete',
                'weight' => 91,
                'settings' => array(
                    'match_operator' => 'CONTAINS',
                    'size' => '60',
                    'autocomplete_type' => 'tags',
                    'placeholder' => '',
                ),
            ))
            ->setDisplayConfigurable('form', TRUE)
            ->setDisplayConfigurable('view', TRUE);

        $fields['type'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('Type'))
            ->setDescription(t('The question type.'))
            ->setSetting('target_type', 'iquiz_question_type')
            ->setReadOnly(TRUE);


        $fields['question'] = BaseFieldDefinition::create('string_long')
            ->setLabel(t('Question'))
            ->setDescription(t('The question that has to be answered.'))
            ->setSettings(array(
                'max_length' => 1024,
                'text_processing' => 0,
            ))
            ->setRequired(TRUE)
            ->setDefaultValue('')
            ->setDisplayOptions('view', array(
                'label' => 'above',
                'type' => 'string',
                'weight' => 1,
            ))
            ->setDisplayOptions('form', array(
                'type' => 'string_textarea',
                'weight' => 1,
            ))
            ->setDisplayConfigurable('form', TRUE)
            ->setDisplayConfigurable('view', TRUE);

        $fields['answer'] = BaseFieldDefinition::create('string_long')
            ->setLabel(t('Answer'))
            ->setDescription(t('The corect answer of question.'))
            ->setSettings(array(
                'max_length' => 1024,
                'text_processing' => 0,
            ))
            ->setRequired(TRUE)
            ->setDefaultValue('')
            ->setDisplayOptions('view', array(
                'label' => 'above',
                'type' => 'string',
                'weight' => 88,
            ))
            ->setDisplayOptions('form', array(
                'type' => 'string_textarea',
                'weight' => 88,
            ))
            ->setDisplayConfigurable('form', TRUE)
            ->setDisplayConfigurable('view', TRUE);

        $fields['score'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('Score'))
            ->setDescription(t('How many points the question is worth.'))
            ->setRequired(TRUE)
            ->setDefaultValue(1)
            ->addPropertyConstraints('value', ['Range' => ['min' => 0]])
            ->setDisplayOptions('view', array(
                'label' => 'above',
                'type' => 'string',
                'weight' => 89,
            ))
            ->setDisplayOptions('form', array(
                'type' => 'string_textfield',
                'weight' => 89,
            ));

        $fields['status'] = BaseFieldDefinition::create('boolean')
            ->setLabel(t('Independent status'))
            ->setRequired(TRUE)
            ->setDefaultValue(TRUE)
            ->setDisplayOptions('form', [
                'type' => 'boolean_checkbox',
                'settings' => [
                    'display_label' => TRUE,
                ],
                'weight' => 90,
            ])
            ->setDisplayConfigurable('form', TRUE);

        $fields['created'] = BaseFieldDefinition::create('created')
            ->setLabel(t('Created'))
            ->setDescription(t('The time that the entity was created.'));

        $fields['changed'] = BaseFieldDefinition::create('changed')
            ->setLabel(t('Changed'))
            ->setDescription(t('The time that the entity was last edited.'));

        return $fields;
    }

}
