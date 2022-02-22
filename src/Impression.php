<?php

namespace Drupal\neg_analytics;

use Drupal\neg_analytics\Handlers\GoogleAnalytics;
use Drupal\neg_analytics\Handlers\PinterestAnalytics;
use Drupal\neg_analytics\Handlers\FacebookAnalytics;

/**
 * Analytics impression class.
 */
class Impression {

  /**
   * {@inheritdoc}
   */
  protected static $instance = FALSE;

  /**
   * {@inheritdoc}
   */
  protected $attachments;

  /**
   * {@inheritdoc}
   */
  protected $handlers = [];

  /**
   * {@inheritdoc}
   */
  protected $productImpressions = [
    'detail' => [],
    'list' => [],
  ];

  /**
   * {@inheritdoc}
   */
  protected $events = [];

  /**
   * Gets the service instance.
   */
  public static function instance() {
    if (self::$instance === FALSE) {
      self::$instance = new self();

      $filterEnabled = Settings::config()->get('filter_enabled');
      $filterDomain = Settings::config()->get('filter_domain');
      if ($filterEnabled && $filterDomain) {
        $host = \Drupal::request()->getHost();
        if ($host == $filterDomain) {
          return self::$instance;
        }
      }

      $gaEnabled = Settings::config()->get('ga_enabled');
      if ($gaEnabled && ($id = Settings::config()->get('ga_measurement_id')) !== NULL) {
        self::$instance->handlers[] = new GoogleAnalytics($id);
      }

      $fbEnabled = Settings::config()->get('pixel_enabled');
      if ($fbEnabled && strlen($id = Settings::config()->get('facebook_pixel')) > 0) {
        self::$instance->handlers[] = new FacebookAnalytics($id);
      }

      $pinterestEnabled = Settings::config()->get('pinterest_enabled');
      if ($pinterestEnabled && strlen($id = Settings::config()->get('pinterest_tag_id')) > 0) {
        self::$instance->handlers[] = new PinterestAnalytics($id);
      }
    }

    return self::$instance;
  }

  /**
   * Adds a product impression.
   */
  public function addProductImpression($product, $type) {
    $this->productImpressions[$type][] = $product;
  }

  /**
   * Adds and event.
   */
  public function addEvent($event) {
    $this->events[] = $event;
  }

  /**
   * Attaches library attachments.
   */
  public function addLibraryAttachments(&$attachments) {
    foreach ($this->handlers as $handler) {
      $handler->addAttachments($attachments);
    }
  }

  /**
   * Renders all handlers.
   */
  public function addAttachments(&$attachments) {

    // Add base cache tags.
    $attachments['#cache'] = [
      'tags' => ['config:neg_analytics.settings'],
    ];

    $this->attachments = &$attachments;

    // Allow handlers to add attachments.
    foreach ($this->handlers as $handler) {
      $handler->render($attachments);
    }

    // Render product impression events.
    $this->renderImpressions();
  }

  /**
   * Renders impression events.
   */
  protected function renderImpressions() {
    $tags = (isset($this->attachments['#cache']['tags'])) ? $this->attachments['#cache']['tags'] : [];

    // view_item_list.
    if (count($this->productImpressions['list']) > 0) {
      $items = $this->renderProducts($this->productImpressions['list']);
      $this->addEvent([
        'event' => 'view_item_list',
        'items' => $items['views'],
      ]);
      $tags = array_merge($tags, $items['tags']);
    }

    // view_item.
    if (count($this->productImpressions['detail']) > 0) {
      $items = $this->renderProducts($this->productImpressions['detail']);
      $this->addEvent([
        'event' => 'view_item',
        'items' => $items['views'],
      ]);
      $tags = array_merge($tags, $items['tags']);
    }

    $this->renderEvents($tags);
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

  /**
   * Renders events.
   */
  protected function renderEvents($tags) {

    if (count($this->events) === 0) {
      return;
    }

    // Merge attachment tags.
    $this->attachments['#cache']['tags'] = $tags;

    $code = '';
    foreach ($this->events as $event) {
      $eventName = $event['event'];
      $items = $event['items'];

      if (count($items) === 0) {
        continue;
      }

      $json = json_encode($items);
      $code .= "events.triggerEvent('{$eventName}', {'items': {$json}});\n";
    }

    if (strlen($code) > 0) {
      $this->attachments['#attached']['library'][] = 'neg_analytics/render_events';
      $this->attachments['#attached']['drupalSettings']['neg_analytics']['events'] = $code;
    }
  }

}
