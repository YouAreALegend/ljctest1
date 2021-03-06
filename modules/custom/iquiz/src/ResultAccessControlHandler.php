<?php

namespace Drupal\iquiz;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Question instance entity.
 *
 * @see \Drupal\iquiz\Entity\QuestionInstance.
 */
class ResultAccessControlHandler extends EntityAccessControlHandler {
  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {

    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view quiz result entity');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit quiz result entity');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete quiz result entity');
    }

    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add quiz result entity');
  }

}
