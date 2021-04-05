events.registerHandler(new function ga() {
  const _ = this;

  this.processEvent = function processEvent(event, details) {
    switch (event) {
      case 'addToCart':
        pintrk('track', 'AddToCart', {
          line_items: [
            {
              product_id: details.sku,
              product_price: details.price,
              product_quantity: details.qty
            }
          ]
        });
        break;

      case 'checkout':
        let items = [];
        let qty = 0;
        let total = 0;

        for (let i = 0; i < details.items.length; i++) {
          let item = details.items[i];
          total += item.price;
          items.push({
            product_id: item.sku,
            product_quantity: item.qty,
            product_price: item.price
          });
          qty += item.qty;
        }

        pintrk('track', 'checkout', {
          order_quantity: qty,
          value: total,
          line_items: items
        });
        break;
    }

  };
});
