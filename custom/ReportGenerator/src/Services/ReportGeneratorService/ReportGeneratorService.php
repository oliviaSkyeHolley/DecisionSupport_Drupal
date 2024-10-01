<?php

declare(strict_types=1);

namespace Drupal\ReportGenerator\Services\ReportGeneratorService;


use Drupal\process\Entity\ReportGenerator;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Service Class for handling ReportGenerator.
 */
final class ReportGeneratorService implements ReportGeneratorInterface {

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
  public function getReportGeneratorList() {

    $unformattedReportGenerator = ReportGenerator::loadMultiple();
    $ReportGeneratorList = array();
    foreach ($unformattedReportGenerator as $unformattedReportGenerator) {
      if ($unformattedReportGenerator instanceof ReportGenerator) {
        $ReportGenerator['label'] = $unformattedReportGenerator->getName();
        $ReportGenerator['entityId'] = $unformattedReportGenerator->id();
        $ReportGenerator['revisionId'] = $unformattedReportGenerator->getRevisionId();
        $ReportGenerator['revisionCreationTime'] = $unformattedReportGenerator->getRevisionCreationTime();
        $ReportGenerator['createdTime'] = $unformattedReportGenerator->getCreatedTime();
        $ReportGenerator['updatedTime'] = $unformattedReportGenerator->getupdatedTime();
        $ReportGenerator['revisionStatus'] = $unformattedReportGenerator->getRevisionStatus();
        $ReportGenerator['json_string'] = $unformattedReportGenerator->getJsonString();

        $ReportGeneratorList[] = $ReportGenerator;
        unset($ReportGenerator);
      }
    }

    return $ReportGeneratorList;
  }

  /**
   * {@inheritdoc}
   */
  public function getReportGenerator($ReportGeneratorId) {

    $pReportGenerator = ReportGenerator::load($ReportGeneratorId);

    if(!$ReportGenerator){
      throw new NotFoundHttpException(sprintf('ReportGenerator with ID %s was not found.', $processId));
    }
    $ReportGeneratorsonString = $ReportGenerator->getJsonString();

    return $ReportGeneratorJsonString;
  }

  /**
   * {@inheritdoc}
   */
  public function createReportGenerator(array $data) {

    $ReportGenerator = ReportGenerator::create($data);

    $entityId = $ReportGenerator->save();
    $returnValue['entityId'] = $ReportGenerator->id();
    $jsonstring = [
      'entityId' =>$ReportGenerator->id(),
      'uuid'=>uniqid(),
      'label' =>$ReportGenerator->label(),
      'steps'=>[]
    ];
    $ReportGeneratorJsonstring = json_encode($jsonstring);
    $ReportGenerator->setJsonString($ReportGeneratorJsonstring);
    $ReportGenerator->setRevisionStatus($data['revision_status']);
    $entity=$ReportGenerator->save();

    // log the creation of the entity.
    $this->logger->notice('Created new ReportGenerator entity with ID @id.', ['@id' => $returnValue]);
    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function duplicateReportGenerator(array $data) {

    $ReportGenerator = ReportGenerator::create($data);

    $entityId = $ReportGenerator->save();
    $returnValue['entityId'] = $ReportGenerator->id();
    $data_jsonstring = json_decode($data['json_string'],true);
    $newjsonstring = [
      'entityId' =>$ReportGenerator->id(),
      'uuid'=>uniqid(),
      'ReportGeneratorLabel' =>$ReportGenerator->label(),
      'steps'=>$data_jsonstring['steps']
    ];
    $ReportGeneratorJsonstring = json_encode($newjsonstring);
    $ReportGenerator->setJsonString($ReportGeneratorJsonstring);
    $ReportGenerator->setRevisionStatus($data['revision_status']);
    $entity=$ReportGenerator->save();

    // log the creation of the entity.
    $this->logger->notice('Duplicated ReportGenerator entity with new ID @id.', ['@id' => $returnValue]);
    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function patchReportGenerator($ReportGeneratorId, array $data)
  {
    $ReportGenerator = ReportGenerator::load($ReportGeneratorId);

    if (!$ReportGenerator) {
      throw new NotFoundHttpException(sprintf('ReportGenerator with ID %s was not found.', $ReportGeneratorId));
    }

    $ReportGenerator->setName($data['label']);
    $ReportGenerator->setRevisionStatus($data['revision_status']);
    $entity=$ReportGenerator->save();

    $this->logger->notice('The ReportGenerator @id has been updated.', ['@id' => $ReportGeneratorId]);

    return $entity;
  }

    /**
   * {@inheritdoc}
   */
  public function updateReportGenerator($ReportGeneratorId, array $data)
  {
    $ReportGenerator = ReportGenerator::load($ReportGeneratorId);

    if (!$ReportGenerator) {
      throw new NotFoundHttpException(sprintf('ReportGenerator with ID %s was not found.', $ReportGeneratorId));
    }
    $json_string = json_encode($data);

    $ReportGenerator->setJsonString($json_string);
    $entity=$ReportGenerator->save();

    $this->logger->notice('The ReportGenerator @id has been updated.', ['@id' => $ReportGeneratorId]);

    return $entity;
  }


  /**
   * {@inheritdoc}
   */
  public function deleteReportGenerator($ReportGeneratorId){

    $ReportGenerator = ReportGenerator::load($ReportGeneratorId);
    if (!$ReportGenerator) {
      throw new NotFoundHttpException(sprintf('ReportGenerator with ID %s was not found.', $ReportGeneratorId));
    }
    $label = $ReportGenerator -> getLabel();
    $newLabel = "$label - Archived";
    $ReportGenerator->setLabel($newLabel);
    $entity=$ReportGenerator->save();

    $this->logger->notice('Moved ReportGenerator with ID @id to archived.', ['@id' => $ReportGeneratorId]);

    return $entity;
  }


}
