<?php
# start of PayPalController.php

namespace Drupal\paypal_example\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\paypal_example\PayPalClient;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PayPalController extends ControllerBase {


  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /** @var \Drupal\paypal_example\PayPalClient */
  protected $paypalClient;

  public function __construct(ConfigFactoryInterface $configFactory, PayPalClient $paypalClient) {
    $this->configFactory = $configFactory;
    $this->paypalClient = $paypalClient;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('paypal_example.paypal_client'),
    );
  }

  public function pay(Request $request) {
    $config = $this->paypalClient->getConfigs();
    $currency = $config['currency'];
    $client_id = $config['client_id'];
    $payment_title = $config['payment_title'];
    $order_id = $request->get('order_id');
    $order_sku = $request->get('order_sku');

    if ($order_sku && $order_id) {
      $capture_order = $this->paypalClient->captureOrder($order_id, $order_sku);

      if (isset($capture_order['payment_status'])) {
        // Do something

      }
      (new RedirectResponse('/flair-core/paypal_payment'))->send();

    }
    $info = !$client_id ? 'Missing paypal app details.' : null;

    $data = [
      'title' => $payment_title,
      'info' => $info,
      'client_id' => $client_id,
      'currency' => $currency,
      'amount' => 10
    ];

    return [
      '#theme' => 'paypal_example',
      '#cache' => [
        'max-age' => 0
      ],
      '#attached' => [
        'library' => 'paypal_example/paypal',
        'drupalSettings' => [
          'paypal_payment_data' => $data
        ]
      ],
      // The items twig variable as an array as defined paypal_example_theme().
      '#data' => $data,
    ];
  }

}

# End of file.
