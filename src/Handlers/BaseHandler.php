<?php

namespace Drupal\neg_analytics\Handlers;

/**
 * Base Handler.
 */
class BaseHandler {

  /**
   * {@inheritdoc}
   */
  protected $measurementId = NULL;

  /**
   * {@inheritdoc}
   */
  protected $attachments;

  /**
   * {@inheritdoc}
   */
  protected $library = NULL;

  /**
   * Implements __construct().
   */
  public function __construct($measurementId) {
    $this->measurementId = $measurementId;
  }

  /**
   * Attaches frontend library code.
   */
  public function addAttachments(&$variables) {
    if ($this->library !== NULL) {
      $variables['#attached']['library'][] = $this->library;
    }
  }

  /**
   * Renders tracking code.
   */
  public function render(&$attachments) {
    $this->attachments = &$attachments;
    $this->renderBaseCode();
  }

  /**
   * Renders base GA code.
   */
  protected function renderBaseCode() {

  }

}
