<?php

namespace Drupal\neg_analytics\Api;

use Drupal\neg_analytics\Settings;
use GuzzleHttp\Exception\RequestException;

/**
 * Pinterest Conversions API.
 */
class PinterestConversionsApi {

  /**
   * {@inheritdoc}
   */
  protected $endpoint = 'https://api.pinterest.com/';

  /**
   * {@inheritdoc}
   */
  protected $apiVersion = 'v5';

  /**
   * {@inheritdoc}
   */
  protected $accountId = NULL;

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
  protected $testMode = FALSE;

  /*
   * {@inheritdoc}
   */
  public function __construct() {
    $config = Settings::config();
    $this->apiToken = trim($config->get('pinterest_conversion_token'));
    $this->accountId = trim($config->get('pinterest_account_id'));
  }

  /**
   * {@inheritdoc}
   */
  protected function getEndpointUrl($endpoint) {
    return $this->endpoint . $this->apiVersion . '/';
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

    return $this->request('ad_accounts/'.$this->accountId.'/events', $request);
  }

  /**
   * {@inheritdoc}
   */
  public function request($endpoint, $data = '') {
    $client = \Drupal::httpClient();

    if ($this->testMode === TRUE) {
      $endpoint .= '?test=true';
    }

    $headers = [
      'headers' => [
        'content-type' => 'application/json',
        'Authorization' => 'Bearer '.$this->apiToken,
      ],
    ];

    $headers['json'] = $data;

    \Drupal::logger('neg_analytics')->error("<pre><code>Pinterest Conversions API Call:\n@endpoint\nQuery: \n@query</code></pre>", [
      '@endpoint' => $endpoint,
      '@query' => json_encode($headers),
    ]);

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

        if (isset($json_error->code)) {
          $logMessage = $json_error->message;
        }

      }

      if (!$logMessage) {
        $logMessage = $e->getMessage();
      }

      \Drupal::logger('neg_analytics')->error("<pre><code>Pinterest Conversions API Error:\n@error\nQuery: \n@query</code></pre>", [
        '@error' => $logMessage,
        '@query' => json_encode($data),
      ]);

      throw new \Exception($e->getMessage());
    }

    return $data;
  }

}
