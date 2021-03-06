events.registerHandler(new function ga() {
  const _ = this;

  this.processEvent = function processEvent(event, details) {
    switch (event) {
      case 'addToCart':
        fbq('track', 'AddToCart', {content_ids: [details.sku]});
        break;

      case 'checkout':
        let items = [];
        let qty = 0;
        for (let i = 0; i < details.items.length; i++) {
          let item = details.items[i];
          items.push(items.sku);
          qty += item.qty;
        }

        fbq('track', 'InitiateCheckout', {content_ids: items, num_items: qty});
        break;
    }

  };
});
