<?php

namespace Drupal\representative_lookup\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use GuzzleHttp\Exception\ClientException;

/**
 * Postal Lookup form
 */
class PostalLookupBlockForm extends FormBase {
  // headers for the representative table
  // @todo move stuff somewhere else?
  const HEADERS = [
    'name' => 'Name',
    'party_name' => 'Party',
    'elected_office' => 'Elected Office',
    'representative_set_name' => 'Representative Set',
  ];

  private $reps = [];
  private $errors = [];

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'postal_lookup_block_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // What's the Postal Code?
    $form['postal'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Postal Code'),
      '#default_value' => '',
    ];

    // Submit
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Lookup'),
      '#required' => true,
      '#ajax' => [
        'callback' => '::lookupAjax', // don't forget :: when calling a class method.
        'disable-refocus' => FALSE, // Or TRUE to prevent re-focusing on the triggering element.
        'event' => 'click',
        'wrapper' => 'edit-reps-table', // This element is updated with this AJAX callback.
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Getting representatives...'),
        ],
      ],
    ];

    // Simple Table
    $form['reps-table'] = [
      '#type' => 'table',
      '#header' => array_values(self::HEADERS),
      '#empty' => $this->t('No representatives found'),
      '#attributes' => [
        'id' => 'edit-reps-table',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    $postal = $form_state->getValue('postal');

    // https://stackoverflow.com/a/46761018/1978219
    $regex = "/^[ABCEGHJ-NPRSTVXY]\d[ABCEGHJ-NPRSTV-Z][ -]?\d[ABCEGHJ-NPRSTV-Z]\d$/i";

    if (!preg_match($regex, $postal)) {
      // Response taken from https://en.wikipedia.org/wiki/Postal_codes_in_Canada
      $form_state->setErrorByName('postal', $this->t('Postal Codes must be written as A1A1A1, where A are letters and 1 are numbers'));
      return;
    }

    $this->callApi($postal); // catch api errors while we can still set them

    foreach ($this->errors as $error) {
      $form_state->setErrorByName('reps-table', $error);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // abstract method. have not found what are best practices for it when doing ajax forms
  }

  public function lookupAjax(array &$form, FormStateInterface $form_state) {
    // iterate the cached value so we can expand the app to allow different columns
    $rows = [];

    foreach ($this->reps as $rep) {
      $row = [];

      // simple tables on drupal use simple arrays
      // double foreach is not ideal,
      // but for array filtering it should be fine
      foreach (array_keys(self::HEADERS) as $column) {
        $row[] = isset($rep[$column]) ? $rep[$column] : '';
      }

      $rows[] = $row;
    }

    $form['reps-table']['#rows'] = $rows;

    return $form['reps-table'];
  }

  public function callApi($postal) {
    $this->reps = [];
    $this->errors = [];

    // the api requires uppercase postal codes
    $postal = strtoupper($postal);

    // per-postal caches
    $cid = 'representative_lookup:'. $postal;

    $cache = \Drupal::cache()->get($cid);

    if ($cache) {
      $this->reps = $cache->data;
      return;
    }

    try {
      // let's cache all the results for this postal code once a day
      // doesn't seem like the api paginates on postal codes
      $response = \Drupal::httpClient()->get('https://represent.opennorth.ca/postcodes/' . $postal);

      $response = json_decode($response->getBody(), true);
      $this->reps = $response['representatives_centroid'];

    } catch (ClientException $e) {
      // @todo add logging for API errors
      $this->errors[] = $this->t('Error attempting to download representatives. Please check your Postal Code and try again.');
    }

    // spaceship witchcraft suggested by https://stackoverflow.com/a/2699159/1978219
    usort($this->reps, function($a, $b) {
        return $a['representative_set_name'] <=> $b['representative_set_name'];
    });

    \Drupal::cache()->set($cid, $this->reps, strtotime('+1 day'));
  }

}
