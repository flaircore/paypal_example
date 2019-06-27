#Paypal example for drupal

Contains code example for a drupal form that collects payments via paypal

Clone this repo in your modules folder and enable the module.

Download paypal-php-sdk via composer

run `composer require paypal/rest-api-sdk-php:*`

go to `https://developer.paypal.com/developer/applications/`

login and create your app and test accounts with some virtual $$

copy your client id and crient secret in 

\Drupal\paypal_examplePaypalListener.php; lines 53, 54 AND
\Drupal\paypal_example\FormPaypalExample.php; lines 87, 88

and go to <yourhomepage>/paypal_example/form to test 

Blog at

https://www.flaircore.com/blog/paypal-express-checkout-and-drupal
