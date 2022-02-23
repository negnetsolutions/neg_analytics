<?php

namespace Drupal\neg_analytics\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\neg_analytics\Api\FacebookConversionsApi;

/**
 *
 * @QueueWorker(
 * id = "neg_analytics_facebook_conversions",
 * title = "Facebook Conversions API Queue",
 * cron = {"time" = 60}
 * )
 */
class FacebookConversionsProcessor extends QueueWorkerBase {

  /**
   * Processes a queue item.
   */
  public function processItem($events) {
    $api = new FacebookConversionsApi();

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
