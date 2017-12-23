<?php

/**
 * @file
 * Contains \Drupal\iquiz\Entity\Quiz.
 */

namespace Drupal\iquiz\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\iquiz\ResultInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Quiz entity.
 *
 * @ingroup quiz
 *
 * @ContentEntityType(
 *   id = "iquiz_result",
 *   label = @Translation("Quiz result"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\iquiz\ResultListBuilder",
 *     "views_data" = "Drupal\iquiz\ResultViewsData",
 *     "form" = {
 *       "default" = "Drupal\iquiz\Form\ResultForm",
 *       "add" = "Drupal\iquiz\Form\ResultForm",
 *       "edit" = "Drupal\iquiz\Form\ResultForm",
 *       "delete" = "Drupal\iquiz\Form\ResultDeleteForm",
 *     },
 *     "access" = "Drupal\iquiz\ResultAccessControlHandler", 
 *   },
 *   base_table = "iquiz_result",
 *   admin_permission = "administer quiz result entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "id",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/iquiz/result/{iquiz_result}",
 *     "edit-form" = "/admin/iquiz/result/{iquiz_result}/edit",
 *     "delete-form" = "/admin/iquiz/result/{iquiz_result}/delete"
 *   }
 * )
 */
class Result extends ContentEntityBase implements ResultInterface {
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
      ->setDescription(t('The ID of the Quiz result entity.'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Quiz result entity.'))
      ->setReadOnly(TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Quiz result entity.'))
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

    $fields['is_evaluated']  = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Is evaluated'))
	  ->setDescription(t('Boolean indicating if this quiz requires manual grading and if it has been graded..'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => TRUE,
        ],
        'weight' => 90,
      ])
      ->setDisplayConfigurable('form', TRUE);
	  
    $fields['time_start'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Time start'))
      ->setDescription(t('Unix timestamp when this result started.'));
	  
    $fields['time_end'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Time end'))
      ->setDescription(t('Unix timestamp when this result ended.'));
	  
    $fields['quiz_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Quiz ID'))
      ->setDescription(t('The quiz ID of the result entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'iquiz_quiz')
      ->setSetting('handler', 'default')
      ->setDefaultValueCallback('Drupal\iquiz\Entity\QuestionInstance::getCurrentQuizId')
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
	  
    $fields['data'] = BaseFieldDefinition::create('map')
      ->setLabel(t('Data'))
      ->setDescription(t('A serialized array of user answers data.'));	  
    return $fields;
  }

}
