<?php

namespace Drupal\neg_analytics\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\neg_shopify\Event\LoadMultipleProductsViewEvent;
use Drupal\neg_analytics\Handlers\GoogleAnalytics;

/**
 * Handles product view events.
 */
class LoadMultipleProductViewsSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   *
   * @return array
   *   The event names to listen for, and the methods that should be executed.
   */
  public static function getSubscribedEvents() {
    return [
      'neg_shopify_postprocess_loadmultipleproductsview' => [
        'postProcessProductViews',
        100,
      ],
    ];
  }

  /**
   * React to a product views event.
   *
   * @param \Drupal\neg_shopify\Event\LoadMultipleProductsViewEvent $event
   *   LoadMultipleProductsViewEvent event.
   */
  public function postProcessProductViews(LoadMultipleProductsViewEvent $event) {

    if ($event->defaultContext === FALSE) {
      // Attempt to add analytics events.
      $items = [];
      foreach ($event->products as $product) {
        $impression = $product->getAnalyticsDetails();
        if ($impression) {
          unset($impression['#tags']);
          $items[] = $impression;
        }
      }

      if (count($items) > 0) {
        $items = json_encode($items);
        $script = <<<EOL
  <script>
  if (typeof events === 'object') {
    events.triggerEvent('view_item_list', {'items': {$items}});
  }
  </script>
EOL;
        $event->view[] = $script;
      }
    }

  }

}
