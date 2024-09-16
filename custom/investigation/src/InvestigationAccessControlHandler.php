<?php

declare(strict_types=1);

namespace Drupal\investigation;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the investigation entity type.
 *
 * phpcs:disable Drupal.Arrays.Array.LongLineDeclaration
 *
 * @see https://www.drupal.org/project/coder/issues/3185082
 */
final class InvestigationAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account): AccessResult {
    if ($account->hasPermission($this->entityType->getAdminPermission())) {
      return AccessResult::allowed()->cachePerPermissions();
    }

    return match($operation) {
      'view' => AccessResult::allowedIfHasPermission($account, 'view investigation'),
      'update' => AccessResult::allowedIfHasPermission($account, 'edit investigation'),
      'delete' => AccessResult::allowedIfHasPermission($account, 'delete investigation'),
      'delete revision' => AccessResult::allowedIfHasPermission($account, 'delete investigation revision'),
      'view all revisions', 'view revision' => AccessResult::allowedIfHasPermissions($account, ['view investigation revision', 'view investigation']),
      'revert' => AccessResult::allowedIfHasPermissions($account, ['revert investigation revision', 'edit investigation']),
      default => AccessResult::neutral(),
    };
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL): AccessResult {
    return AccessResult::allowedIfHasPermissions($account, ['create investigation', 'administer investigation'], 'OR');
  }

}
