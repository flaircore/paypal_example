<?php
# start of PayPalPaymentsConfigForm.php
namespace Drupal\paypal_example\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class PayPalPaymentsSettingsForm.
 *
 * Store the paypal credentials required to make the api calls
 */
class PayPalPaymentsConfigForm extends ConfigFormBase {

  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   *   An array of configuration object names that are editable if called in
   *   conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames() {

    return ['paypal_example.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'paypal_example_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('paypal_example.settings');
    $environmentTypes = [
      'live' => 'Live',
      'sandbox' => 'Sandbox',
    ];

    $currency = [
      'USD' => 'USD',
      'GBP' => 'GBP',
      'AUD' => 'AUD',
      'CAD' => 'CAD',
      'EUR' => 'EUR',
      'JPY' => 'JPY'
    ];

    $form['client_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client ID'),
      '#description' => $this->t('The Client ID from PayPal, you can put any value here if you have set PP_CLIENT_ID in your environment variables'),
      '#default_value' => $config->get('client_id'),
      '#maxlength' => 128,
      '#size' => 64,
      '#required' => TRUE,
    ];
    $form['client_secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client Secret'),
      '#description' => $this->t('The Client Secret Key From PayPal, (You can put any value here, if you have set PP_CLIENT_ID in your env variables.)'),
      '#default_value' => $config->get('client_secret'),
      '#maxlength' => 128,
      '#size' => 64,
      '#required' => TRUE,
    ];
    $form['environment'] = [
      '#type' => 'select',
      '#title' => $this->t('Environment'),
      '#options' => $environmentTypes,
      '#description' => $this->t('Select either; live or sandbox(for development)'),
      '#default_value' => $config->get('environment'),
      '#required' => TRUE,
      '#multiple' => FALSE,
    ];
    $form['currency'] = [
      '#type' => 'select',
      '#title' => $this->t('Store Currency'),
      '#options' => $currency,
      '#description' => $this->t('Select the currency to use with your store'),
      '#default_value' => $config->get('currency'),
      '#required' => TRUE,
    ];

    $form['payment_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Payment Title'),
      '#description' => $this->t('The title to associate with this payment'),
      '#placeholder' => 'Example Payment',
      '#default_value' => $config->get('payment_title'),
      '#maxlength' => 180,
      '#size' => 120,
      '#required' => TRUE,
    ];

    $form['paypal_instructions'] = [
      '#type' => 'markup',
      '#markup' => $this->paypalDocumentation(),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $env = $form_state->getValue('environment');

    $config = $this->config('paypal_example.settings');
    $config
      ->set('currency', $form_state->getValue('currency'))
      ->set('environment',$env)
      ->set('client_secret', $form_state->getValue('client_secret'))
      ->set('client_id', $form_state->getValue('client_id'))
      ->set('payment_title', $form_state->getValue('payment_title'))
      ->save();

    drupal_flush_all_caches();
    parent::submitForm($form, $form_state);

  }

  private function paypalDocumentation() {
    return '
    <div>
    <p> <strong>Getting started. </strong></p>
    <p>
        * After logging in at: <a target="_blank" href="https://www.paypal.com/">Paypal.com</a> , got to
        <a target="_blank" href="https://developer.paypal.com/developer/accountStatus/">Developer.paypal.com </a>
    </p>
    <p>
        * Under <strong>Dashboard > My Apps & Credentials </strong>, click on Create App button, give it a
        name, select a sandbox Business Account to use while testing, and confirm create.
    </p>
    <p>
        * Note the <strong>Client ID</strong>,  and the <strong>Secret</strong> as those are required
        in the above inputs.
    </p>
    </div>
      <br>
    ';
  }
}

# End of file
