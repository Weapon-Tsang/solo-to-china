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
	var mobile = window.matchMedia('(max-width: 840px)');
	var viewport = window.visualViewport;
	var openingHeight = 0;
	var positionFrame = 0;

	function motionFrames() {
		var origin = trigger.getBoundingClientRect();
		// offset geometry stays untransformed while the dialog animates.
		var x = origin.left + origin.width / 2 - dialog.offsetLeft - dialog.offsetWidth / 2;
		var y = origin.top + origin.height / 2 - dialog.offsetTop - dialog.offsetHeight / 2;
		var scale = Math.min(origin.width / dialog.offsetWidth, origin.height / dialog.offsetHeight);
		return [
			{ transform: 'translate(' + x + 'px,' + y + 'px) scale(' + scale + ')', opacity: 0 },
			{ transform: 'translate(0,0) scale(1)', opacity: 1 }
		];
	}

	function positionDialog() {
		positionFrame = 0;
		if (!dialog.open) { return; }
		if (mobile.matches) {
			var height = viewport ? viewport.height : window.innerHeight;
			var offset = viewport ? viewport.offsetTop : 0;
			dialog.style.setProperty('--stc-search-height', Math.max(0, height - 32) + 'px');
			var desiredTop = openingHeight / 3 - dialog.offsetHeight / 2;
			var top = Math.max(16, Math.min(desiredTop, height - dialog.offsetHeight - 16));
			dialog.style.setProperty('--stc-search-top', offset + top + 'px');
		} else {
			dialog.style.removeProperty('--stc-search-height');
			dialog.style.removeProperty('--stc-search-top');
		}
		if (motion) { motion.effect.setKeyframes(motionFrames()); }
	}

	function queuePosition() {
		if (dialog.open && !positionFrame) { positionFrame = window.requestAnimationFrame(positionDialog); }
	}
	window.addEventListener('resize', queuePosition);
	if (viewport) {
		viewport.addEventListener('resize', queuePosition);
		viewport.addEventListener('scroll', queuePosition);
	}

	function closeSearch() {
		if (!dialog.open || closing) { return; }
		closing = true;
		dialog.classList.remove('is-visible');
		if (motion && !reduced.matches) {
			motion.effect.setKeyframes(motionFrames());
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
		openingHeight = viewport ? viewport.height : window.innerHeight;
		dialog.showModal();
		document.body.classList.add('stc-search-open');
		positionDialog();
		if (!reduced.matches && typeof dialog.animate === 'function') {
			motion = dialog.animate(motionFrames(), { duration: 320, easing: 'cubic-bezier(.22,.68,0,1)', fill: 'both' });
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
		window.cancelAnimationFrame(positionFrame);
		positionFrame = 0;
		dialog.style.removeProperty('--stc-search-height');
		dialog.style.removeProperty('--stc-search-top');
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
