<?php

namespace Drupal\neg_analytics\Handlers;

/**
 * GA Handler.
 */
class GoogleAnalytics extends BaseHandler {

  protected $library = 'neg_analytics/google_analytics';

  /**
   * Renders product impressions.
   */
  protected function renderImpressions() {
    $tags = $attachments['#cache']['tags'];

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

    $this->attachments['#attached']['html_head'][] = [
      [
        '#type' => 'html_tag',
        '#tag' => 'script',
        '#value' => $code,
        '#attributes' => [],
      ],
      'google_analytics_events',
    ];
  }

  /**
   * Renders base GA code.
   */
  protected function renderBaseCode() {

    // Include GA scripts.
    $this->attachments['#attached']['html_head'][] = [
      [
        '#type' => 'html_tag',
        '#tag' => 'script',
        '#value' => '',
        '#attributes' => [
          'async' => 'true',
          'src' => 'https://www.googletagmanager.com/gtag/js?id=' . $this->measurementId,
        ],
      ],
      'google_analytics',
    ];

    $this->attachments['#attached']['html_head'][] = [
      [
        '#type' => 'html_tag',
        '#tag' => 'script',
        '#value' => "window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', '{$this->measurementId}');",
        '#attributes' => [],
      ],
      'google_analytics_inline',
    ];

  }

  /**
   * Renders GA View items.
   */
  protected function renderProducts($items) {
    $views = [];
    $tags = [];
    foreach ($items as $product) {
      $view = $product->getGoogleAnalyticsImpression();

      $view = $product->getGoogleAnalyticsImpression();
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
