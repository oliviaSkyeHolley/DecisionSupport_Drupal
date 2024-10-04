<?php

declare(strict_types=1);

namespace Drupal\decision_support\Plugin\rest\resource;

use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\Core\KeyValueStore\KeyValueStoreInterface;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\decision_support\Entity\DecisionSupport;
use Drupal\decision_support\Services\DecisionSupport\DecisionSupportService;

/**
 * Represents Decision Support Get records as resources.
 *
 * @RestResource (
 *   id = "get_decision_support_report",
 *   label = @Translation("Decision Support Report Get"),
 *   uri_paths = {
 *     "canonical" = "/rest/support/report/{decisionSupportId}",
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
final class GetDecisionSupportReportResource extends ResourceBase {



  /**
   * Responds to GET requests.
   */
  public function get($decisionSupportId): JsonResponse
  {
    // Check user permissions.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }

    try {
      // Retrieve the decision support data.
      $decisionSupportJsonString = $this->decisionSupportService->getDecisionSupport($decisionSupportId);

      // Return the JSON response.
      return new JsonResponse($decisionSupportJsonString, 200, [], true);
    }
    catch (\Exception $e) {
      // Log the error message.
      $this->logger->error('An error occurred while loading DecisionSupport: @message', ['@message' => $e->getMessage()]);

      // Throw a generic HTTP exception for internal server errors.
      throw new HttpException(500, 'Internal Server Error');
    }
  }

  /* sift through json file and get relevant data for the report */
  public function addReport($decisionSupportId){
    $report_json = $this->get($decisionSupportId);
    $questionNumber = $report_json['id'];
    $question = $report_json['description'];
    $answerCode = $report_json['answer'];
    $textAnswer = $report_json['textAnswer'];

    /*need to figure out how I can compare there answer
    with the choices to figure out if I should return
    yes or no*/

    $choices = $report_json['choiceUuid'];

    foreach($choices as $choice){
      if($answerCode == $choice){
        $answer = $choice;
      }
    }
/*
    $questionString =  'Question: '. $questionNumber. ' - '. $question;
    $answerString = 'Answer: '. $answer;
    $additionalInformation = 'Additional Information: '. $textAnswer;
*/


    /* call the class that outputs this infomation */

    echo 'Question: '. $questionNumber. ' - '. $question;
    echo 'Answer: '. $choice;
    echo 'Additional Information: '. $textAnswer;
  }

}
