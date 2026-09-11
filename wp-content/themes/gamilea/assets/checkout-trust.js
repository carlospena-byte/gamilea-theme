(function () {
	'use strict';

	// Purely decorative DOM additions (icons, a subtitle line, a trust reassurance badge) — never
	// touches form fields, values, or anything WooCommerce reads back for validation/submission.

	function enhanceStepHeading(fieldsetSelector, iconClass, subtitleText) {
		var fieldset = document.querySelector(fieldsetSelector);
		if (!fieldset) { return; }
		var heading = fieldset.querySelector('.wc-block-components-checkout-step__heading');
		var h2 = heading ? heading.querySelector('h2') : null;
		if (!heading || !h2 || heading.classList.contains('gamilea-enhanced')) { return; }
		heading.classList.add('gamilea-enhanced');

		var icon = document.createElement('span');
		icon.className = 'gamilea-step-icon ' + iconClass;
		icon.setAttribute('aria-hidden', 'true');

		var textWrap = document.createElement('div');
		textWrap.className = 'gamilea-step-text';

		var subtitle = document.createElement('p');
		subtitle.className = 'gamilea-step-subtitle';
		subtitle.textContent = subtitleText;

		heading.insertBefore(icon, h2);
		textWrap.appendChild(h2);
		textWrap.appendChild(subtitle);
		heading.appendChild(textWrap);
	}

	// WooCommerce renders each payment method's description as a sibling "accordion content"
	// element after the <label>, in the same row as the trust badge, instead of stacked under
	// the title — move it into the label-group so it lands directly below "Contra reembolso".
	function movePaymentDescriptionUnderTitle() {
		document.querySelectorAll('.wc-block-checkout__payment-method .wc-block-components-radio-control-accordion-option').forEach(function (wrap) {
			var content = wrap.querySelector('.wc-block-components-radio-control-accordion-content');
			var labelGroup = wrap.querySelector('.wc-block-components-radio-control__label-group');
			if (!content || !labelGroup || content.parentElement === labelGroup) { return; }
			labelGroup.appendChild(content);
		});
	}

	function addPaymentTrustBadge() {
		var label = document.querySelector('.wc-block-checkout__payment-method .wc-block-components-radio-control__option-checked');
		var layout = label ? label.querySelector('.wc-block-components-radio-control__option-layout') : null;
		if (!layout || layout.querySelector('.gamilea-trust-badge')) { return; }

		var badge = document.createElement('span');
		badge.className = 'gamilea-trust-badge';

		var icon = document.createElement('span');
		icon.className = 'gamilea-trust-badge__icon';
		icon.setAttribute('aria-hidden', 'true');

		var text = document.createElement('span');
		text.className = 'gamilea-trust-badge__text';
		var strong = document.createElement('strong');
		strong.textContent = 'Pago seguro';
		var small = document.createElement('small');
		small.textContent = 'Sin cargos adicionales';
		text.appendChild(strong);
		text.appendChild(small);

		badge.appendChild(icon);
		badge.appendChild(text);
		layout.appendChild(badge);
	}

	function enhanceAll() {
		enhanceStepHeading('.wc-block-checkout__shipping-option', 'gamilea-step-icon--truck', 'Selecciona cómo quieres recibir tu pedido.');
		enhanceStepHeading('.wc-block-checkout__payment-method', 'gamilea-step-icon--card', 'Elige el método de pago que prefieres.');
		movePaymentDescriptionUnderTitle();
		addPaymentTrustBadge();
	}

	var observer = new MutationObserver(function (mutations) {
		for (var i = 0; i < mutations.length; i++) {
			if (mutations[i].addedNodes.length) { enhanceAll(); return; }
		}
	});
	observer.observe(document.body, { childList: true, subtree: true });

	enhanceAll();
})();
