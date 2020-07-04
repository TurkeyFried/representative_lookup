<?php

namespace Drupal\representative_lookup\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

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

}
