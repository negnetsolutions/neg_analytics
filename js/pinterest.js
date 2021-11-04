window.addEventListener('DOMContentLoaded', function() {
// Start Pinterest Pixel.
!function(e){if(!window.pintrk){window.pintrk = function () {
window.pintrk.queue.push(Array.prototype.slice.call(arguments))};var
  n=window.pintrk;n.queue=[],n.version="3.0";var
  t=document.createElement("script");t.async=!0,t.src=e;var
  r=document.getElementsByTagName("script")[0];
  r.parentNode.insertBefore(t,r)}}("https://s.pinimg.com/ct/core.js");
pintrk('load', drupalSettings.neg_analytics.pinterest.measurementId, {em: drupalSettings.neg_analytics.pinterest.em});
pintrk('page');

// Register events.
events.registerHandler(new function ga() {
  const _ = this;

  this.processEvent = function processEvent(event, details) {
    switch (event) {
      case 'addToCart':
        pintrk('track', 'AddToCart', {
          line_items: [
            {
              product_id: details.product_id,
              product_variant_id: details.variant_id,
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
            product_id: item.product_id,
            product_variant_id: item.variant_id,
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
});
