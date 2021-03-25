<?php

namespace Drupal\neg_analytics;

use Drupal\neg_analytics\Handlers\GoogleAnalytics;
use Drupal\neg_analytics\Handlers\PinterestAnalytics;
use Drupal\neg_analytics\Handlers\FacebookAnalytics;

/**
 * Analytics impression class.
 */
class Impression {

  protected static $instance = FALSE;
  protected $handlers = [];

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
    foreach ($this->handlers as $handler) {
      $handler->addImpression($product, $type);
    }
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

    // Allow handlers to add attachments.
    foreach ($this->handlers as $handler) {
      $handler->render($attachments);
    }
  }

}
