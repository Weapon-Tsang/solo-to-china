(function () {
	'use strict';

	var header = document.querySelector('.stc-header');
	var toggle = document.querySelector('.stc-menu-toggle');
	var nav = document.querySelector('#stc-primary-nav');
	var mobileQuery = window.matchMedia('(max-width: 840px)');

	if (!header || !toggle || !nav) {
		return;
	}

	var label = toggle.querySelector('.screen-reader-text');

	function syncLabel() {
		var isOpen = toggle.getAttribute('aria-expanded') === 'true';
		var nextLabel = toggle.getAttribute(isOpen ? 'data-close-label' : 'data-open-label');

		if (label && nextLabel) {
			label.textContent = nextLabel;
		}
	}

	function closeMenu(restoreFocus) {
		if (toggle.getAttribute('aria-expanded') !== 'true') {
			return;
		}

		header.classList.remove('is-menu-open');
		toggle.setAttribute('aria-expanded', 'false');
		syncLabel();

		if (restoreFocus) {
			toggle.focus();
		}
	}

	toggle.addEventListener('click', syncLabel);

	document.addEventListener('keydown', function (event) {
		if (event.key === 'Escape') {
			closeMenu(true);
		}
	});

	nav.addEventListener('click', function (event) {
		if (mobileQuery.matches && event.target.closest('a')) {
			closeMenu(false);
		}
	});

	if (typeof mobileQuery.addEventListener === 'function') {
		mobileQuery.addEventListener('change', function (event) {
			if (!event.matches) {
				closeMenu(false);
			}
		});
	}

	syncLabel();
})();
