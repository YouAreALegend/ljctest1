<?php

namespace Drupal\iquiz\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\iquiz\AnswerInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Answer entity.
 *
 * @ingroup answer
 *
 * @ContentEntityType(
 *   id = "iquiz_answer",
 *   label = @Translation("Answer"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\iquiz\AnswerListBuilder",
 *     "views_data" = "Drupal\iquiz\AnswerViewsData",
 *   },
 *   base_table = "iquiz_answer",
 *   admin_permission = "administer answer entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "answer"
 *   }
 * )
 */
class Answer extends ContentEntityBase implements AnswerInterface {
  use EntityChangedTrait;
  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'uid' => \Drupal::currentUser()->id(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('uid')->target_id;
  }
  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the answer entity.'))
      ->setReadOnly(TRUE);
	  
    $fields['question_instance_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Question instance'))
      ->setSetting('target_type', 'iquiz_question_instance')
      ->setDisplayConfigurable('view', TRUE)
      ->setReadOnly(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ));
    $fields['result_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('quiz result'))
      ->setSetting('target_type', 'iquiz_result')
      ->setDisplayConfigurable('view', TRUE)
      ->setReadOnly(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ));	  

    $fields['is_evaluated']  = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Is evaluated'))
	  ->setDescription(t('Boolean indicating if it has been graded..'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => TRUE,
        ],
        'weight' => 90,
      ])
      ->setDisplayConfigurable('form', TRUE);


    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the answer entity.'))
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
      ->setDisplayConfigurable('view', TRUE);
    $fields['answer'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Answer'))
      ->setDescription(t('The coorect answer of question.'))
      ->setSettings(array(
        'max_length' => 4096,
        'text_processing' => 0,
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'text_textarea',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
	  
    $fields['score'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Score'))
      ->setDescription(t('How many points the answer is worth.'))
      ->setDefaultValue(0)
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

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
