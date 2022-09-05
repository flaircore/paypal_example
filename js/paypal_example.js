// paypal_example.js
(function ($, Drupal, drupalSettings) {

  const data = drupalSettings.paypal_payment_data
  if (!data.client_id) {
    console.warn("Some information is missing!!")
    return
  }

  $(document).ready(function () {
    console.log(data)
    console.log(data)
    console.log(data)
    window.paypalLoadScript({ "client-id": data.client_id }).then((paypal) => {
      const amountInput = document.querySelector('#paypal-example input')
      const uniqueId = data.title + ((Math.random() * Math.pow(36, 6)) | 0).toString(36)
      paypal.Buttons({
        // Set up the transaction
        createOrder: function(dt, actions) {
          // This function sets up the details of the transaction, including the amount and line item details.

          data.amount = amountInput.value

          if (!data.amount) {
            console.error('Please enter an amount')
            return
          }

          return actions.order.create({
            intent: 'CAPTURE',
            purchase_units: [{
              reference_id:  uniqueId,
              custom_id: uniqueId,
              amount: {
                value: data.amount,
                currency_code: data.currency,
                breakdown: {
                  item_total: {
                    currency_code: data.currency,
                    value: data.amount
                  }
                }
              },
              items: [
                {
                  name: data.title,
                  description: data.title,
                  sku: uniqueId,
                  unit_amount: {
                    currency_code: data.currency,
                    value: data.amount
                  },
                  quantity: 1
                },
              ]
            }]
          });
        },
        onInit: function (dt, actions) {
          // Btns initialized
        },
        onApprove: function(dt, actions) {
          amountInput.value = data.amount
          window.location = `?&order_sku=${uniqueId}&order_id=${dt.orderID }`
        }
      }).render('#paypal-button-container');
    });

  })

})(jQuery, Drupal, drupalSettings)

// End of file
