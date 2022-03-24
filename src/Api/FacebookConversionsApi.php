<?php

namespace Drupal\neg_analytics\Api;

use Drupal\neg_analytics\Settings;
use GuzzleHttp\Exception\RequestException;

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
    $this->apiToken = trim($config->get('facebook_api_token'));
    $this->pixelId = trim($config->get('facebook_pixel'));
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

    try {
      $request = $client->post($this->getEndpointUrl($endpoint), $headers);
      $response = $request->getBody()->getContents();

      $data = json_decode($response, TRUE);
    }
    catch (RequestException $e) {

      $logMessage = NULL;

      if ($e->hasResponse()) {
        $response = $e->getResponse();
        $json_error = json_decode((string) $response->getBody());
        if ($json_error) {

          if ($json_error->error->error_user_title === 'Event Timestamp Too Old') {
            // We can just throw away this queue item. It can never be processed.
            return $json_error;
          }

          $logMessage = $json_error->error->error_user_msg;
        }
      }

      if (!$logMessage) {
        $logMessage = $e->getMessage();
      }

      \Drupal::logger('neg_analytics')->error("<pre><code>Facebook Conversions API Error:\n@error\nQuery: \n@query</code></pre>", [
        '@error' => $logMessage,
        '@query' => json_encode($data),
      ]);

      throw new \Exception($e->getMessage());
    }

    return $data;
  }

}
