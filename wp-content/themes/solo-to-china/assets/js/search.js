(function () {
	var trigger = document.querySelector('[data-stc-search-trigger]');
	var dialog = document.querySelector('[data-stc-search-dialog]');
	if (!trigger || !dialog) { return; }
	var close = dialog.querySelector('[data-stc-search-close]');
	var field = dialog.querySelector('input[name="s"]');
	var opener = null;

	function visibleInlineField() {
		var fields = document.querySelectorAll('.stc-search-form--inline input[name="s"], .stc-search-form--results input[name="s"]');
		for (var i = 0; i < fields.length; i++) {
			if (fields[i].getClientRects().length) { return fields[i]; }
		}
		return null;
	}

	trigger.addEventListener('click', function (event) {
		if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || event.button !== 0) { return; }
		var inline = visibleInlineField();
		if (inline) {
			event.preventDefault();
			inline.scrollIntoView({ block: 'center', behavior: 'auto' });
			inline.focus({ preventScroll: true });
			return;
		}
		if (typeof dialog.showModal !== 'function') { return; }
		event.preventDefault();
		document.dispatchEvent(new Event('stc:search-open'));
		opener = trigger;
		dialog.showModal();
		document.body.classList.add('stc-search-open');
		field.focus({ preventScroll: true });
	});

	close.addEventListener('click', function () { dialog.close(); });
	dialog.addEventListener('keydown', function (event) {
		if (event.key !== 'Tab') { return; }
		var stops = [close, field, dialog.querySelector('.search-submit')];
		if (event.shiftKey && document.activeElement === stops[0]) {
			event.preventDefault();
			stops[stops.length - 1].focus();
		} else if (!event.shiftKey && document.activeElement === stops[stops.length - 1]) {
			event.preventDefault();
			stops[0].focus();
		}
	});
	dialog.addEventListener('close', function () {
		document.body.classList.remove('stc-search-open');
		if (opener && opener.isConnected) { opener.focus({ preventScroll: true }); }
		opener = null;
	});
	dialog.addEventListener('click', function (event) {
		if (event.target === dialog) { dialog.close(); }
	});
})();
