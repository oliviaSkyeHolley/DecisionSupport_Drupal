<?php

declare(strict_types=1);

namespace Drupal\process\Services\ProcessService;

use Drupal\process\Entity\Process;

/**
 * @todo Add interface description.
 */
interface ProcessServiceInterface {

  /**
   * Loads an Process entity.
   *
   * @return Process|null
   *   The Process entity, or NULL if not found.
   */
  public function getProcessList();

  /**
   * Creates a new Process entity.
   *
   * @param array $data
   *   The data for the new entity.
   *
   * @return Process
   *   The created Process entity.
   */
  public function createProcess(array $data);

  /**
   * Duplicates a new Process entity.
   *
   * @param array $data
   *   The data for the new entity.
   *
   * @return Process
   *   The duplicated Process entity.
   */
  public function duplicateProcess(array $data);


  /**
   * Updates a Process entity.
   *
   * @param array $data
   *   The data of the entity.
   *
   * @param $processId
   *   The id of the existing entity.
   *
   * @return Process
   *   The updated Process entity.
   */

  public function updateProcess($processId, array $data);

  /**
   * Delete a existing Process entity.
   *
   * @param $processId
   *   The id of the existing entity.
   */
  public function deleteProcess($processId);

  /**
   * Get a Process.
   *
   * @return string
   *   The JSON string.
   */
  public function getProcess(): string;




}
