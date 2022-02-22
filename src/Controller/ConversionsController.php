<?php

namespace Drupal\neg_analytics\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\neg_analytics\Api\FacebookConversionsApi;

/**
 * Class ConversionsController.
 */
class ConversionsController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  protected const QUEUE = 'neg_analytics_facebook_conversions';

  /**
   * {@inheritdoc}
   */
  public function track() {
    $content = \Drupal::request()->getContent();

    if (empty($content)) {
      throw new NotFoundHttpException();
    }

    $data = json_decode($content, TRUE);

    if (!$data) {
      throw new NotFoundHttpException();
    }

    $this->processData($data);
    $this->sendItem($data);

    return new JsonResponse('OK');
  }

  /**
   * {@inheritdoc}
   */
  protected function processData(&$data) {
    $data['user_data']['client_ip_address'] = \Drupal::request()->getClientIp();

    $email = \Drupal::currentUser()->getEmail();
    if (strlen($email) > 0) {
      $data['user_data']['em'] = [hash('sha256', $email)];
    }

    $fbp = \Drupal::request()->cookies->get('_fbp');
    if ($fbp) {
      $data['user_data']['fbp'] = $fbp;
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function sendItem($data) {
    // Add the data to the queue.
    $queue_factory = \Drupal::service('queue');
    $queue = $queue_factory->get(self::QUEUE);

    $queue_manager = \Drupal::service('plugin.manager.queue_worker');
    $worker = $queue_manager->createInstance(self::QUEUE);

    // Add the item to the queue.
    $queue->createItem($data);

    // Attempt to push the event to facebook.
    $item = $queue->claimItem();
    if ($item) {
      try {
        $worker->processItem($item->data);
        $queue->deleteItem($item);
      }
      catch (\Exception $e) {
        \Drupal::logger('neg_analytics')->error('Could not process conversion request: %m', [
          '%m' => $e->getMessage(),
        ], 'error');
        $queue->releaseItem($item);
      }
    }

    return TRUE;
  }

}
