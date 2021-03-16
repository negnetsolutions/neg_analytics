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
        '#tag' => 'script',
        '#value' => "!function(e){if(!window.pintrk){window.pintrk = function () { window.pintrk.queue.push(Array.prototype.slice.call(arguments))};var n=window.pintrk;n.queue=[],n.version=\"3.0\";var t=document.createElement(\"script\");t.async=!0,t.src=e;var r=document.getElementsByTagName(\"script\")[0]; r.parentNode.insertBefore(t,r)}}(\"https://s.pinimg.com/ct/core.js\"); pintrk('load', '{$this->measurementId}', {em: '{$email}'}); pintrk('page');",
        '#attributes' => [],
      ],
      'pinterest_analytics_inline',
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
          'src' => "https://ct.pinterest.com/v3/?tid={$this->measurementId}&pd[em]={$email}&noscript=",
        ],
      ],
      'pinterest_no_script',
    ];

  }

}
