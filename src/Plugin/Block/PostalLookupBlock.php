<?php

namespace Drupal\representative_lookup\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a Postal Lookup Block.
 *
 * @Block(
 *   id = "lookup_block",
 *   admin_label = @Translation("Postal Lookup block"),
 *   category = @Translation("Custom"),
 * )
 */
class PostalLookupBlock extends BlockBase implements BlockPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Just build the form
    return \Drupal::formBuilder()->getForm('Drupal\representative_lookup\Form\PostalLookupBlock');
  }

}