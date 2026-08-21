(function () {
	document.documentElement.classList.add('stc-tools-js');

	var storageKey = 'stcTicketReminders';

	function stcClampText(value, maxLength) {
		return String(value || '').trim().slice(0, maxLength);
	}

	function stcDateValue(value) {
		var dateValue = stcClampText(value, 20);
		return /^\d{4}-\d{2}-\d{2}$/.test(dateValue) ? dateValue : '';
	}

	function formatDate(date) {
		return date.toLocaleDateString('en-US', {
			year: 'numeric',
			month: 'short',
			day: 'numeric'
		});
	}

	function stcCalendarDate(dateString) {
		return dateString.replace(/-/g, '');
	}

	function stcCalendarEndDate(dateString) {
		var date = new Date(dateString + 'T12:00:00');
		date.setDate(date.getDate() + 1);
		return stcCalendarDate(date.toISOString().slice(0, 10));
	}

	function stcEscapeCalendarText(value) {
		return String(value || '')
			.replace(/\\/g, '\\\\')
			.replace(/\n/g, '\\n')
			.replace(/,/g, '\\,')
			.replace(/;/g, '\\;');
	}

	function stcCalendarFilename(reminder) {
		return 'solotochina-' + reminder.attraction.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') + '.ics';
	}

	function stcBookingWindowStatus(visitDate, reminderDate) {
		var today = new Date();
		today.setHours(0, 0, 0, 0);

		var visitDay = new Date(visitDate.getTime());
		visitDay.setHours(0, 0, 0, 0);

		var reminderDay = new Date(reminderDate.getTime());
		reminderDay.setHours(0, 0, 0, 0);

		if (visitDay < today) {
			return {
				key: 'passed',
				label: 'Date has passed',
				copy: 'Choose a future visit date before checking ticket timing.'
			};
		}

		if (today >= reminderDay) {
			return {
				key: 'book-now',
				label: 'Book now',
				copy: 'You are already inside the recommended ticket-checking window.'
			};
		}

		return {
			key: 'set-reminder',
			label: 'Set reminder',
			copy: 'Save a reminder now and check tickets when the booking window gets closer.'
		};
	}

	function stcDownloadCalendar(reminder) {
		if (!reminder.reminderDate) {
			return;
		}

		var description = [
			'Visit date: ' + reminder.visitDateLabel,
			'City: ' + reminder.city,
			reminder.bookingNote,
			reminder.passportNote
		].filter(Boolean).join('\n');
		var stamp = new Date().toISOString().replace(/[-:]/g, '').replace(/\.\d{3}/, '');
		var calendar = [
			'BEGIN:VCALENDAR',
			'VERSION:2.0',
			'PRODID:-//SoloToChina//Ticket Reminder//EN',
			'CALSCALE:GREGORIAN',
			'BEGIN:VEVENT',
			'UID:' + stcEscapeCalendarText(reminder.id) + '@solotochina',
			'DTSTAMP:' + stamp,
			'DTSTART;VALUE=DATE:' + stcCalendarDate(reminder.reminderDate),
			'DTEND;VALUE=DATE:' + stcCalendarEndDate(reminder.reminderDate),
			'SUMMARY:' + stcEscapeCalendarText('Check ' + reminder.attraction + ' tickets'),
			'DESCRIPTION:' + stcEscapeCalendarText(description),
			'END:VEVENT',
			'END:VCALENDAR'
		].join('\r\n');
		var url = window.URL.createObjectURL(new Blob([calendar], { type: 'text/calendar;charset=utf-8' }));
		var link = document.createElement('a');

		link.href = url;
		link.download = stcCalendarFilename(reminder);
		document.body.append(link);
		link.click();
		link.remove();
		window.URL.revokeObjectURL(url);
	}

	function readReminders() {
		try {
			return JSON.parse(window.localStorage.getItem(storageKey) || '[]');
		} catch (error) {
			return [];
		}
	}

	function writeReminders(reminders) {
		window.localStorage.setItem(storageKey, JSON.stringify(reminders));
	}

	function stcExportReminders() {
		var payload = {
			source: 'SoloToChina',
			exportedAt: new Date().toISOString(),
			reminders: readReminders()
		};
		var url = window.URL.createObjectURL(new Blob([JSON.stringify(payload, null, 2)], { type: 'application/json;charset=utf-8' }));
		var link = document.createElement('a');

		link.href = url;
		link.download = 'solotochina-ticket-reminders.json';
		document.body.append(link);
		link.click();
		link.remove();
		window.URL.revokeObjectURL(url);
	}

	function normalizeImportedReminders(payload) {
		var importedReminders = Array.isArray(payload) ? payload : payload.reminders;

		if (!Array.isArray(importedReminders)) {
			return [];
		}

		return importedReminders.filter(function (reminder) {
			return reminder && reminder.id && reminder.attraction && reminder.city && stcDateValue(reminder.visitDate);
		}).map(function (reminder) {
			var visitDate = stcDateValue(reminder.visitDate);
			var reminderDate = stcDateValue(reminder.reminderDate);

			return {
				id: stcClampText(reminder.id, 120),
				attraction: stcClampText(reminder.attraction, 140),
				city: stcClampText(reminder.city, 80),
				visitDate: visitDate,
				visitDateLabel: stcClampText(reminder.visitDateLabel || visitDate, 40),
				reminderDate: reminderDate,
				reminderDateLabel: stcClampText(reminder.reminderDateLabel || reminderDate, 40),
				bookingStatus: stcClampText(reminder.bookingStatus || '', 40),
				bookingStatusLabel: stcClampText(reminder.bookingStatusLabel || '', 80),
				bookingStatusCopy: stcClampText(reminder.bookingStatusCopy || '', 180),
				bookingNote: stcClampText(reminder.bookingNote || '', 180),
				passportNote: stcClampText(reminder.passportNote || '', 180)
			};
		});
	}

	function stcImportReminders(file, list) {
		var reader = new FileReader();

		reader.addEventListener('load', function () {
			var importedReminders = [];

			try {
				importedReminders = normalizeImportedReminders(JSON.parse(String(reader.result || '{}')));
			} catch (error) {
				window.alert('This saved reminders file could not be imported.');
				return;
			}

			if (!importedReminders.length) {
				window.alert('No saved reminders were found in this file.');
				return;
			}

			var merged = importedReminders.concat(readReminders()).filter(function (reminder, index, reminders) {
				return reminders.findIndex(function (candidate) {
					return candidate.id === reminder.id;
				}) === index;
			});

			writeReminders(merged.slice(0, 12));
			stcRenderReminders(list);
		});

		reader.readAsText(file);
	}

	function getReminderPlan(select, dateInput) {
		if (!dateInput.value) {
			return null;
		}

		var option = select.options[select.selectedIndex];
		var leadDays = parseInt(option.getAttribute('data-lead-days'), 10) || 7;
		var visitDate = new Date(dateInput.value + 'T12:00:00');
		var reminderDate = new Date(visitDate.getTime());
		reminderDate.setDate(reminderDate.getDate() - leadDays);
		var bookingStatus = stcBookingWindowStatus(visitDate, reminderDate);

		return {
			id: option.value + '-' + dateInput.value,
			attraction: option.getAttribute('data-name'),
			city: option.getAttribute('data-city'),
			visitDate: dateInput.value,
			visitDateLabel: formatDate(visitDate),
			reminderDate: reminderDate.toISOString().slice(0, 10),
			reminderDateLabel: formatDate(reminderDate),
			bookingStatus: bookingStatus.key,
			bookingStatusLabel: bookingStatus.label,
			bookingStatusCopy: bookingStatus.copy,
			bookingNote: option.getAttribute('data-booking-note'),
			passportNote: option.getAttribute('data-passport-note')
		};
	}

	function stcRenderReminders(list) {
		var reminders = readReminders();
		list.replaceChildren();

		if (!reminders.length) {
			var empty = document.createElement('p');
			empty.textContent = 'No saved reminders on this device yet.';
			list.append(empty);
			return;
		}

		reminders.forEach(function (reminder) {
			var item = document.createElement('article');
			item.className = 'stc-reminder-item';

			var copy = document.createElement('div');
			var title = document.createElement('strong');
			title.textContent = reminder.attraction + ' - ' + reminder.city;

			var timing = document.createElement('span');
			timing.textContent = 'Check around ' + reminder.reminderDateLabel + ' for ' + reminder.visitDateLabel + '.';

			var actions = document.createElement('div');
			actions.className = 'stc-reminder-actions';

			if (reminder.reminderDate) {
				var calendarButton = document.createElement('button');
				calendarButton.type = 'button';
				calendarButton.setAttribute('data-stc-download-calendar', reminder.id);
				calendarButton.textContent = 'Add to calendar';
				actions.append(calendarButton);
			}

			var deleteButton = document.createElement('button');
			deleteButton.type = 'button';
			deleteButton.setAttribute('data-stc-delete-reminder', reminder.id);
			deleteButton.textContent = 'Delete';

			copy.append(title, timing);
			actions.append(deleteButton);
			item.append(copy, actions);
			list.append(item);
		});
	}

	function stcTicketTool(form) {
		var select = form.querySelector('select[name="stc_attraction"]');
		var dateInput = form.querySelector('input[name="stc_visit_date"]');
		var result = form.querySelector('[data-stc-ticket-result]');
		var saveButton = form.querySelector('[data-stc-save-reminder]');
		var reminderList = form.querySelector('[data-stc-reminder-list]');
		var exportButton = form.querySelector('[data-stc-export-reminders]');
		var importInput = form.querySelector('[data-stc-import-reminders]');
		var clearButton = form.querySelector('[data-stc-clear-reminders]');

		if (!select || !dateInput || !result || !saveButton || !reminderList || !exportButton || !importInput || !clearButton) {
			return;
		}

		form.addEventListener('submit', function (event) {
			event.preventDefault();

			var plan = getReminderPlan(select, dateInput);

			if (!plan) {
				result.textContent = 'Choose a visit date to see the recommended reminder timing.';
				result.classList.add('is-visible');
				return;
			}

			result.replaceChildren();

			var title = document.createElement('strong');
			title.textContent = plan.attraction;

			var status = document.createElement('span');
			status.className = 'stc-ticket-status stc-ticket-status--' + plan.bookingStatus;
			status.textContent = plan.bookingStatusLabel;

			var timing = document.createElement('span');
			timing.textContent = 'Plan to check tickets around ' + plan.reminderDateLabel + ' for a ' + plan.visitDateLabel + ' visit.';

			var guidance = document.createElement('span');
			guidance.textContent = plan.bookingStatusCopy;

			var note = document.createElement('span');
			note.textContent = plan.bookingNote + '. ' + plan.passportNote + '.';

			result.append(title, status, timing, guidance, note);
			result.classList.add('is-visible');
		});

		saveButton.addEventListener('click', function () {
			var plan = getReminderPlan(select, dateInput);

			if (!plan) {
				result.textContent = 'Choose a visit date before saving a reminder.';
				result.classList.add('is-visible');
				return;
			}

			if (plan.bookingStatus === 'passed') {
				result.textContent = 'Choose a future visit date before saving a reminder.';
				result.classList.add('is-visible');
				return;
			}

			var reminders = readReminders().filter(function (reminder) {
				return reminder.id !== plan.id;
			});

			reminders.unshift(plan);
			writeReminders(reminders.slice(0, 12));
			stcRenderReminders(reminderList);

			result.textContent = 'Reminder saved on this device. No login required.';
			result.classList.add('is-visible');
		});

		exportButton.addEventListener('click', stcExportReminders);

		importInput.addEventListener('change', function () {
			if (importInput.files && importInput.files[0]) {
				stcImportReminders(importInput.files[0], reminderList);
			}

			importInput.value = '';
		});

		clearButton.addEventListener('click', function () {
			if (!readReminders().length || !window.confirm('Clear all saved reminders on this device?')) {
				return;
			}

			writeReminders([]);
			stcRenderReminders(reminderList);
		});

		reminderList.addEventListener('click', function (event) {
			var calendarButton = event.target.closest('[data-stc-download-calendar]');
			var deleteButton = event.target.closest('[data-stc-delete-reminder]');

			if (calendarButton) {
				var reminder = readReminders().filter(function (savedReminder) {
					return savedReminder.id === calendarButton.getAttribute('data-stc-download-calendar');
				})[0];

				if (reminder) {
					stcDownloadCalendar(reminder);
				}
				return;
			}

			if (!deleteButton) {
				return;
			}

			writeReminders(readReminders().filter(function (reminder) {
				return reminder.id !== deleteButton.getAttribute('data-stc-delete-reminder');
			}));
			stcRenderReminders(reminderList);
		});

		stcRenderReminders(reminderList);
	}

	document.querySelectorAll('[data-stc-ticket-tool]').forEach(stcTicketTool);
})();
