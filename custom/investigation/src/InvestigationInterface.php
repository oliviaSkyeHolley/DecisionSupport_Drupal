<?php

declare(strict_types=1);

namespace Drupal\investigation;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining an investigation entity type.
 */
interface InvestigationInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
