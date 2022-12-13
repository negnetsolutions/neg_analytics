<?php

namespace Drupal\neg_analytics\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\neg_analytics\Api\PinterestConversionsApi;

/**
 *
 * @QueueWorker(
 * id = "neg_analytics_pinterest_conversions",
 * title = "Pinterest Conversions API Queue",
 * cron = {"time" = 60}
 * )
 */
class PinterestConversionsProcessor extends QueueWorkerBase {

  /**
   * Processes a queue item.
   */
  public function processItem($events) {
    $api = new PinterestConversionsApi();

    if (!$api->isConfigured()) {
      // Skip if conversion tracking isn't configured.
      \Drupal::logger('neg_analytics')->notice("<pre><code>Pinterest Conversions API: Skipping due to not being configured</code></pre>");
      return TRUE;
    }

    // Split events into chunks of 1000 events (max per fb request).
    $chunks = array_chunk($events, 1000);

    foreach ($chunks as $events) {
      // Check for a single event.
      if (isset($events['event_name'])) {
        $api->addEvent($events);
      }
      else {
        // Queue multiple events.
        foreach ($events as $event) {
          $api->addEvent($event);
        }
      }

      $api->sendEvents();
    }
  }

}
