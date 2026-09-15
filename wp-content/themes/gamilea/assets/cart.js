(function () {
	'use strict';

	// Keeps the summary line under the page title in sync with the cart, which the blocks
	// re-render client-side after every quantity change or removal. Read-only: it never touches
	// the cart controls, their values, or anything WooCommerce submits.

	var intro = document.querySelector('.gamilea-cart-intro');
	if (!intro || typeof gamileaCart === 'undefined') { return; }

	function countItems() {
		var inputs = document.querySelectorAll('.wc-block-cart-item__quantity .wc-block-components-quantity-selector__input');
		var total = 0;
		for (var i = 0; i < inputs.length; i++) {
			var value = parseInt(inputs[i].value, 10);
			total += isNaN(value) ? 0 : value;
		}
		return total;
	}

	function syncIntro() {
		if (!document.querySelector('.wc-block-cart-items')) { return; }
		var count = countItems();
		if (!count) { return; }
		var template = count === 1 ? gamileaCart.singular : gamileaCart.plural;
		var text = template.replace('%d', count);
		if (intro.textContent !== text) { intro.textContent = text; }
	}

	new MutationObserver(syncIntro).observe(document.body, { childList: true, subtree: true });
	document.addEventListener('change', syncIntro, true);

	syncIntro();
})();
