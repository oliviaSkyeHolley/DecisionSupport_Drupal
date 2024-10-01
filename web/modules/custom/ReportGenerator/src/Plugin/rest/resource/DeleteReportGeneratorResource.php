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
 * Represents Delete ReportGenerator records as resources.
 *
 * @RestResource (
 *   id = "delete_ReportGenerator_resource",
 *   label = @Translation("Delete ReportGenerator"),
 *   uri_paths = {
 *     "canonical" = "/rest/ReportGenerator/delete/{ReportGeneratorId}",
 *     "patch" = "/rest/ReportGenerator/delete/{ReportGeneratorId}"
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
final class DeleteReportGeneratorResource extends ResourceBase {

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
    ReportGeneratorService $ReportGenerator_service
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->storage = $keyValueFactory->get('delete_ReportGenerator_resource');
    $this->currentUser = $currentUser;
    $this->ReportGeneratorService = $ReportGenerator_service;
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
   * Responds to Patch requests.
   *
   * @param string $ReportGeneratorId
   *   The ID of the ReportGenerator entity to be archived.
   *
   * @return \Drupal\rest\ModifiedResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Thrown when an error occurs during the ReportGenerator.
   */
  public function patch($ReportGeneratorId): ModifiedResourceResponse {
    // Check user permissions.
    if (!$this->currentUser->hasPermission('access content')) {
       throw new AccessDeniedHttpException();
     }

     try {
      // Attempt to update the ReportGenerator entity.
      $entity = $this->ReportGeneratorService->deleteReportGenerator($ReportGeneratorId);
      $this->logger->notice('The ReportGenerator @id has been moved to Archived.', ['@id' => $ReportGeneratorId]);

      // Return a response with status code 200 OK.
      return new ModifiedResourceResponse($entity, 200);
    }
    catch (\Exception $e) {
      // Handle any other exceptions that occur during moving entity to archived.
      $this->logger->error('An error occurred while moving ReportGenerator to archived: @message', ['@message' => $e->getMessage()]);
      throw new HttpException(500, 'Internal Server Error');
    }
   }

}
