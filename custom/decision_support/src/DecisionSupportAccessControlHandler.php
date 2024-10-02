<?php

declare(strict_types=1);

namespace Drupal\decision_support;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the decision support entity type.
 *
 * phpcs:disable Drupal.Arrays.Array.LongLineDeclaration
 *
 * @see https://www.drupal.org/project/coder/issues/3185082
 */
final class DecisionSupportAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account): AccessResult {
    if ($account->hasPermission($this->entityType->getAdminPermission())) {
      return AccessResult::allowed()->cachePerPermissions();
    }

    return match($operation) {
      'view' => AccessResult::allowedIfHasPermission($account, 'view decision_support'),
      'update' => AccessResult::allowedIfHasPermission($account, 'edit decision_support'),
      'delete' => AccessResult::allowedIfHasPermission($account, 'delete decision_support'),
      'delete revision' => AccessResult::allowedIfHasPermission($account, 'delete decision_support revision'),
      'view all revisions', 'view revision' => AccessResult::allowedIfHasPermissions($account, ['view decision_support revision', 'view decision_support']),
      'revert' => AccessResult::allowedIfHasPermissions($account, ['revert decision_support revision', 'edit decision_support']),
      default => AccessResult::neutral(),
    };
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL): AccessResult {
    return AccessResult::allowedIfHasPermissions($account, ['create decision_support', 'administer decision_support'], 'OR');
  }

}
