<?php

declare(strict_types=1);

namespace Drupal\ReportGenerator\Plugin\rest\resource;

use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\Core\KeyValueStore\KeyValueStoreInterface;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Route;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\ReportGenerator\Entity\ReportGenerator;
use Drupal\ReportGenerator\Services\ReportGeneratorService\ReportGeneratorService;

/**
 * Represents Update ReportGenerator records as resources.
 *
 * @RestResource (
 *   id = "update_ReportGenerator_resource",
 *   label = @Translation("Update ReportGenerator"),
 *   uri_paths = {
 *     "canonical" = "/rest/ReportGenerator/update/{ReportGeneratorId}",
 *     "patch" = "/rest/ReportGenerator/update/{ReportGeneratorId}"
 *   }
 * )
 *
 * @DCG
 * The plugin exposes key-value records as REST resources. In order to enable it
 * import the resource configuration into active configuration storage. An
 * example of such configuration can be located in the following file:
 * core/modules/rest/config/optional/rest.resource.entity.node.yml.
 * Alternatively, you can enable it through admin interface provider by REST UI
 * module.
 * @see https://www.drupal.org/project/restui
 *
 * @DCG
 * Notice that this plugin does not provide any validation for the data.
 * Consider creating custom normalizer to validate and normalize the incoming
 * data. It can be enabled in the plugin definition as follows.
 * @code
 *   serialization_class = "Drupal\foo\MyDataStructure",
 * @endcode
 *
 * @DCG
 * For entities, it is recommended to use REST resource plugin provided by
 * Drupal core.
 * @see \Drupal\rest\Plugin\rest\resource\EntityResource
 */
final class UpdateReportGeneratorResource extends ResourceBase {

  /**
   * The key-value storage.
   */
  private readonly KeyValueStoreInterface $storage;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    KeyValueFactoryInterface $keyValueFactory,
    AccountProxyInterface $currentUser,
    ReportGeneratorervice $ReportGeneratorservice
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->storage = $keyValueFactory->get('update_ReportGenerator_resource');
    $this->currentUser = $currentUser;
    $this->ReportGeneratorService = $ReportGeneratorservice;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('keyvalue'),
      $container->get('current_user'),
      $container->get('ReportGenerator.service')
    );
  }

 /**
   * Responds to PATCH requests.
   *
   * @param int $ReportGeneratorId
   *   The ID of the ReportGenerator entity to update.
   * @param array $data
   *   The data to update the ReportGenerator entity with.
   *
   * @return \Drupal\rest\ModifiedResourceResponse
   *   The modified resource response.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Thrown when an error occurs during the update.
   */
  public function patch($ReportGeneratorId, array $data): ModifiedResourceResponse {

    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }

    try {
      // Attempt to update the ReportGenerator entity.
      $entity = $this->ReportGeneratorService->updateReportGenerator($ReportGeneratorId,$data);
      $this->logger->notice('The ReportGenerator @id has been updated.', ['@id' => $ReportGeneratorId]);

      // Return a response with status code 200 OK.
      return new ModifiedResourceResponse($entity, 200);
    }
    catch (\Exception $e) {
      // Handle any other exceptions that occur during the update.
      $this->logger->error('An error occurred while updating ReportGenerator: @message', ['@message' => $e->getMessage()]);
      throw new HttpException(500, 'Internal Server Error');
    }
  }


}
