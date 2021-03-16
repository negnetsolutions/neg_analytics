const NegEvents = function events() {

  const _ = this;
  this.handlers = [];

  this.registerHandler = function registerHandler(handler) {
    _.handlers.push(handler);
  };

  this.triggerEvent = function triggerEvent(event, details, callback) {
    switch (event) {
      case 'addToCart':
        if (typeof details.qty == 'undefined') {
          // Try to find actual quantity added.
          const form = document.querySelector('.shopify-add-to-cart-form');
          if (form) {
            const qty = form.querySelector('.form-item-quantity input').value
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

    for (let i = 0; i < _.handlers.length; i++) {
      _.handlers[i].processEvent(event, details);
    }

    if (typeof callback === "function") {
      return callback();
    }

    return false;
  };

};

const events = new NegEvents();
