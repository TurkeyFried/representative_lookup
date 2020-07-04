<?php

namespace Drupal\representative_lookup\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;

/**
 * Lorem Ipsum block form
 */
class PostalLookupBlockForm extends FormBase {

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
      // '#description' => $this->t('Check Representatives for this area'),
    ];

    // Submit.
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Lookup'),
      '#attributes' => [
        'class' => [
          'use-ajax-submit',
        ],
      ],
      '#ajax' => [
        'callback' => '::getRepresentatives', // don't forget :: when calling a class method.
        'disable-refocus' => FALSE, // Or TRUE to prevent re-focusing on the triggering element.
        'event' => 'click',
        'wrapper' => 'edit-reps-table', // This element is updated with this AJAX callback.
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Getting representatives...'),
        ],
      ],
    ];

    $form['reps-table'] = [
      '#type' => 'table',
      '#header' => [
        'Name',
        'Party',
        'Elected Office',
        'Representative Set',
      ],
      '#empty' => t('No representatives found'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    // @todo add validation for:
    // URLs must include the postal code in uppercase letters with no spaces.
    $postal = $form_state->getValue('postal');

    // $phrases = $form_state->getValue('phrases');
    // if (!is_numeric($phrases)) {
    //   $form_state->setErrorByName('phrases', $this->t('Please use a number.'));
    // }

    // if (floor($phrases) != $phrases) {
    //   $form_state->setErrorByName('phrases', $this->t('No decimals, please.'));
    // }

    // if ($phrases < 1) {
    //   $form_state->setErrorByName('phrases', $this->t('Please use a number greater than zero.'));
    // }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->messenger()->addStatus($this->t('Your postal code is @postal', [
      '@postal' => $form_state->getValue('postal'),
    ]));
  }

  // Get the value from example select field and fill
  // the textbox with the selected text.
  public function getRepresentatives(array &$form, FormStateInterface $form_state) {
    $postal = strtoupper($form_state->getValue('postal'));

    $cid = 'representative_lookup:' . $postal;
    $data = NULL;
    $cache = \Drupal::cache()->get($cid);

    if ($cache) {
      $reps = $cache->data;
    } else {
      $response = \Drupal::httpClient()->get('https://represent.opennorth.ca/postcodes/' . $postal);

      // @todo validate response here

      $response = json_decode($response->getBody(), true);

      $reps = [];

      foreach ($response['representatives_centroid'] as $rep) {
        $reps[] = [
          $rep['name'],
          $rep['party_name'],
          $rep['elected_office'],
          $rep['representative_set_name'],
        ];
      }

      \Drupal::cache()->set($cid, $reps, strtotime('+1 day'));
    }

    $form['reps-table']['#rows'] = $reps;

    return $form['reps-table'];
  }

}
