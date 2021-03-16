events.registerHandler(new function ga() {
  const _ = this;

  this.processEvent = function processEvent(event, details) {
    switch (event) {
      case 'addToCart':
        pintrk('track', 'AddToCart');
        break;

      case 'checkout':
        let items = [];
        let qty = 0;
        for (let i = 0; i < details.items.length; i++) {
          let item = details.items[i];
          items.push({
            'product_id': item.sku,
            'product_quantity': item.qty
          });
          qty += item.qty;
        }

        pintrk('track', 'checkout', {
          order_quantity: qty,
          line_items: items
        });
        break;
    }

  };
});
