<?php

declare(strict_types=1);

namespace Drupal\investigation\Services\InvestigationService;

use Drupal\investigation\Entity\Investigation;


/**
 * Interface for investigation service.
 */
interface InvestigationServiceInterface {

 /**
   * Loads an Investigation entity.
   *
   * @return Investigation|null
   *   The Investigation entity, or NULL if not found.
   */
  public function getInvestigationList();

/**
 * Get a Investigation by ID.
 *
 * @param int $investigationId
 *   The ID of the investigation entity to retrieve.
 *
 * @return string
 *   The JSON representation of the Investigation entity.
 */
  public function getInvestigation($investigationId);

  /**
   * Creates a new Investigation entity.
   *
   * @param array $data
   *   The data for the new entity.
   *
   * @return Investigation
   *   The created Investigation entity.
   */
  public function createInvestigation(array $data);

  /**
   * Updates a Investigation entity.
   *
   * @param array $data
   *   The data of the entity.
   *
   * @param $investigationId
   *   The id of the existing entity.
   *
   * @return Investigation
   *   The updated Investigation entity.
   */
  public function updateInvestigation($investigationId, array $data);


  /**
   * Move a existing Investigation entity to archived.
   *
   * @param $investigationId
   *   The id of the existing entity.
   */
  public function deleteInvestigation($investigationId);


}
