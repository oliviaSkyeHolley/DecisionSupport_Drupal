<?php

declare(strict_types=1);

namespace Drupal\ReportGenerator\Services\ReportGeneratorService;

use Drupal\ReportGenerator\Entity\ReportGenerator;

/**
 * Interface for ReportGenerator service.
 */
interface ReportGeneratorServiceInterface {

  /**
   * Loads a ReportGenerator entity.
   *
   * @return ReportGenerator|null
   *   The ReportGenerator entity, or NULL if not found.
   */
  public function getReportGeneratorList();

/**
 * Get a ReportGenerator by ID.
 *
 * @param int $ReportGeneratorId
 *   The ID of the ReportGenerator entity to retrieve.
 *
 * @return string
 *   The JSON representation of the ReportGenerator entity.
 */
  public function getReportGenerator($ReportGeneratorId);

  /**
   * Creates a new ReportGenerator entity.
   *
   * @param array $data
   *   The data for the new entity.
   *
   * @return ReportGenerator
   *   The created ReportGenerator entity.
   */
  public function createReportGenerator(array $data);

  /**
   * Duplicates a new ReportGenerator entity.
   *
   * @param array $data
   *   The data for the new entity.
   *
   * @return ReportGenerator
   *   The duplicated ReportGenerator entity.
   */
  public function duplicateReportGenerator(array $data);


  /**
   * Updates a ReportGenerator entity.
   *
   * @param array $data
   *   The data of the entity.
   *
   * @param $ReportGeneratorId
   *   The id of the existing entity.
   *
   * @return ReportGenerator
   *   The updated ReportGenerator entity.
   */
  public function patchReportGenerator($ReportGeneratorId, array $data);

    /**
   * Updates a ReportGenerator Json String.
   *
   * @param array $data
   *   The data of the entity.
   *
   * @param $ReportGeneratorId
   *   The id of the existing entity.
   *
   * @return ReportGenerator
   *   The updated ReportGenerator entity.
   *   The updated ReportGenerator entity.
   */
  public function updateReportGenerator($ReportGeneratorId, array $data);

  /**
   * Move a existing ReportGenerator entity to archived.
   *
   * @param $ReportGeneratorId
   *   The id of the existing entity.
   */
  public function deleteReportGenerator($ReportGeneratorId);



}
