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
		total.find('.woocommerce-Price-currencySymbol').remove();
		// console.log(total);
		if (total.length > 0) {
			total = $(total[0]).html();
			total = total.substring(0, total.length - c4d_woo_cb.num_demical - 1).replace(c4d_woo_cb.thousand, '');
			total = parseInt(total);
			
			$('.c4d-woo-cb').each(function(index, el){
				var amount = parseInt($(el).attr('data-amount')),
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
			if (settings.url.indexOf('wc-ajax=get_refreshed_fragments') >= 0) {
				c4d_woo_cb.update();
			}
		});
		
	});
})(jQuery);