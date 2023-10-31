jQuery(document).ready(function ($) {
    var pointsApplied = false; // Track whether points have already been applied

    // Function to apply points redemption
    function applyPointsRedemption(points) {
        if (pointsApplied) {
            // Display an error message if points have already been applied
            $('.woocommerce-message, .woocommerce-error').remove();
            $('.woocommerce-cart-form').before('<div class="woocommerce-error" role="alert">Points have already been applied.</div>');
            return;
        }

        var data = {
            action: 'apply_points_redemption',
            nonce: custom_script_params.nonce,
            points: points,
        };

        // Send the AJAX request
        $.post(custom_script_params.ajax_url, data, function (response) {
            if (response.success) {
                pointsApplied = true; // Mark points as applied
                // Parse the response data
                var cartTotal = $('<div>' + response.cart_total + '</div>').text();
                var discountAmount = $('<div>' + response.discount_amount + '</div>').text();
                var pointsEarned = response.points_earned;

                // Update the cart totals and discount amount
                $('.fee td').html('-' + discountAmount);
                $('.order-total td').html(response.total_amount);
                 // Update the "Total Points" display on the cart and checkout pages
                 $('.points-earned td').html(pointsEarned + ' Points');
                
                
                var pointsRedemptionAmount = parseFloat(discountAmount.replace(/[^\d.-]/g, ''));
                console.log('Points Redemption amount:', pointsRedemptionAmount);

                 if(pointsRedemptionAmount === 0){
                    $('.fee').hide();
                 }else{
                    $('.fee').show();
                 }

                // Trigger the cart recalculation
                $('body').trigger('update_checkout');
                $('.woocommerce-message, .woocommerce-error').remove();
				if(points>1){
					var pointtext= Math.floor(points) + ' Points Applied.';
				}else{
					var pointtext= Math.floor(points) + ' Point Applied.';
				}
                $('.woocommerce-cart-form').before('<div class="woocommerce-message" role="alert">' + pointtext + '</div>');
                
            } else {
                // Display the error message for insufficient points
                $('.woocommerce-message, .woocommerce-error').remove();
                $('.woocommerce-cart-form').before('<div class="woocommerce-error" role="alert">Oops!! You don\'t have ' + points + ' points for redemption.</div>');
            }
        }).fail(function () {
            alert('Error processing the request.');
        });
    }

    // Event listener for the "Apply Points" button
    $('#apply_points_btn').on('click', function () {
        var points = $('#points_redemption').val();
        //var cartTotal = parseFloat($('.order-total td').text().replace(/[^\d.]/g, ''));
        var cartTotal = parseFloat($('.cart-subtotal td').text().replace(/[^\d.]/g, ''));
        console.log('Entered Points:', points);
        console.log('Cart Total:', cartTotal);
        if(points <= cartTotal){
            if (points >= 1) {
                applyPointsRedemption(points);
            } else {
                $('.woocommerce-message, .woocommerce-error').remove();
                $('.woocommerce-cart-form').before('<div class="woocommerce-error" role="alert">Enter a valid point value to redeem.</div>');
            }
        }else{
            $('.woocommerce-message, .woocommerce-error').remove();
            $('.woocommerce-cart-form').before('<div class="woocommerce-error" role="alert">Please enter less than or equal ' + cartTotal + ' Points to redeem.</div>');
        }

    });
});

jQuery(document).ready(function ($) {
  var feeRow = document.querySelector('tr.fee');
  if(feeRow){
    var feeamttext = feeRow.querySelector('.woocommerce-Price-amount').textContent.replace(/[^\d.]/g, '');
  
    pointsRedemptionAmountElement =  parseFloat(feeamttext.replace(',', ''));
    
       if (isNaN(pointsRedemptionAmountElement) || pointsRedemptionAmountElement === 0) {
            $('.fee').hide();
        }
  }
});
