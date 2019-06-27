<?php

namespace Drupal\paypal_example\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Paypal\Exception\PayPalConnectionException;

/**
 * Class PaypalExample.
 */
class PaypalExample extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'paypal_example';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['amount'] = [
      '#type' => 'number',
      '#title' => $this->t('Amount'),
      '#description' => $this->t('The amount in store currency'),
      '#weight' => '0',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Pay with paypal'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $amount = $form_state->getValue('amount');
    if (!$amount) {
      $form_state->setErrorByName('amount', t('Amount must be a valid value'));
      return;
    }


  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /**
     * will update code from here
     */
    $storeCurrency = 'AUD';// set currency to pass
    if ($form_state->getValue('amount')) {
      // call our paypal express request passing the required vals
      $this->paypalExpressCheckoutRequest($form_state, $storeCurrency);
    }



  }

  /**
   * Request to paypal with our payment object
   */

  protected function paypalExpressCheckoutRequest(FormStateInterface $formState, $storeCurrency){
    // fill in our Test App credentials as created here
    // https://developer.paypal.com/developer/applications/
    $clientId = '';//Your own app
    $clientSecret = '';//Your own app
    $apiContext = new \PayPal\Rest\ApiContext(
      new \PayPal\Auth\OAuthTokenCredential(
        $clientId,
        $clientSecret
      )
    );

    $product_sku = 'test product 01';
    $product = $product_sku;
    $price = (float)$formState->getValue('amount');
    $shipping = 0.50;
    $total = $price + $shipping;

    // Create and fill in our payment object
    $payer = new Payer();
    $payer->setPaymentMethod('paypal');

    $item = new Item();
    $item->setName($product)
      ->setCurrency($storeCurrency)
      ->setQuantity(1)
      ->setPrice($price);

    $itemList = new ItemList();
    $itemList->setItems([$item]);

    $details =new Details();
    $details->setShipping($shipping)
      ->setSubtotal($price);

    $amount = new Amount();
    $amount->setCurrency($storeCurrency)
      ->setTotal($total)
      ->setDetails($details);

    $transaction = new Transaction();
    $transaction->setAmount($amount)
      ->setItemList($itemList)
      ->setDescription($product_sku)
      ->setInvoiceNumber(uniqid($product_sku));

    /**
     * define our route for paypal redirects first
     *  we will generate a url string from this current
     * route where the user is accessing the form, which is
     * also the route we defined in our routing.yml
     */
    $current_url = Url::fromRoute('paypal_example.paypal_example', [], ['absolute' => 'TRUE']);
    $urlString = $current_url->toString();

    $redirectUrls = new RedirectUrls();
    $redirectUrls->setReturnUrl($urlString. "/?success=true") // TODO, attach security token to url
      ->setCancelUrl($urlString. "/?success=false'");

    $payment = new Payment();
    $payment->setIntent('sale')
      ->setPayer($payer)
      ->setRedirectUrls($redirectUrls)
      ->setTransactions([$transaction]);


    try {
      $payment->create($apiContext);

    } catch (\Exception $exception) {
      die('Request failed '.$exception); //incase of error, output to user
    }

    $approvalUrl = $payment->getApprovalLink(); // Url to this payment
    $response = new TrustedRedirectResponse($approvalUrl); // redirect to external url
    $formState->setResponse($response);

    return;
  }

}

