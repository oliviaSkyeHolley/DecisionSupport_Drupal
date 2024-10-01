<?php

declare(strict_types=1);

namespace Drupal\ReportGenerator;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a ReportGenerator entity type.
 */
interface ProcessInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
