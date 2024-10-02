<?php

declare(strict_types=1);

namespace Drupal\investigation\Services\InvestigationService;

use Drupal\investigation\Entity\Investigation;
use Drupal\process\Entity\Process;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @todo Add class description.
 */
final class InvestigationService implements InvestigationServiceInterface {

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
   * Constructs a InvestigationService object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, LoggerInterface $logger) {
    $this->entityTypeManager = $entity_type_manager;
    $this ->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public function getInvestigationList() {

    $unformattedInvestigation = Investigation::loadMultiple();
    $investigationList = array();
    foreach ($unformattedInvestigation as $unformattedInvestigation) {
      if ($unformattedInvestigation instanceof Investigation) {
        $investigation['label'] = $unformattedInvestigation->getName();
        $investigation['entityId'] = $unformattedInvestigation->id();
        $investigation['revisionId'] = $unformattedInvestigation->getRevisionId();
        $investigation['createdTime'] = $unformattedInvestigation->getCreatedTime();
        $investigation['updatedTime'] = $unformattedInvestigation->getupdatedTime();
        $investigation['revisionStatus'] = $unformattedInvestigation->getRevisionStatus();
        $investigation['json_string'] = $unformattedInvestigation->getJsonString();

        $investigationList[] = $investigation;
        unset($investigation);
      }
    }

    return $investigationList;
  }

  /**
   * {@inheritdoc}
   */
  public function getInvestigation($investigationId) {

    $investigation = Investigation::load($investigationId);
    if (!$investigation) {
      throw new NotFoundHttpException(sprintf('Investigation with ID %s was not found.', $investigationId));
    }
    $investigationJsonString = $investigation->getJsonString();

    return $investigationJsonString;
  }

  /**
   * {@inheritdoc}
   */
  public function createInvestigation(array $data) {

    $processId = $data['process_id'];
    $process = Process::load($processId);
    $processJson = $process->getJsonString();
    $processData = json_decode($processJson, true);

    $investigation = Investigation::create($data);
    $entityId = $investigation->save();
    $returnValue['entityId'] = $investigation->id();
    $jsonstring = [
      'entityId' =>$investigation->id(),
      'uuid'=>uniqid(),
      'investigationLabel' =>$investigation->label(),
      'processId' =>$data['investigation_id'],
      'processLabel' => $investigation->getName(),
      'steps'=> $processData['steps'],
    ];
    $investigationJsonstring = json_encode($jsonstring);
    $investigation->setJsonString($investigationJsonstring);
    $entity=$investigation->save();

    // log the creation of the entity.
    $this->logger->notice('Created new Investigation entity with ID @id.', ['@id' => $returnValue]);
    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function updateInvestigation($investigationId, array $data)
  {
    $investigation = Investigation::load($investigationId);

    if (!$investigation) {
      throw new NotFoundHttpException(sprintf('Investigation with ID %s was not found.', $investigationId));
    }
    $json_string = json_encode($data);
    $investigation->setJsonString($json_string);
    $entity=$investigation->save();
    
    return $entity;
  }


  /**
   * {@inheritdoc}
   */
  public function deleteInvestigation($investigationId){

    $investigation = Investigation::load($investigationId);
    if (!$investigation) {
      throw new NotFoundHttpException(sprintf('Investigation with ID %s was not found.', $investigationId));
    }
    
    $investigation->delete();

    $this->logger->notice('Moved Investigation with ID @id to archived.', ['@id' => $investigationId]);

  }

}