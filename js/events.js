const NegEvents = function events() {

  const _ = this;
  this.handlers = [];

  this.registerHandler = function registerHandler(handler) {
    _.handlers.push(handler);
  };

  this.getEventId = function getEventId(event) {
    return event + "_" + Math.floor(Math.random() * Date.now());
  };

  this.triggerEvent = function triggerEvent(event, details, callback) {
    switch (event) {
      case 'addToCart':
        if (typeof details.qty == 'undefined') {
          // Try to find actual quantity added.
          const form = document.querySelector('.shopify-add-to-cart-form[data-variant-id="' + details.variant_id + '"]');
          if (form) {
            const qty = form.querySelector('[name="quantity"]').value
            if (qty) {
              details.qty = qty;
            }
          }
          else {
            details.qty = 1;
          }
        }
        break;
    }

    // Generate an event id.
    const eventId = _.getEventId(event);

    for (let i = 0; i < _.handlers.length; i++) {
      _.handlers[i].processEvent(eventId, event, details);
    }

    if (typeof callback === "function") {
      return callback();
    }

    return false;
  };

};

const events = new NegEvents();
