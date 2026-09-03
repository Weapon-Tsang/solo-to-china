(function () {
	'use strict';

	var settings = window.stcCommercialEvents || {};
	var endpoint = typeof settings.endpoint === 'string' ? settings.endpoint : '';
	var seenImpressions = new Set();

	if (!endpoint) {
		return;
	}

	function deviceClass() {
		if (window.matchMedia('(max-width: 599px)').matches) {
			return 'mobile';
		}
		if (window.matchMedia('(max-width: 900px)').matches) {
			return 'tablet';
		}
		return 'desktop';
	}

	function eventPayload(component, eventType) {
		var data = component.dataset;
		var payload = {
			event_type: eventType,
			affiliate_asset_id: data.stcAffiliateAssetId || '',
			provider: data.stcProvider || '',
			category: data.stcCategory || '',
			slot_key: data.stcSlotKey || '',
			component_variant: (data.stcComponent || '') + ':' + (data.stcComponentVariant || 'default'),
			placement: data.stcPlacement || '',
			timestamp: new Date().toISOString(),
			device: deviceClass(),
			locale: document.documentElement.lang || navigator.language || 'en',
			strategy_version: data.stcStrategyVersion || ''
		};

		['articleId', 'draftId', 'entity', 'route', 'destination'].forEach(function (key) {
			var value = data['stc' + key.charAt(0).toUpperCase() + key.slice(1)];
			if (value) {
				payload[key.replace(/[A-Z]/g, function (letter) { return '_' + letter.toLowerCase(); })] = value;
			}
		});

		return payload;
	}

	function send(component, eventType) {
		var body = JSON.stringify(eventPayload(component, eventType));
		if (navigator.sendBeacon) {
			try {
				if (navigator.sendBeacon(endpoint, new Blob([body], { type: 'application/json' }))) {
					return;
				}
			} catch (error) {
				// Fall through to a non-blocking keepalive request.
			}
		}

		try {
			fetch(endpoint, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: body,
				credentials: 'same-origin',
				keepalive: true
			}).catch(function () {});
		} catch (error) {
			// Analytics failure must never interrupt the visitor.
		}
	}

	var components = Array.prototype.slice.call(document.querySelectorAll('[data-stc-commercial="true"]'));
	if (!components.length) {
		return;
	}

	if ('IntersectionObserver' in window) {
		var observer = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting || entry.intersectionRatio < 0.5) {
					return;
				}
				var component = entry.target;
				var key = (component.dataset.stcArticleId || '') + '|' + (component.dataset.stcSlotKey || '');
				if (!seenImpressions.has(key)) {
					seenImpressions.add(key);
					send(component, 'impression');
				}
				observer.unobserve(component);
			});
		}, { threshold: [0.5] });

		components.forEach(function (component) {
			observer.observe(component);
		});
	}

	document.addEventListener('click', function (event) {
		var link = event.target.closest('[data-stc-commercial-click]');
		if (!link) {
			return;
		}
		var component = link.closest('[data-stc-commercial="true"]');
		if (component) {
			send(component, 'click');
		}
	});
}());
