(function () {
	document.documentElement.classList.add('stc-tools-js');

	function formatDate(date) {
		return date.toLocaleDateString('en-US', {
			year: 'numeric',
			month: 'long',
			day: 'numeric'
		});
	}

	function bookingWindowStatus(visitDate, bookingDate) {
		var today = new Date();
		today.setHours(0, 0, 0, 0);
		visitDate.setHours(0, 0, 0, 0);
		bookingDate.setHours(0, 0, 0, 0);

		if (visitDate < today) {
			return {
				key: 'passed',
				label: 'VISIT DATE PASSED',
				copy: 'Choose a future visit date.'
			};
		}

		if (today >= bookingDate) {
			return {
				key: 'reached',
				label: 'BOOKING WINDOW REACHED',
				copy: 'Your estimated booking window has started. Check the latest ticket information now.'
			};
		}

		return {
			key: 'not-open',
			label: 'BOOKING NOT OPEN YET',
			copy: 'Recommended time to start checking: ' + formatDate(bookingDate) + '.'
		};
	}

	function getBookingPlan(select, dateInput) {
		if (!dateInput.value || !/^\d{4}-\d{2}-\d{2}$/.test(dateInput.value)) {
			return null;
		}

		var option = select.options[select.selectedIndex];

		if (!option) {
			return null;
		}

		var leadDays = parseInt(option.getAttribute('data-lead-days'), 10);
		var visitDate = new Date(dateInput.value + 'T12:00:00');

		if (Number.isNaN(visitDate.getTime()) || !Number.isFinite(leadDays) || leadDays < 0) {
			return null;
		}

		var bookingDate = new Date(visitDate.getTime());
		bookingDate.setDate(bookingDate.getDate() - leadDays);

		return {
			attraction: option.getAttribute('data-name'),
			visitDate: visitDate,
			bookingDate: bookingDate,
			leadDays: leadDays,
			status: bookingWindowStatus(visitDate, bookingDate)
		};
	}

	function appendLine(container, className, text, elementName) {
		var element = document.createElement(elementName || 'p');
		element.className = className;
		element.textContent = text;
		container.append(element);
	}

	function ticketBookingWindow(form) {
		var select = form.querySelector('select[name="stc_attraction"]');
		var dateInput = form.querySelector('input[name="stc_visit_date"]');
		var result = form.querySelector('[data-stc-ticket-result]');
		var bookingLink = form.querySelector('[data-stc-ticket-link]');
		var disclosure = form.querySelector('[data-stc-ticket-disclosure]');

		if (!select || !dateInput || !result || !bookingLink || !disclosure) {
			return;
		}

		form.addEventListener('submit', function (event) {
			event.preventDefault();
			result.replaceChildren();

			var plan = getBookingPlan(select, dateInput);

			if (!plan) {
				appendLine(result, 'stc-ticket-result__error', 'Choose a valid visit date to check booking timing.');
				result.classList.add('is-visible');
				bookingLink.hidden = true;
				disclosure.hidden = true;
				return;
			}

			appendLine(result, 'stc-ticket-result__attraction', plan.attraction, 'strong');
			appendLine(result, 'stc-ticket-result__visit', 'Visit date: ' + formatDate(plan.visitDate));
			appendLine(result, 'stc-ticket-status stc-ticket-status--' + plan.status.key, plan.status.label, 'span');

			if (plan.status.key !== 'passed') {
				appendLine(result, 'stc-ticket-result__label', 'Recommended time to start checking');
				appendLine(result, 'stc-ticket-result__date', 'Around ' + formatDate(plan.bookingDate), 'strong');
				appendLine(result, 'stc-ticket-result__rule', 'Tickets commonly open about ' + plan.leadDays + ' days before the visit date.');
			}

			appendLine(result, 'stc-ticket-result__guidance', plan.status.copy);
			result.classList.add('is-visible');
			bookingLink.hidden = plan.status.key === 'passed';
			disclosure.hidden = plan.status.key === 'passed';
		});
	}

	document.querySelectorAll('[data-stc-ticket-tool]').forEach(ticketBookingWindow);
})();
