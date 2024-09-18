<?php

declare(strict_types=1);

namespace Drupal\investigation_documents\Plugin\rest\resource;

use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\Core\KeyValueStore\KeyValueStoreInterface;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Route;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\investigation_documents\Entity\InvestigationDocuments;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\investigation_documents\Services\InvestigationDocumentsService\InvestigationDocumentsService;

/**
 * Represents Get Investigation Documents records as resources.
 *
 * @RestResource (
 *   id = "get_investigation_documents",
 *   label = @Translation("Get Investigation Documents"),
 *   uri_paths = {
 *     "canonical" = "/rest/investigation/document/get/{investigationId}",
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
final class GetInvestigationDocuments extends ResourceBase {

   /**
   * The key-value storage.
   */
  private readonly KeyValueStoreInterface $storage;

  /**
   * The current user.
   */
  private AccountProxyInterface $currentUser;

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
    AccountProxyInterface    $currentUser,
    InvestigationDocumentsService $investigation_documents_service
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->storage = $keyValueFactory->get('get_investigation_document');
    $this->currentUser = $currentUser;
    $this->investigationDocumentsService = $investigation_documents_service;
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
      $container->get('investigation_documents.service')
    );
  }

  /**
   * Responds to GET requests.
   */
  public function get($investigationId){

    // Check user permissions.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }

    try {
      // Retrieve the list of processes.
      $investigationDocuments = $this->investigationDocumentsService->getInvestigationDocuments($investigationId);
      $response = new ResourceResponse($investigationDocuments);
      $response->addCacheableDependency($this->currentUser);

      return $response;
    }
    catch (\Exception $e) {
      // Log the error message.
      $this->logger->error('An error occurred while loading Investigation Document list: @message', ['@message' => $e->getMessage()]);

      // Throw a generic HTTP exception for internal server errors.
      throw new HttpException(500, 'Internal Server Error');
    }
  }
  }

