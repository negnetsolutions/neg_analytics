<?php

namespace Drupal\neg_analytics\Handlers;

use Drupal\Core\Url;

/**
 * Facebook Handler.
 */
class FacebookAnalytics extends BaseHandler {

  /**
   * {@inheritdoc}
   */
  protected $library = 'neg_analytics/facebook_analytics';

  /**
   * {@inheritdoc}
   */
  protected $apiToken = NULL;

  /**
   * Implements __construct().
   */
  public function __construct($measurementId, $apiToken = NULL) {
    parent::__construct($measurementId);

    // Set the api token for the conversions api.
    if (strlen($apiToken) > 0) {
      $this->apiToken = $apiToken;
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

    // Set the conversions api endpoint if we have an api token.
    if ($this->apiToken !== NULL) {
      $this->attachments['#attached']['drupalSettings']['neg_analytics']['track_url'] = Url::fromRoute('neg_analytics.conversions_api.track')->toString();
    }
  }

}
