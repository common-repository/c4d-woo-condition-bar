(function($){
	"use strict";
	c4d_woo_cb.update = function () {
		var miniCart = $('.total .woocommerce-Price-amount'),
		pageCart = $('.order-total .woocommerce-Price-amount');

		if (miniCart.length > 0) {
			var total = miniCart;
		} else {
			var total = pageCart;
		}

		total = total.clone();
		total.find('.woocommerce-Price-currencySymbol').remove();
		
		if (total.length > 0) {
			total = total.html();
			total = total.replace(c4d_woo_cb.thousand, '');
			total = parseFloat(total);
			
			$('.c4d-woo-cb').each(function(index, el){
				var amount = parseFloat($(el).attr('data-amount')),
				currentClass = $(el).attr('class');
				if (total > 0 && total >= amount) {
					$(el).attr('class', currentClass).addClass('success');
				} 
				if (total > 0 && total < amount) {
					$(el).attr('class', currentClass).addClass('achive');	
					$(el).find('.c4d-woo-cb__achive_amount').html(c4d_woo_cb.currency + (amount - total));
				}
			});
		}
	}
	$(document).ready(function(){
		setTimeout(function(){
			c4d_woo_cb.update();	
		}, 2000);
		$(document).ajaxComplete(function(event, request, settings) {
			c4d_woo_cb.update();
		});
	});
})(jQuery);