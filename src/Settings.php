<?php

namespace Drupal\neg_analytics;

/**
 * Analytics Settings.
 */
class Settings {

  const CONFIGNAME = 'neg_analytics.settings';

  /**
   * Gets a config object.
   */
  public static function config() {
    return \Drupal::config(self::CONFIGNAME);
  }

  /**
   * Gets an editable config object.
   */
  public static function editableConfig() {
    return \Drupal::service('config.factory')->getEditable(self::CONFIGNAME);
  }

}
