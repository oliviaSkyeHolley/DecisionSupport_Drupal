<?php

declare(strict_types=1);

namespace Drupal\investigation_documents\Services\InvestigationDocumentsService;

use Drupal\investigation_documents\Entity\InvestigationDocuments;
use Drupal\file\Entity\File;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @todo Add class description.
 */
final class InvestigationDocumentsService implements InvestigationDocumentsServiceInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The logger service.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected LoggerInterface $logger;

  /**
   * Constructs a ProcessService object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, LoggerInterface $logger) {
    $this->entityTypeManager = $entity_type_manager;
    $this ->logger = $logger;
  }


  /**
   * {@inheritdoc}
   */
  public function getInvestigationDocuments($investigationId){

    // Create an entity query for the investigation_documents entity.
    $query = $this->entityTypeManager
      ->getStorage('investigation_documents') // Get the storage handler.
      ->getQuery() // Create the query.
      ->condition('status', 1) // Add condition for published documents.
      ->condition('investigationId', $investigationId) // Add condition for matching investigationId.
     ->accessCheck(TRUE); // Enable access checks.

    $investigationDocumentIds = $query->execute();
    $unformattedInvestigationDocuments = InvestigationDocuments::loadMultiple($investigationDocumentIds);
    $investigationDocumentList = array();

        foreach ($unformattedInvestigationDocuments as $unformattedInvestigationDocument) {
          if ($unformattedInvestigationDocument instanceof InvestigationDocuments) {
            $document['label'] = $unformattedInvestigationDocument->getLabel();
            $document['entityId'] = $unformattedInvestigationDocument->id();
            $document['stepId'] = $unformattedInvestigationDocument->getStepId();
            $document['fileEntityId'] = $unformattedInvestigationDocument->getFileId();
            $document['isVisible'] = $unformattedInvestigationDocument->getVisible();

            $investigationDocumentList[] = $document;
            unset($document);
          }
        }

        return $investigationDocumentList;
  }

  /**
   * {@inheritdoc}
   */
  public function createInvestigationDocument(array $data){
    // Validate required fields.
    if (empty($data['stepId']) || empty($data['investigationId']) || empty($data['fid'])) {
      throw new BadRequestHttpException('Missing required fields');
    }

    // Load the file entity using the provided fid.
    $file_entity = File::load($data['fid']);
    if (!$file_entity) {
      throw new NotFoundHttpException('File not found');
    }

    // Create new InvestigationDocuments entity.
    $investigation_document = InvestigationDocuments::create([
      'label' => $data['label'] ?? $file_entity->getFilename(),
      'notes' => $data['notes'] ?? '',
      'stepId' => $data['stepId'],
      'investigationId' => $data['investigationId'],
      'visible' => $data['visible'] ?? TRUE,
      'file' => [
        'target_id' => $file_entity->id(),
      ],
    ]);

    $entity = $investigation_document->save();

    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function deleteInvestigationDocument($fileId){

    $investigationDocument = InvestigationDocuments::load($fileId);

    if (!$investigationDocument) {
      throw new NotFoundHttpException();
    }
    $label = $investigationDocument -> getLabel();
    $newLabel = "$label - Archived";
    $investigationDocument->setLabel($newLabel);
    $investigationDocument->setVisible(false);
    $entity=$investigationDocument->save();

    return $entity;
  }
}
