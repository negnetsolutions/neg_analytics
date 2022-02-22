<?php

namespace Drupal\neg_analytics\Handlers;

use Drupal\neg_analytics\Settings;

/**
 * GA Handler.
 */
class GoogleAnalytics extends BaseHandler {

  /**
   * {@inheritdoc}
   */
  protected $library = 'neg_analytics/google_analytics';

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

}
