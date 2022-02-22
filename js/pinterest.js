(function () {

  if (typeof drupalSettings === 'undefined' || typeof drupalSettings.neg_analytics === 'undefined' || typeof drupalSettings.neg_analytics.pinterest === 'undefined') {
    console.debug("Skipping Pinterest Analytics");
    return;
  }

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

    this.processEvent = function processEvent(eventId, event, details) {
      let items = [];

      switch (event) {
        case 'search':
          for (let i = 0; i < details.items.length; i++) {
            let item = details.items[i];
            items.push(item.sku); 
          }

          pintrk('track', 'Search', {
            'search_query': details.search_query,
            'product_id': items
          });
          break;

        case 'view_item_list':
          break;

        case 'view_item':
          for (let i = 0; i < details.items.length; i++) {
            let item = details.items[i];
            items.push(item.sku); 
          }

          pintrk('track', 'PageVisit', {
            'product_id': items
          });

          break;

        case 'addToCart':
          pintrk('track', 'AddToCart', {
            line_items: [
              {
                product_id: details.sku,
                product_variant_id: details.variant_id,
                product_price: details.price,
                product_brand: (typeof details.brand !== 'undefined') ? details.brand : null,
                product_name: (typeof details.name !== 'undefined') ? details.name : null,
                product_category: (typeof details.category !== 'undefined') ? details.category : null,
                product_quantity: details.qty
              }
            ]
          });
          break;

        case 'purchase':
          items = [];
          let qty = 0;

          for (let i = 0; i < details.items.length; i++) {
            let item = details.items[i];
            items.push({
              product_id: item.sku,
              product_variant_id: item.variant_id,
              product_quantity: item.qty,
              product_brand: (typeof details.brand !== 'undefined') ? details.brand : null,
              product_name: (typeof details.name !== 'undefined') ? details.name : null,
              product_category: (typeof details.category !== 'undefined') ? details.category : null,
              product_price: item.price
            });
            qty += item.qty;
          }

          pintrk('track', 'Checkout', {
            order_id: details.order_number,
            order_quantity: qty,
            value: details.value,
            currency: details.currency,
            line_items: items
          });
          break;
      }

    };
  });

})();
