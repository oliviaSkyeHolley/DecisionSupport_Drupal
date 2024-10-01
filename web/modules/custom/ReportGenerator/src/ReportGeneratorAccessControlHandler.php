<?php

declare(strict_types=1);

namespace Drupal\ReportGenerator;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the report-generator entity type.
 *
 * phpcs:disable Drupal.Arrays.Array.LongLineDeclaration
 *
 * @see https://www.drupal.org/project/coder/issues/3185082
 */
final class ReportGeneratorAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account): AccessResult {
    if ($account->hasPermission($this->entityType->getAdminPermission())) {
      return AccessResult::allowed()->cachePerPermissions();
    }

    return match($operation) {
      'view' => AccessResult::allowedIfHasPermission($account, 'view ReportGenerator'),
      'update' => AccessResult::allowedIfHasPermission($account, 'edit ReportGenerator'),
      'delete' => AccessResult::allowedIfHasPermission($account, 'delete ReportGenerator'),
      'delete revision' => AccessResult::allowedIfHasPermission($account, 'delete ReportGenerator revision'),
      'view all revisions', 'view revision' => AccessResult::allowedIfHasPermissions($account, ['view ReportGenerator revision', 'view ReportGenerator']),
      'revert' => AccessResult::allowedIfHasPermissions($account, ['revert ReportGenerator revision', 'edit ReportGenerator']),
      default => AccessResult::neutral(),
    };
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL): AccessResult {
    return AccessResult::allowedIfHasPermissions($account, ['create ReportGenerator', 'administer ReportGenerator'], 'OR');
  }

}
