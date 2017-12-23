<?php

/**
 * @file
 * Contains \Drupal\iquiz\Entity\Quiz.
 */

namespace Drupal\iquiz\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\iquiz\QuizInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Quiz entity.
 *
 * @ingroup quiz
 *
 * @ContentEntityType(
 *   id = "iquiz_quiz",
 *   label = @Translation("Quiz"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\iquiz\QuizListBuilder",
 *     "views_data" = "Drupal\iquiz\Entity\QuizViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\iquiz\Form\QuizForm",
 *       "add" = "Drupal\iquiz\Form\QuizForm",
 *       "edit" = "Drupal\iquiz\Form\QuizForm",
 *       "delete" = "Drupal\iquiz\Form\QuizDeleteForm",
 *     },
 *     "access" = "Drupal\iquiz\QuizAccessControlHandler",
 *   },
 *   base_table = "iquiz_quiz",
 *   admin_permission = "administer Quiz entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "bundle" = "type"
 *   },
 *   bundle_entity_type = "iquiz_quiz_type",
 *   links = {
 *     "canonical" = "/quiz/{iquiz_quiz}",
 *     "edit-form" = "/admin/iquiz/quiz/{iquiz_quiz}/edit",
 *     "delete-form" = "/admin/iquiz/quiz/{iquiz_quiz}/delete"
 *   },
 *   field_ui_base_route = "entity.iquiz_quiz_type.edit_form"
 * )
 */
class Quiz extends ContentEntityBase implements QuizInterface
{
    use EntityChangedTrait;

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
        $this->set('uid', $account->id());
        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
    {
        $fields['id'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('ID'))
            ->setDescription(t('The ID of the Quiz entity.'))
            ->setReadOnly(TRUE);

        $fields['uuid'] = BaseFieldDefinition::create('uuid')
            ->setLabel(t('UUID'))
            ->setDescription(t('The UUID of the Quiz entity.'))
            ->setReadOnly(TRUE);

        $fields['type'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('Type'))
            ->setDescription(t('The quiz type.'))
            ->setSetting('target_type', 'iquiz_quiz_type')
            ->setReadOnly(TRUE);

        $fields['uid'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('Authored by'))
            ->setDescription(t('The user ID of author of the Quiz entity.'))
            ->setRevisionable(TRUE)
            ->setSetting('target_type', 'user')
            ->setSetting('handler', 'default')
            ->setDefaultValueCallback('Drupal\node\Entity\Node::getCurrentUserId')
            ->setTranslatable(TRUE)
            ->setDisplayOptions('view', array(
                'label' => 'hidden',
                'type' => 'author',
                'weight' => 0,
            ))
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

        $fields['name'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Name'))
            ->setDescription(t('The name of the Quiz entity.'))
            ->setSettings(array(
                'max_length' => 50,
                'text_processing' => 0,
            ))
            ->setRequired(TRUE)
            ->setDefaultValue('')
            ->setDisplayOptions('view', array(
                'label' => 'above',
                'type' => 'string',
                'weight' => -4,
            ))
            ->setDisplayOptions('form', array(
                'type' => 'string_textfield',
                'weight' => -4,
            ))
            ->setDisplayConfigurable('form', TRUE)
            ->setDisplayConfigurable('view', TRUE);

        $fields['description'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Description'))
            ->setDescription(t('The description of this quiz'))
            ->setSettings(array(
                'max_length' => 256,
                'text_processing' => 0,
            ))
            ->setDefaultValue('')
            ->setDisplayOptions('view', array(
                'label' => 'above',
                'type' => 'string',
                'weight' => 0,
            ))
            ->setDisplayOptions('form', array(
                'type' => 'string_textfield',
                'weight' => 0,
            ))
            ->setDisplayConfigurable('form', TRUE)
            ->setDisplayConfigurable('view', TRUE);

        $fields['percent'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('Pass rate'))
            ->setDescription(t('The pass rate for this quiz in percents.'))
            ->setRequired(TRUE)
            ->setDefaultValue(50)
            ->addPropertyConstraints('value', ['Range' => ['min' => 0, 'max' => 100]])
            ->setDisplayOptions('view', array(
                'label' => 'above',
                'type' => 'string',
                'weight' => 1,
            ))
            ->setDisplayOptions('form', array(
                'type' => 'string_textfield',
                'weight' => 1,
            ));

        $fields['time'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('Quiz time (seconds)'))
            ->setDescription(t('The number of seconds the user has to complete the quiz after starting it. Set to 0 for no time limit.'))
            ->setRequired(TRUE)
            ->setDefaultValue(0)
            ->addPropertyConstraints('value', ['Range' => ['min' => 0]])
            ->setDisplayOptions('view', array(
                'label' => 'above',
                'type' => 'string',
                'weight' => 1,
            ))
            ->setDisplayOptions('form', array(
                'type' => 'string_textfield',
                'weight' => 1,
            ));

        $fields['attempts'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('Number of attempts allowed'))
            ->setDescription(t('The number a time an user is allowed to attempt this quiz. Set to 0 for unlimited attempts.'))
            ->setRequired(TRUE)
            ->setDefaultValue(0)
            ->addPropertyConstraints('value', ['Range' => ['min' => 0]])
            ->setDisplayOptions('view', array(
                'label' => 'above',
                'type' => 'string',
                'weight' => 1,
            ))
            ->setDisplayOptions('form', array(
                'type' => 'string_textfield',
                'weight' => 1,
            ));

        $fields['status'] = BaseFieldDefinition::create('boolean')
            ->setLabel(t('Published'))
            ->setDisplayOptions('form', [
                'type' => 'boolean_checkbox',
                'settings' => [
                    'display_label' => TRUE,
                ],
                'weight' => 90,
            ])
            ->setDisplayConfigurable('form', TRUE);
        /*
        $state_values = [
          0 => '考试中',
          1 => '考试结束',
          2 => '评分中',
          3 => '完成'
        ];
        $fields['state'] = BaseFieldDefinition::create('list_integer')
          ->setDisplayOptions('form', array(
            'type' => 'options_buttons',
            'weight' => -4,
          ))
          ->setSetting('allowed_values', $state_values)
          ->setDisplayConfigurable('form', TRUE);
        */

        $fields['created'] = BaseFieldDefinition::create('created')
            ->setLabel(t('Created'))
            ->setDescription(t('The time that the entity was created.'));

        $fields['changed'] = BaseFieldDefinition::create('changed')
            ->setLabel(t('Changed'))
            ->setDescription(t('The time that the entity was last edited.'));

        return $fields;
    }
}
