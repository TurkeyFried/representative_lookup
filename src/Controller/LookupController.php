<?php

namespace Drupal\representative_lookup\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\Client;

/**
 * Defines LookupController class.
 */
class LookupController extends ControllerBase {

  /**
   * @var \Guzzle\Client
   */
  protected $client;

  /**
   * Load the client for all routes
   */
  public function __construct() {
    $this->client = \Drupal::httpClient();
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
    $reps = $this->client->getRepresentatives([
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


    // foreach ($entries = getData() as $entry) {
    //   $row = array_map('Drupal\Component\Utility\SafeMarkup::checkPlain', $entry);
    //   $rows[] = $row;
    // }
    // $form['table'] = array(
    //   '#type' => 'table',
    //   '#header' => $headers,
    //   '#rows' => $rows,
    //   '#attributes' => array('id' => 'my-module-list'),
    //   '#empty' => t('No entries available.'),
    // );

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
    // $form_class = '\Drupal\my_module\Form\MyForm';
    // $build['form'] = \Drupal::formBuilder()->getForm($form_class);

    return [
      '#type' => 'markup',
      '#markup' => $this->t('Hello, World!'),
    ];
  }

}
