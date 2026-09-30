/* Native GET filtering works without JavaScript; enhance only the tag picker. */
(() => {
	'use strict';
	document.querySelectorAll('[data-stc-tag-filter]').forEach((filter) => {
		const picker = filter.querySelector('[data-stc-tag-picker]');
		const form = filter.querySelector('[data-stc-tag-form]');
		const search = filter.querySelector('[data-stc-tag-search]');
		const apply = filter.querySelector('[data-stc-tag-apply]');
		const close = filter.querySelector('[data-stc-tag-close]');
		const closePicker = () => {
			picker.open = false;
			picker.querySelector('summary').focus();
		};
		close.hidden = false;
		close.addEventListener('click', closePicker);
		if (!search || !apply) return;
		const applyLabel = apply.querySelector('[data-stc-tag-apply-label]');
		const options = Array.from(filter.querySelectorAll('[data-stc-tag-option]'));
		const groups = Array.from(filter.querySelectorAll('[data-stc-tag-group]'));
		const selection = filter.querySelector('[data-stc-tag-selection]');
		const noMatch = filter.querySelector('[data-stc-tag-no-match]');
		filter.querySelector('[data-stc-tag-search-wrap]').hidden = false;
		const updateSelection = () => {
			const count = form.querySelectorAll('input[type="checkbox"]:checked').length;
			selection.textContent = count ? `${count} selected` : 'All guides';
		};
		form.addEventListener('change', updateSelection);
		updateSelection();
		search.addEventListener('input', () => {
			const term = search.value.trim().toLocaleLowerCase();
			let visible = 0;
			options.forEach((option) => {
				option.hidden = !option.textContent.toLocaleLowerCase().includes(term);
				if (!option.hidden) visible++;
			});
			groups.forEach((group) => {
				group.hidden = !Array.from(group.querySelectorAll('[data-stc-tag-option]')).some((option) => !option.hidden);
			});
			noMatch.hidden = visible > 0;
		});
		picker.addEventListener('keydown', (event) => {
			if (event.key === 'Escape' && picker.open) {
				closePicker();
				event.stopPropagation();
			}
		});
		document.addEventListener('click', (event) => {
			if (picker.open && !picker.contains(event.target)) picker.open = false;
		});
		form.addEventListener('submit', (event) => {
			const count = form.querySelectorAll('input[type="checkbox"]:checked').length;
			if (count > 20) {
				event.preventDefault();
				selection.textContent = 'Choose up to 20 tags.';
				return;
			}
			applyLabel.textContent = 'Filtering…';
			form.setAttribute('aria-busy', 'true');
		});
		window.addEventListener('pageshow', () => {
			applyLabel.textContent = 'Apply filters';
			form.removeAttribute('aria-busy');
			updateSelection();
		});
	});
})();
