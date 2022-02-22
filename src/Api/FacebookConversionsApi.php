<?php

namespace Drupal\neg_analytics\Api;

use Drupal\neg_analytics\Settings;

/**
 * Facebook Conversions API.
 */
class FacebookConversionsApi {

  /**
   * {@inheritdoc}
   */
  protected $endpoint = 'https://graph.facebook.com/';

  /**
   * {@inheritdoc}
   */
  protected $apiVersion = 'v13.0';

  /**
   * {@inheritdoc}
   */
  protected $pixelId = NULL;

  /**
   * {@inheritdoc}
   */
  protected $apiToken = NULL;

  /*
   * {@inheritdoc}
   */
  protected $events = [];

  /*
   * {@inheritdoc}
   */
  public function __construct() {
    $config = Settings::config();
    $this->apiToken = $config->get('facebook_api_token');
    $this->pixelId = $config->get('facebook_pixel');
  }

  /**
   * {@inheritdoc}
   */
  protected function getEndpointUrl($endpoint) {
    return $this->endpoint . $this->apiVersion . '/' . $this->pixelId . '/' . $endpoint . '?access_token=' . $this->apiToken;
  }

  /**
   * {@inheritdoc}
   */
  public function addEvent($data) {
    $this->events[] = $data;
  }

  /**
   * {@inheritdoc}
   */
  public function sendEvents() {
    if (count($this->events) === 0) {
      return FALSE;
    }

    $request = [
      'data' => $this->events,
    ];

    return $this->request('events', $request);
  }

  /**
   * {@inheritdoc}
   */
  public function request($endpoint, $data = '') {
    $client = \Drupal::httpClient();

    $headers = [
      'headers' => [
        'content-type' => 'application/json',
      ],
    ];

    $headers['json'] = $data;

    $request = $client->post($this->getEndpointUrl($endpoint), $headers);
    $response = $request->getBody()->getContents();

    $data = json_decode($response, TRUE);

    \Drupal::logger('neg_analytics')->debug("<pre><code>Query: \n@query \nResponse: \n @resp</code></pre>", [
      '@query' => $data,
      '@resp' => $response,
    ]);

    return $data;
  }

}
