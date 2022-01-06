<?php

namespace Drupal\neg_analytics\Handlers;

/**
 * Facebook Handler.
 */
class FacebookAnalytics extends BaseHandler {

  protected $library = 'neg_analytics/facebook_analytics';

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
      $eventName = 'ViewContent';

      $items = [];
      foreach ($event['items'] as $item) {
        $items[] = $item['id'];
      }

      if (count($items) === 0) {
        continue;
      }

      $json = json_encode($items);
      $code .= "fbq('track', '{$eventName}', {content_ids: {$json}});\n";
    }

    if (strlen($code) > 0) {
      $this->attachments['#attached']['drupalSettings']['neg_analytics']['facebook']['events'] = $code;
    }
  }

  /**
   * Renders base code.
   */
  protected function renderBaseCode() {

    // Include Pixel scripts.
    $this->attachments['#attached']['html_head'][] = [
      [
        '#type' => 'html_tag',
        '#tag' => 'link',
        '#value' => '',
        '#attributes' => [
          'href' => 'https://connect.facebook.net',
          'rel' => 'preconnect',
        ],
      ],
      'facebook_analytics_preconnect',
    ];
    $this->attachments['#attached']['http_header'][] = [
      'Link',
      '<https://connect.facebook.net>; rel="preconnect"'
    ];

    $this->attachments['#attached']['drupalSettings']['neg_analytics']['facebook']['measurementId'] = $this->measurementId;

    $this->attachments['#attached']['html_head'][] = [
      [
        '#type' => 'html_tag',
        '#tag' => 'img',
        '#noscript' => TRUE,
        '#attributes' => [
          'height' => 1,
          'width' => 1,
          'style' => 'display: none;',
          'src' => "https://www.facebook.com/tr?id={$this->measurementId}&ev=PageView&noscript=1",
        ],
      ],
      'facebook_no_script',
    ];

  }

}
