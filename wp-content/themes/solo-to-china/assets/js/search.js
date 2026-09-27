(function () {
	var trigger = document.querySelector('[data-stc-search-trigger]');
	var dialog = document.querySelector('[data-stc-search-dialog]');
	if (!trigger || !dialog) { return; }
	var close = dialog.querySelector('[data-stc-search-close]');
	var field = dialog.querySelector('input[name="s"]');
	var opener = null;
	var motion = null;
	var closing = false;
	var reduced = window.matchMedia('(prefers-reduced-motion: reduce)');

	function closeSearch() {
		if (!dialog.open || closing) { return; }
		closing = true;
		dialog.classList.remove('is-visible');
		if (motion && !reduced.matches) {
			motion.onfinish = function () { dialog.close(); };
			motion.reverse();
		} else { dialog.close(); }
	}

	trigger.addEventListener('click', function (event) {
		if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || event.button !== 0) { return; }
		if (typeof dialog.showModal !== 'function') { return; }
		event.preventDefault();
		document.dispatchEvent(new Event('stc:search-open'));
		opener = trigger;
		closing = false;
		var origin = trigger.getBoundingClientRect();
		dialog.showModal();
		document.body.classList.add('stc-search-open');
		var bounds = dialog.getBoundingClientRect();
		if (!reduced.matches && typeof dialog.animate === 'function') {
			var x = origin.left + origin.width / 2 - bounds.left - bounds.width / 2;
			var y = origin.top + origin.height / 2 - bounds.top - bounds.height / 2;
			var scale = Math.min(origin.width / bounds.width, origin.height / bounds.height);
			motion = dialog.animate([
				{ transform: 'translate(' + x + 'px,' + y + 'px) scale(' + scale + ')', opacity: 0 },
				{ transform: 'translate(0,0) scale(1)', opacity: 1 }
			], { duration: 320, easing: 'cubic-bezier(.22,.68,0,1)', fill: 'both' });
		}
		dialog.classList.add('is-visible');
		field.focus({ preventScroll: true });
	});

	close.addEventListener('click', closeSearch);
	dialog.addEventListener('cancel', function (event) { event.preventDefault(); closeSearch(); });
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
		if (motion) { motion.cancel(); motion = null; }
		closing = false;
		dialog.classList.remove('is-visible');
		document.body.classList.remove('stc-search-open');
		if (opener && opener.isConnected) { opener.focus({ preventScroll: true }); }
		opener = null;
	});
	dialog.addEventListener('click', function (event) {
		var bounds = dialog.getBoundingClientRect();
		if (event.target === dialog && (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom)) { closeSearch(); }
	});
})();
