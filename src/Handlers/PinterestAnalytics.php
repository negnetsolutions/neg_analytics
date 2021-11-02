<?php

namespace Drupal\neg_analytics\Handlers;

/**
 * Pinterest Handler.
 */
class PinterestAnalytics extends BaseHandler {

  protected $library = 'neg_analytics/pinterest_analytics';

  /**
   * Renders base GA code.
   */
  protected function renderBaseCode() {

    $email = hash('sha256', \Drupal::currentUser()->getEmail());

    // Include Pinterest scripts.
    $this->attachments['#attached']['html_head'][] = [
      [
        '#type' => 'html_tag',
        '#tag' => 'link',
        '#value' => '',
        '#attributes' => [
          'href' => 'https://s.pinimg.com',
          'rel' => 'preconnect',
        ],
      ],
      'pinterest_analytics_preconnect',
    ];

    $this->attachments['#attached']['drupalSettings']['neg_analytics']['pinterest']['measurementId'] = $this->measurementId;
    $this->attachments['#attached']['drupalSettings']['neg_analytics']['pinterest']['em'] = $email;

    $this->attachments['#attached']['html_head'][] = [
      [
        '#type' => 'html_tag',
        '#tag' => 'img',
        '#noscript' => TRUE,
        '#attributes' => [
          'height' => 1,
          'width' => 1,
          'style' => 'display: none;',
          'src' => "https://ct.pinterest.com/v3/?tid={$this->measurementId}&pd[em]={$email}&noscript=",
        ],
      ],
      'pinterest_no_script',
    ];

  }

}
