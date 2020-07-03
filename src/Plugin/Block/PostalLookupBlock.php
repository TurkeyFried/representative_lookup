<?php

namespace Drupal\representative_lookup\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a Postal Lookup block for getting and displaying a list of representatives.
 *
 * @Block(
 *   id = "postal_lookup_block",
 *   admin_label = @Translation("Lorem ipsum block"),
 * )
 */
class PostalLookupBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Return the form @ Form/PostalLookupBlockForm.php.
    // @todo can i do this with a relative path?
    return \Drupal::formBuilder()->getForm('Drupal\representative_lookup\Form\PostalLookupBlockForm');

    // $config = $this->getConfiguration();

    // if (!empty($config['hello_block_name'])) {
    //   $name = $config['hello_block_name'];
    // }
    // else {
    //   $name = $this->t('to no one');
    // }

    // return [
    //   '#markup' => $this->t('Hello @name!', [
    //     '@name' => $name,
    //   ]),
    // ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    // @todo is this the best permission to use?
    return AccessResult::allowedIfHasPermission($account, 'generate lorem ipsum');
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {

    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    // $form['hello_block_name'] = [
    //   '#type' => 'textfield',
    //   '#title' => $this->t('Who'),
    //   '#description' => $this->t('Who do you want to say hello to?'),
    //   '#default_value' => isset($config['hello_block_name']) ? $config['hello_block_name'] : '',
    // ];

    // $form['actions']['custom_submit'] = [
    //   '#type' => 'submit',
    //   '#name' => 'custom_submit',
    //   '#value' => $this->t('Custom Submit'),
    //   '#submit' => [[$this, 'custom_submit_form']],
    // ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->setConfigurationValue('postal_lookup_block_settings', $form_state->getValue('postal_lookup_block_settings'));

    // parent::blockSubmit($form, $form_state);
    // $values = $form_state->getValues();
    // $this->configuration['hello_block_name'] = $form_state->getValue(['myfieldset', 'hello_block_name']);
  }

  // /**
  //  * Custom submit actions.
  //  */
  // public function custom_submit_form($form, FormStateInterface $form_state) {
  //   $values = $form_state->getValues();
  //   // Perform the required actions.
  // }

}
