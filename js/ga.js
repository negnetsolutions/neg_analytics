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
  let items;

  this.processEvent = function processEvent(event, details) {
    switch (event) {
      case 'view_item':
        gtag('event', 'view_item', {
          items: [details]
        });
        break;

      case 'view_item_list':
        gtag('event', 'view_item_list', {
          items: details.items
        });
        break;

      case 'removeFromCart':
        gtag('event', 'remove_from_cart', {
          items: [{
            'id': details.sku,
            'price': details.price,
            'quantity': details.qty,
            'brand': (typeof details.brand !== 'undefined') ? details.brand : null,
            'name': (typeof details.name !== 'undefined') ? details.name : null
          }]
        });
        break;

      case 'addToCart':
        gtag('event', 'add_to_cart', {
          items: [{
            'id': details.sku,
            'price': details.price,
            'quantity': details.qty,
            'brand': (typeof details.brand !== 'undefined') ? details.brand : null,
            'name': (typeof details.name !== 'undefined') ? details.name : null
          }]
        });
        break;

      case 'checkout':
        items = _.fetchCheckoutItems(details.items);

        gtag('event', 'begin_checkout', {
          "transaction_id": details.order_number,
          items: items
        });
        break;

      case 'checkout_progress':
        items = _.fetchCheckoutItems(details.items);

        gtag('event', 'checkout_progress', {
          "transaction_id": details.order_number,
          items: items
        });

        gtag('event', 'set_checkout_option', {
          "checkout_step": details.checkout_step,
          "checkout_option": details.checkout_option
        });

        break;

      case 'purchase':
        items = _.fetchCheckoutItems(details.items);

        gtag('event', 'purchase', {
          "transaction_id": details.order_number,
          "affiliation": details.affiliation,
          "value": details.value,
          "currency": details.currency,
          "tax": details.tax,
          "shipping": details.shipping,
          "items": items
        });
        break;
    }

  };

  this.fetchCheckoutItems = function fetchCheckoutItems(detailItems) {
    let items = [];
    for (let i = 0; i < detailItems.length; i++) {
      let item = detailItems[i];
      items.push({
        'id': item.sku,
        'price': item.price,
        'quantity': item.qty,
        'brand': (typeof item.brand !== 'undefined') ? item.brand : null,
        'name': (typeof item.name !== 'undefined') ? item.name : null
      });
    }

    return items;
  };

});

// Send Events.
if (typeof drupalSettings.neg_analytics.google.events !== 'undefined') {
  eval(drupalSettings.neg_analytics.google.events);
}
