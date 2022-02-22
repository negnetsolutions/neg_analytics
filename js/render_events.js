(function () {

  if (typeof drupalSettings === 'undefined' || typeof drupalSettings.neg_analytics === 'undefined' || typeof drupalSettings.neg_analytics.events === 'undefined') {
    console.debug("Skipping Events");
    return;
  }

  // Run Events.
  eval(drupalSettings.neg_analytics.events);
})();
