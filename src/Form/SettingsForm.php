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

    $form['google_analytics'] = [
      '#type' => 'details',
      '#title' => t('Google Analytics'),
      '#open' => TRUE,
    ];

    $form['google_analytics']['ga_enabled'] = [
      '#type' => 'checkbox',
      '#title' => t('Enable Google Analytics?'),
      '#default_value' => $config->get('ga_enabled'),
    ];

    $form['google_analytics']['ga_measurement_id'] = [
      '#type' => 'textfield',
      '#title' => t('GA Measurement ID'),
      '#default_value' => $config->get('ga_measurement_id'),
      '#description' => t('Enter your Google Analytics Measurement ID'),
      '#required' => FALSE,
    ];

    $form['google_analytics']['ga_custom_code'] = [
      '#type' => 'textarea',
      '#title' => t('GA Custom Tracking Code'),
      '#default_value' => $config->get('ga_custom_code'),
      '#description' => t('Optionally enter datalayer portion of analytics tracking code.'),
      '#required' => FALSE,
    ];

    $form['facebook'] = [
      '#type' => 'details',
      '#title' => t('Facebook Pixel'),
      '#open' => TRUE,
    ];

    $form['facebook']['pixel_enabled'] = [
      '#type' => 'checkbox',
      '#title' => t('Enable Facebook Pixel?'),
      '#default_value' => $config->get('pixel_enabled'),
    ];

    $form['facebook']['facebook_pixel'] = [
      '#type' => 'textfield',
      '#title' => t('Facebook Pixel ID'),
      '#default_value' => $config->get('facebook_pixel'),
      '#description' => t('Enter your Facebook Pixel ID'),
      '#required' => FALSE,
    ];

    $form['facebook']['facebook_api_token'] = [
      '#type' => 'textarea',
      '#title' => t('Conversions API Token'),
      '#default_value' => $config->get('facebook_api_token'),
      '#description' => t('Enter your Facebook Conversions API Token to enable the conversions api.'),
      '#required' => FALSE,
    ];

    $form['pinterest'] = [
      '#type' => 'details',
      '#title' => t('Pinterest'),
      '#open' => TRUE,
    ];

    $form['pinterest']['pinterest_enabled'] = [
      '#type' => 'checkbox',
      '#title' => t('Enable Pinterest Tracker?'),
      '#default_value' => $config->get('pinterest_enabled'),
    ];

    $form['pinterest']['pinterest_tag_id'] = [
      '#type' => 'textfield',
      '#title' => t('Pinterest Tag ID'),
      '#default_value' => $config->get('pinterest_tag_id'),
      '#description' => t('Enter your pinterest tag ID'),
      '#required' => FALSE,
    ];

    $form['debug'] = [
      '#type' => 'details',
      '#title' => t('Debug Options'),
    ];

    $form['debug']['filter_enabled'] = [
      '#type' => 'checkbox',
      '#title' => t('Enable Domain Filter?'),
      '#default_value' => $config->get('filter_enabled'),
    ];

    $form['debug']['filter_domain'] = [
      '#type' => 'textfield',
      '#title' => t('Filter Domains'),
      '#default_value' => $config->get('filter_domain'),
      '#description' => t('Enter a development domain where analytics should be disabled.'),
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

    $config->set('ga_enabled', $form_state->getValue('ga_enabled'));
    $config->set('ga_measurement_id', $form_state->getValue('ga_measurement_id'));
    $config->set('ga_custom_code', $form_state->getValue('ga_custom_code'));
    $config->set('pinterest_enabled', $form_state->getValue('pinterest_enabled'));
    $config->set('pinterest_tag_id', $form_state->getValue('pinterest_tag_id'));
    $config->set('pixel_enabled', $form_state->getValue('pixel_enabled'));
    $config->set('facebook_pixel', $form_state->getValue('facebook_pixel'));
    $config->set('facebook_api_token', $form_state->getValue('facebook_api_token'));
    $config->set('filter_enabled', $form_state->getValue('filter_enabled'));
    $config->set('filter_domain', $form_state->getValue('filter_domain'));

    $config->save();

    parent::submitForm($form, $form_state);
  }

}
