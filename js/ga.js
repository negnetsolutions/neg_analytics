// Start Google Analytics.
if (drupalSettings.neg_analytics.google.customcode) {
  eval(drupalSettings.neg_analytics.google.customcode);
}
else {
  window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', drupalSettings.neg_analytics.google.measurementId);
}

// Register events.
events.registerHandler(new function ga() {
  const _ = this;

  this.processEvent = function processEvent(event, details) {
    switch (event) {
      case 'view_item_list':
        gtag('event', 'view_item_list', {
          items: details.items
        });
        break;

      case 'removeFromCart':
        gtag('event', 'remove_from_cart', {
          items: [{
            'id': details.product_id,
            'variant': details.sku,
            'price': details.price,
            'quantity': details.qty,
          }]
        });
        break;

      case 'addToCart':
        gtag('event', 'add_to_cart', {
          items: [{
            'id': details.product_id,
            'variant': details.sku,
            'price': details.price,
            'quantity': details.qty,
          }]
        });
        break;

      case 'checkout':
        let items = [];
        for (let i = 0; i < details.items.length; i++) {
          let item = details.items[i];
          items.push({
            'id': item.product_id,
            'variant': item.sku,
            'price': item.price,
            'quantity': item.qty
          });
        }

        gtag('event', 'begin_checkout', {
          items: items
        });
        break;
    }

  };
});

// Send Events.
if (drupalSettings.neg_analytics.google.events) {
  eval(drupalSettings.neg_analytics.google.events);
}

