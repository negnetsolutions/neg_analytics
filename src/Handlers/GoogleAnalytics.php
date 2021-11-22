<?php

namespace Drupal\neg_analytics\Handlers;

use Drupal\neg_analytics\Settings;

/**
 * GA Handler.
 */
class GoogleAnalytics extends BaseHandler {

  protected $library = 'neg_analytics/google_analytics';

  /**
   * Renders product impressions.
   */
  protected function renderImpressions() {
    $tags = (isset($attachments['#cache']['tags'])) ? $attachments['#cache']['tags'] : [];

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
   * Renders GA events.
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
      $json = json_encode($items);
      $code .= "gtag('event', '{$eventName}', { items: {$json}});\n";
    }

    if (strlen($code) > 0) {
      $this->attachments['#attached']['drupalSettings']['neg_analytics']['google']['events'] = $code;
    }
  }

  /**
   * Renders base GA code.
   */
  protected function renderBaseCode() {

    // Include GA scripts.
    $this->attachments['#attached']['html_head'][] = [
      [
        '#type' => 'html_tag',
        '#tag' => 'link',
        '#value' => '',
        '#attributes' => [
          'href' => 'https://www.googletagmanager.com',
          'rel' => 'preconnect',
        ],
      ],
      'google_analytics_preconnect',
    ];
    $this->attachments['#attached']['http_header'][] = [
      'Link',
      '<https://www.googletagmanager.com>; rel="preconnect"'
    ];

    $customCode = Settings::config()->get('ga_custom_code');
    if (strlen($customCode) > 0) {
      $this->attachments['#attached']['drupalSettings']['neg_analytics']['google']['customCode'] = $customCode;
    }
    else {
      $this->attachments['#attached']['drupalSettings']['neg_analytics']['google']['measurementId'] = $this->measurementId;
    }

  }

  /**
   * Renders GA View items.
   */
  protected function renderProducts($items) {
    $views = [];
    $tags = [];
    foreach ($items as $product) {
      $view = self::getGoogleAnalyticsProductImpression($product);

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
   * Get's google analytics view.
   */
  public static function getGoogleAnalyticsProductImpression($product) {
    $variant = $product->getFirstAvailableVariant();
    if (!$variant) {
      return FALSE;
    }

    $item = [
      'name' => $product->get('title')->value,
      'id' => $product->get('product_id')->value,
      'variant' => $variant->get('sku')->value,
      'brand' => $product->get('vendor')->value,
      'price' => $variant->get('price')->value,
      '#tags' => $product->getCacheTags(),
    ];

    return $item;
  }

}
