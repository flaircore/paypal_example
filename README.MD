#Paypal example for drupal

Contains code example for a drupal form that collects payments via paypal smart buttons.

[Check out an older v1 implementation of the same here](https://github.com/flaircore/paypal_example/tree/v1)

### Blogs @:
* https://flaircore.com/blog/paypal-express-checkout-and-drupal-updated-smart-buttons
* https://dev.to/nickbahson/paypal-express-checkout-and-drupal-updated-smart-buttons-48gj

Clone this repo in your modules folder and enable the module.

Download paypal/paypal-checkout-sdk via composer

run `composer require paypal/paypal-checkout-sdk:*`

### Enable the module

### Getting started.

* After logging in at: [Paypal.com](https://www.paypal.com/) , got to [Developer.paypal.com](https://developer.paypal.com/developer/accountStatus/)

* Under ** Dashboard > My Apps & Credentials **, click on Create App button, give it a name, select a sandbox Business Account to use while testing, and confirm create.

* Note the ** Client ID ** , and the ** Secret ** as those are required in the paypal example configuration form inputs.
