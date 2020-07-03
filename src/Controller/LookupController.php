<?php

namespace Drupal\canadian_representatives\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Defines LookupController class.
 */
class LookupController extends ControllerBase {

  /**
   * Display the markup.
   *
   * @return array
   *   Return markup array.
   */
  public function content() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Hello, World!'),
    ];
  }
}
