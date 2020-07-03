<?php

namespace Drupal\representative_lookup\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\http_client_manager\HttpClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines LookupController class.
 */
class LookupController extends ControllerBase {

  /**
   * @var \Drupal\http_client_manager\HttpClientInterface
   */
  protected $httpClient;

  /**
   * @param \Drupal\http_client_manager\HttpClientInterface $http_client
   */
  public function __construct(HttpClientManagerFactoryInterface $http_client) {
    $this->httpClient = $http_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('representative_lookup.http_client.contents')
    );
  }

  /**
   * Representatives route callback.
   *
   * @param string $postal
   *   The postal code to search in.
   * @param int $limit
   *   The total number of representatives we want to fetch.
   * @param string $sort
   *   The sorting order.
   *
   * @return array
   *   A render array used to show the Representative list.
   */
  public function representatives($postal, $limit, $sort) {
    $reps = $this->httpClient->getRepresentatives([
      'postal' => strtoupper($postal),
      'limit' => (int) $limit,
      'sort' => $sort,
    ]);


    $build = [
      '#theme' => 'representative_lookup_representatives_list',
      '#representatives' => [],
    ];

    // if ($request->getStatusCode() != 200) {
    //   return $build;
    // }

    // $reps = $request->getBody()->getContents();

    foreach ($reps as $rep) {
      $build['#representatives'][] = [
        'id' => $rep['id'],
        'title' => $rep['title'],
        'text' => $rep['text'],
      ];
    }

    return $build;
  }

  /**
   * Display the markup.
   *
   * @return array
   *   Return markup array.
   */
  public function content() {
    //@todo page should load form and empty table
    // then the API call response will trigger the response
    // how do we use JS here?
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Hello, World!'),
    ];
  }

}
