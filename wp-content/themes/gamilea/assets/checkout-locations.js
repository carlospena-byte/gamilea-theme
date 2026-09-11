(function () {
	'use strict';

	// gamileaLocations: { "Departamento name": ["Town", ...], ... } — localized from PHP.
	var locations = window.gamileaLocations || {};

	// WooCommerce always appends this custom field after every core address field, and CSS `order`
	// only works while the address form is display:flex — WooCommerce switches it to display:block
	// on narrow viewports, where `order` becomes a no-op and city falls back to the end. Moving the
	// actual DOM node next to Departamento works at every breakpoint, flex or not.
	function moveCityNextToState(scope) {
		var state = document.getElementById(scope + '-state');
		var city = document.getElementById(scope + '-gamilea-city');
		if (!state || !city) { return; }
		var stateWrap = state.closest('.wc-block-components-address-form__state');
		var cityWrap = city.closest('[class*="select-input-gamilea-city"]');
		if (!stateWrap || !cityWrap || stateWrap.parentNode !== cityWrap.parentNode) { return; }
		if (stateWrap.nextElementSibling !== cityWrap) {
			stateWrap.parentNode.insertBefore(cityWrap, stateWrap.nextElementSibling);
		}
	}

	function filterCity(scope) {
		var state = document.getElementById(scope + '-state');
		var city = document.getElementById(scope + '-gamilea-city');
		if (!state || !city) { return; }

		var deptName = state.value && state.selectedIndex >= 0 ? state.options[state.selectedIndex].text : '';
		var towns = deptName && locations[deptName] ? locations[deptName] : [];
		var options = city.querySelectorAll('option');
		var selectedStillValid = !city.value;

		city.disabled = !deptName;

		for (var i = 0; i < options.length; i++) {
			var option = options[i];
			if (!option.value) { continue; }
			var matches = towns.indexOf(option.value) !== -1;
			option.hidden = !matches;
			option.disabled = !matches;
			if (matches && option.value === city.value) { selectedStillValid = true; }
		}

		if (!selectedStillValid) {
			city.value = '';
			city.dispatchEvent(new Event('change', { bubbles: true }));
		}
	}

	function updateAll() {
		['shipping', 'billing'].forEach(function (scope) {
			moveCityNextToState(scope);
			filterCity(scope);
		});
	}

	document.addEventListener('change', function (event) {
		if (event.target && event.target.id === 'shipping-state') { filterCity('shipping'); }
		if (event.target && event.target.id === 'billing-state') { filterCity('billing'); }
	});

	// The checkout block mounts/remounts fields as the shopper edits the form
	// (e.g. toggling "use same address for billing"), so keep Ciudad positioned
	// next to Departamento and re-apply its current filter whenever fields change.
	var observer = new MutationObserver(function (mutations) {
		for (var i = 0; i < mutations.length; i++) {
			if (mutations[i].addedNodes.length) { updateAll(); return; }
		}
	});
	observer.observe(document.body, { childList: true, subtree: true });

	updateAll();
})();
