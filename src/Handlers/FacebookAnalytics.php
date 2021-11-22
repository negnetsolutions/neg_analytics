<?php

namespace Drupal\neg_analytics\Handlers;

/**
 * Facebook Handler.
 */
class FacebookAnalytics extends BaseHandler {

  protected $library = 'neg_analytics/facebook_analytics';

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
