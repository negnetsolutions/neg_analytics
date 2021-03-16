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
        '#tag' => 'script',
        '#value' => "!function(f,b,e,v,n,t,s) {if(f.fbq)return;n=f.fbq=function(){n.callMethod? n.callMethod.apply(n,arguments):n.queue.push(arguments)}; if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0'; n.queue=[];t=b.createElement(e);t.async=!0; t.src=v;s=b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t,s)}(window, document,'script', 'https://connect.facebook.net/en_US/fbevents.js'); fbq('init', '{$this->measurementId}'); fbq('track', 'PageView');",
        '#attributes' => [],
      ],
      'facebook_analytics_inline',
    ];

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
