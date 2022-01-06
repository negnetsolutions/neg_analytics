// Start Facebook Pixel.
!function(f,b,e,v,n,t,s) {if(f.fbq)return;n=f.fbq=function(){n.callMethod? n.callMethod.apply(n,arguments):n.queue.push(arguments)}; if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0'; n.queue=[];t=b.createElement(e);t.async=!0; t.src=v;s=b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t,s)}(window, document,'script', 'https://connect.facebook.net/en_US/fbevents.js'); fbq('init', drupalSettings.neg_analytics.facebook.measurementId); fbq('track', 'PageView');

// Register events.
events.registerHandler(new function ga() {
  const _ = this;
  let items = [];
  let qty = 0;

  this.processEvent = function processEvent(event, details) {
    switch (event) {
      case 'view_item':
        fbq('track', 'ViewContent', {content_ids: [details.id]});
        break;

      case 'view_item_list':
        items = [];
        for (let i = 0; i < details.items.length; i++) {
          let item = details.items[i];
          items.push(item.id);
        }

        fbq('track', 'ViewContent', {content_ids: items});
        break;

      case 'addToCart':
        fbq('track', 'AddToCart', {content_ids: [details.sku]});
        break;

      case 'checkout':
        items = [];
        qty = 0;
        for (let i = 0; i < details.items.length; i++) {
          let item = details.items[i];
          items.push(item.sku);
          qty += item.qty;
        }

        fbq('track', 'InitiateCheckout', {content_ids: items, num_items: qty});
        break;

      case 'purchase':
        items = [];
        qty = 0;
        for (let i = 0; i < details.items.length; i++) {
          let item = details.items[i];
          items.push(item.sku);
          qty += item.qty;
        }

        fbq('track', 'Purchase', {content_ids: items, num_items: qty, value: details.value, currency: details.currency});
        break;
    }

  };
});

// Send Events.
if (typeof drupalSettings.neg_analytics.facebook.events !== 'undefined') {
  eval(drupalSettings.neg_analytics.facebook.events);
}
