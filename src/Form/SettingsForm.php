<?php

namespace Drupal\neg_analytics\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\neg_analytics\Settings;

/**
 * Settings for Analytics.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'analytics_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      Settings::CONFIGNAME,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = Settings::config();

    $form['ga_measurement_id'] = [
      '#type' => 'textfield',
      '#title' => t('GA Measurement ID'),
      '#default_value' => $config->get('ga_measurement_id'),
      '#description' => t('Enter your Google Analytics Measurement ID'),
      '#required' => FALSE,
    ];

    $form['facebook_pixel'] = [
      '#type' => 'textfield',
      '#title' => t('Facebook Pixel ID'),
      '#default_value' => $config->get('facebook_pixel'),
      '#description' => t('Enter your Facebook Pixel ID'),
      '#required' => FALSE,
    ];

    $form['pinterest_tag_id'] = [
      '#type' => 'textfield',
      '#title' => t('Pinterest Tag ID'),
      '#default_value' => $config->get('pinterest_tag_id'),
      '#description' => t('Enter your pinterest tag ID'),
      '#required' => FALSE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration.
    $config = Settings::editableConfig();

    $config->set('ga_measurement_id', $form_state->getValue('ga_measurement_id'));
    $config->set('pinterest_tag_id', $form_state->getValue('pinterest_tag_id'));
    $config->set('facebook_pixel', $form_state->getValue('facebook_pixel'));

    $config->save();

    parent::submitForm($form, $form_state);
  }

}
