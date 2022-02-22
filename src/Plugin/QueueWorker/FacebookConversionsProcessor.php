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
  public function processItem($data) {
    $api = new FacebookConversionsApi();
    $api->addEvent($data);
    $api->sendEvents();
  }

}
