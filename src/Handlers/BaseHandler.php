<?php

namespace Drupal\neg_analytics\Handlers;

/**
 * Base Handler.
 */
class BaseHandler {

  protected $events = [];
  protected $productImpressions = [
    'detail' => [],
    'list' => [],
  ];
  protected $measurementId = NULL;
  protected $attachments;
  protected $library = NULL;

  /**
   * Implements __construct().
   */
  public function __construct($measurementId) {
    $this->measurementId = $measurementId;
  }

  /**
   * Adds an impression.
   */
  public function addImpression($product, $type) {
    $this->productImpressions[$type][] = $product;
  }

  /**
   * Adds and event.
   */
  public function addEvent($event) {
    $this->events[] = $event;
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
    $this->renderImpressions();
  }

  /**
   * Renders base GA code.
   */
  protected function renderBaseCode() {

  }

  /**
   * Renders product impressions.
   */
  protected function renderImpressions() {

  }


  /**
   * Renders products.
   */
  protected function renderProducts($items) {
    $views = [];
    $tags = [];
    foreach ($items as $product) {
      $view = $product->getAnalyticsDetails();

      if (!$view) {
        continue;
      }

      $view['#tags'] = $product->getCacheTags();

      if ($view) {
        $tags = array_merge($tags, $view['#tags']);
        unset($view['#tags']);
        $views[] = $view;
      }
    }

    return [
      'views' => $views,
      'tags' => $tags,
    ];
  }

}
