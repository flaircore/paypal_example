<?php
/**
 * PaypalListener
 */

namespace Drupal\paypal_example;


use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

class PaypalListener implements EventSubscriberInterface {


  public function paypalResponseEvent(GetResponseEvent $event){
    $request = $event->getRequest();
    /**
     * Check from the request if the route name matches what we
     * defined in our routing.yml, is for continue to process
     * the request
     */
    if ($request->attributes->get('_route') === 'paypal_example.paypal_example') {

      // get current route for use in redirects
      $current_url = Url::fromRoute('paypal_example.paypal_example', [], ['absolute' => 'TRUE']);
      $urlString = $current_url->toString();

      if ($request->query->get('success') && $request->query->get('paymentId') && $request->query->get('PayerID')) {
        $paypalSuccessResponse = $request->query->get('success');
        if ($paypalSuccessResponse === 'false') {
          // clear the temporary values in the Url by redirect the user
          $response = new RedirectResponse($urlString);#same page
          $response->send();
          \Drupal::messenger()->addMessage('Failed to authorize charge/payment');

          return;
        }

        if ($paypalSuccessResponse === 'true') {

          $paymentId = $request->query->get('paymentId');
          $payerId = $request->query->get('PayerID');

          // fill in our Test App credentials as created here
          // https://developer.paypal.com/developer/applications/
          $clientId = '';//Your own app
          $clientSecret = '';// Your own app
          $apiContext = new ApiContext(
            new OAuthTokenCredential(
              $clientId,
              $clientSecret
            )
          );

          /**
           * create our payment object to send to paypal
           * if success, we will use it's data
           */
          $payment = Payment::get($paymentId, $apiContext);
          $execute = new PaymentExecution();
          $execute->setPayerId($payerId);


          try {

            $payment->execute($execute, $apiContext);

          } catch (\Exception $exception) {
            // maybe log errors here and choose what to pass on to display to user

            // if we have an error display message and return
            // we should still clear the values in the url here
            // but to avoid code repetition and to stick to the
            // scope of this article, I will leave that out.
            if ($exception) {
              \Drupal::messenger()->addMessage('Error charging you!!: '.$exception->getMessage());
              return;
            }
          }
          /**
           * if no exception go ahead and save something(s) to the database,
           * clear temporary values in the url, and thank the user.
           * you can dump($payment) below and choose what to post to database
           *
           */
          $paymentState = $payment->getState();

          if ($paymentState === 'approved') {
            $amount = $payment->transactions[0]->amount->total;


            // clear the temporary values in the Url by redirect the user

            $response = new RedirectResponse($urlString);#same page but cleared url vals
            $response->send();

            \Drupal::messenger()->addMessage('Thank you!, Payment received for amount: '.$amount . ' AUD.');

            return;
          }
        }
      }
    }
  }

  public static function getSubscribedEvents() {
    return [
      KernelEvents::REQUEST => 'paypalResponseEvent', // implement paypalResponseEvent method above
    ];
  }
}
