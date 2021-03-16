events.registerHandler(new function ga() {
  const _ = this;

  this.processEvent = function processEvent(event, details) {
    switch (event) {
      case 'removeFromCart':
        dataLayer.push({
          'event': 'remove_from_cart',
          'ecommerce': [{
            'item_id': details.sku,
            'price': details.price,
            'quantity': details.qty,
          }]
        });
        break;

      case 'addToCart':
        dataLayer.push({
          'event': 'add_to_cart',
          'ecommerce': [{
            'item_id': details.sku,
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
            'item_id': item.sku,
            'quantity': item.qty
          });
        }

        dataLayer.push({
          'event': 'begin_checkout',
          'ecommerce': [{
            'items': items,
          }]
        });
        break;
    }

  };
});
