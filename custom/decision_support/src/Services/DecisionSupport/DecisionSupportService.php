<?php

declare(strict_types=1);

namespace Drupal\decision_support\Services\DecisionSupport;

use Drupal\decisionSupport\Entity\DecisionSupport;
use Drupal\process\Entity\Process;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @todo Add class description.
 */
final class DecisionSupportService implements DecisionSupportServiceInterface {

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
   * Constructs a DecisionSupportService object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, LoggerInterface $logger) {
    $this->entityTypeManager = $entity_type_manager;
    $this ->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public function getDecisionSupportList() {

    $unformattedDecisionSupport = DecisionSupport::loadMultiple();
    $decisionSupportList = array();
    foreach ($unformattedDecisionSupport as $unformattedDecisionSupport) {
      if ($unformattedDecisionSupport instanceof DecisionSupport) {
        $decisionSupport['label'] = $unformattedDecisionSupport->getName();
        $decisionSupport['entityId'] = $unformattedDecisionSupport->id();
        $decisionSupport['revisionId'] = $unformattedDecisionSupport->getRevisionId();
        $decisionSupport['createdTime'] = $unformattedDecisionSupport->getCreatedTime();
        $decisionSupport['updatedTime'] = $unformattedDecisionSupport->getupdatedTime();
        $decisionSupport['revisionStatus'] = $unformattedDecisionSupport->getRevisionStatus();
        $decisionSupport['json_string'] = $unformattedDecisionSupport->getJsonString();

        $decisionSupportList[] = $decisionSupport;
        unset($decisionSupport);
      }
    }

    return $decisionSupportList;
  }
/*
  public function getDecisionSupportReport(){

  }
*/

  public function getdecisionSupportReportList() {

    $unformattedDecisionSupportReport = DecisionSupportReport::loadMultiple();
    $decisionSupportReportList = array();
    foreach ($unformattedDecisionSupportReport as $unformattedDecisionSupportReport) {
      if ($unformattedDecisionSupportReport instanceof DecisionSupportReport) {
        $decisionSupportReport['label'] = $unformattedDecisionSupportReport->getName();
        $decisionSupportReport['entityId'] = $unformattedDecisionSupportReport->id();
        $decisionSupportReport['revisionId'] = $unformattedDecisionSupportReport->getRevisionId();
        $decisionSupportReport['createdTime'] = $unformattedDecisionSupportReport->getCreatedTime();
        $decisionSupportReport['updatedTime'] = $unformattedDecisionSupportReport->getupdatedTime();
        $decisionSupportReport['revisionStatus'] = $unformattedDecisionSupportReport->getRevisionStatus();
        $decisionSupportReport['json_string'] = $unformattedDecisionSupportReport->getJsonString();

        $decisionSupportReportList[] = $decisionSupportReport;
        unset($decisionSupportReport);
      }
    }

    return $decisionSupportReportList;
  }
}
