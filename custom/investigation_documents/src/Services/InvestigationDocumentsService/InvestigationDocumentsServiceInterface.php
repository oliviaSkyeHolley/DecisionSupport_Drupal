<?php

declare(strict_types=1);

namespace Drupal\investigation_documents;

namespace Drupal\investigation_documents\Services\InvestigationDocumentsService;

use Drupal\investigation_documents\Entity\InvestigationDocuments;

/**
 * Interface for investigation document service.
 */
interface InvestigationDocumentsServiceInterface {

/**
 * Get a Investigation Documents by ID.
 *
 * @param int $investigationId
 *   The ID of the investigation document entity to retrieve.
 * @return InvestigationDocuments|null
 *   The Investigation Documents entity, or NULL if not found.
 */
public function getInvestigationDocuments($investigationId);

/**
 * Creates a new InvestigationDocuments entity.
 *
 * @param array $data
 *   The data for the new entity.
 *
 * @return InvestigationDocuments
 *   The created InvestigationDocuments entity.
 */
public function createInvestigationDocument(array $data);

  /**
   * Move a existing InvestigationDocuments entity to archived.
   *
   * @param $fileId
   *   The id of the existing entity.
   */
  public function deleteInvestigationDocument($fileId);

}
