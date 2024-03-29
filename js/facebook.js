(function () {

  if (typeof drupalSettings === 'undefined' || typeof drupalSettings.neg_analytics === 'undefined' || typeof drupalSettings.neg_analytics.facebook === 'undefined') {
    console.debug("Skipping Facebook Analytics");
    return;
  }

  // Start Facebook Pixel.
  !function(f,b,e,v,n,t,s) {if(f.fbq)return;n=f.fbq=function(){n.callMethod? n.callMethod.apply(n,arguments):n.queue.push(arguments)}; if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0'; n.queue=[];t=b.createElement(e);t.async=!0; t.src=v;s=b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t,s)}(window, document,'script', 'https://connect.facebook.net/en_US/fbevents.js'); fbq('init', drupalSettings.neg_analytics.facebook.measurementId, {em: drupalSettings.neg_analytics.facebook.em});

  // Register events.
  events.registerHandler(new function facebook() {
    const _ = this;
    this.useBeacon = (typeof navigator.sendBeacon === 'function') ? true : false;
    this.conversionQueue = [];

    this.apiUrl = (typeof drupalSettings.neg_analytics.track_url !== 'undefined') ? drupalSettings.neg_analytics.track_url : null;

    this.send = function send(eventId, event, details) {
      fbq('track', event, details, {eventID: eventId});
      _.sendToTrackingUrl(eventId, event, details);
    };

    this.unload = function unload() {
      if (_.useBeacon && _.conversionQueue.length > 0) {
        const jsonPayload = JSON.stringify(_.conversionQueue);
        navigator.sendBeacon(_.apiUrl, jsonPayload);
        // Reset queue.
        _.conversionQueue = [];
      }
    };

    this.sendToTrackingUrl = function sendToTrackingUrl(eventId, event, details) {
      if (_.apiUrl) {

        const payload = {
          'event_name': event,
          'event_time': Math.floor(Date.now() / 1000),
          'event_id': eventId,
          'event_source_url': window.location.href,
          'action_source': 'website',
          'user_data': {
            'client_user_agent': navigator.userAgent
          },
          'custom_data': details
        };

        if (_.useBeacon) {
          _.conversionQueue.push(payload);
        }
        else {
          const xhr = new XMLHttpRequest();
          const jsonPayload = JSON.stringify([payload]);
          xhr.open('POST', _.apiUrl);
          xhr.setRequestHeader('Content-Type', 'application/json');
          xhr.send(jsonPayload);
        }
      }
    };

    this.processEvent = function processEvent(eventId, event, details) {
      let items = [];
      let qty = 0;

      switch (event) {
        case 'search':
          for (let i = 0; i < details.items.length; i++) {
            let item = details.items[i];
            items.push(item.sku); 
          }

          _.send(eventId, 'Search', {search_string: details.search_query, content_ids: items});
          break;

        case 'view_item':
          for (let i = 0; i < details.items.length; i++) {
            let item = details.items[i];
            items.push(item.sku); 
          }

          _.send(eventId, 'ViewContent', {content_ids: items});
          break;

        case 'view_item_list':
          for (let i = 0; i < details.items.length; i++) {
            let item = details.items[i];
            items.push(item.sku);
          }

          _.send(eventId, 'ViewContent', {content_ids: items});
          break;

        case 'addToCart':
          _.send(eventId, 'AddToCart', {content_ids: [details.sku]});
          break;

        case 'checkout':
          items = [];
          qty = 0;
          for (let i = 0; i < details.items.length; i++) {
            let item = details.items[i];
            items.push(item.sku);
            qty += item.qty;
          }

          _.send(eventId, 'InitiateCheckout', {content_ids: items, num_items: qty});
          break;

        case 'purchase':
          items = [];
          qty = 0;
          for (let i = 0; i < details.items.length; i++) {
            let item = details.items[i];
            items.push(item.sku);
            qty += item.qty;
          }

          _.send(eventId, 'Purchase', {content_ids: items, num_items: qty, value: details.value, currency: details.currency});
          break;
      }

    };

    // Track page view.
    _.send("PageView_" + Math.floor(Math.random() * Date.now()), 'PageView');

    // Add event listeners.
    window.addEventListener('beforeunload', _.unload, false);
    window.addEventListener('pagehide', _.unload, false);
  });

})();
