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

	var config = window.stcToolsConfig || {};

	function element(name, className, text) {
		var node = document.createElement(name);
		if (className) { node.className = className; }
		if (typeof text === 'string') { node.textContent = text; }
		return node;
	}

	function errorMessage(error, fallback) {
		return error && error.message ? error.message : fallback;
	}

	async function jsonRequest(url, options) {
		var response = await fetch(url, Object.assign({ credentials: 'same-origin', cache: 'no-store' }, options || {}));
		var payload = await response.json().catch(function () { return {}; });
		if (!response.ok) { throw new Error(payload.message || 'The request could not be completed.'); }
		return payload;
	}

	function taxiDestination(place) {
		var addressVerified = place.sources && place.sources.address === 'VERIFIED' && place.verified_address_zh;
		return [place.name_zh, addressVerified ? place.verified_address_zh : place.city_zh].filter(Boolean).join('\n');
	}

	function taxiHandoff(place) {
		if (place.entity_key) { window.location.href = config.taxiUrl + '?entity_key=' + encodeURIComponent(place.entity_key); }
	}

	function renderPlaceCard(container, place, confidenceLabel) {
		container.replaceChildren();
		var card = element('article', 'stc-place-result-card');
		card.append(element('p', 'stc-place-result-card__confidence stc-place-result-card__confidence--' + (place.confidence || 'medium'), confidenceLabel));
		card.append(element('h3', '', place.name_en || 'Likely place'));
		if (place.name_zh) { card.append(element('p', 'stc-place-result-card__zh', place.name_zh)); }
		var location = [place.city_en, place.province_en && place.province_en !== place.city_en ? place.province_en : ''].filter(Boolean).join(' · ');
		if (location) { card.append(element('p', 'stc-place-result-card__location', location)); }
		var sourceLabel = function (value) { return value === 'VERIFIED' ? 'Verified' : (value === 'AI_INFERRED' ? 'AI inferred' : 'Not verified'); };
		var provenance = element('p', 'stc-place-result-card__sources', 'Chinese name: ' + sourceLabel(place.sources && place.sources.name_zh) + ' · City: ' + sourceLabel(place.sources && place.sources.city));
		card.append(provenance);
		var viewpoint = element('div', 'stc-place-result-card__section');
		viewpoint.append(element('strong', '', place.viewpoint_status === 'verified' ? 'Verified photo spot' : (place.viewpoint_status === 'likely' ? 'Likely photo spot' : 'Photo spot not confirmed')));
		viewpoint.append(element('p', '', place.viewpoint_name_en || place.viewpoint_name_zh || 'Not confirmed'));
		card.append(viewpoint);
		if (place.recognition_reason) { var reason = element('div', 'stc-place-result-card__section'); reason.append(element('strong', '', 'Why this may be the place')); reason.append(element('p', '', place.recognition_reason)); card.append(reason); }
		var plan = element('div', 'stc-place-result-card__section stc-place-result-card__visit');
		plan.append(element('strong', '', 'Plan your visit'));
		plan.append(element('p', '', 'Chinese destination: ' + (place.name_zh || 'Not verified')));
		if (!(place.sources && place.sources.address === 'VERIFIED')) { plan.append(element('p', 'stc-place-result-card__unverified', 'Address not verified')); }
		card.append(plan);
		var guides = [place.related_attraction_guide, place.related_city_guide].filter(Boolean);
		if (guides.length) {
			var related = element('div', 'stc-place-result-card__section stc-place-result-card__guides');
			related.append(element('strong', '', 'Related SoloToChina guides'));
			guides.forEach(function (guide) { var link = element('a', '', guide.title); link.href = guide.url; related.append(link); });
			card.append(related);
		}
		if (place.entity_key) {
			var action = element('button', 'stc-tool-primary', 'Show Taxi Card');
			action.type = 'button'; action.addEventListener('click', function () { taxiHandoff(place); }); card.append(action);
		} else { card.append(element('p', 'stc-place-result-card__unverified', 'Taxi Card is available after the destination is verified.')); }
		container.append(card);
	}

	function initPlaceFinder(root) {
		var form = root.querySelector('[data-stc-place-form]');
		var input = root.querySelector('[data-stc-place-input]');
		var dropzone = root.querySelector('[data-stc-place-dropzone]');
		var preview = root.querySelector('[data-stc-place-preview]');
		var previewList = root.querySelector('[data-stc-place-previews]');
		var submit = root.querySelector('[data-stc-place-submit]');
		var status = root.querySelector('[data-stc-place-status]');
		var result = root.querySelector('[data-stc-place-result]');
		var cityHint = root.querySelector('[data-stc-city-hint]');
		var currentFiles = [];
		var previewUrls = [];
		var loadVersion = 0;
		var maxImageBytes = Number(config.maxImageBytes || 20971520);
		var maxImageCount = Number(config.maxImageCount || 4);
		var maxUploadBytes = Number(config.maxUploadBytes || 62914560);

		function announce(message, isError) { status.textContent = message; status.classList.toggle('is-error', Boolean(isError)); }
		function revokePreviews() { previewUrls.forEach(function (url) { URL.revokeObjectURL(url); }); previewUrls = []; }
		function clearFiles() { loadVersion += 1; currentFiles = []; input.value = ''; submit.disabled = true; preview.hidden = true; dropzone.classList.remove('has-preview'); revokePreviews(); previewList.replaceChildren(); }
		function inspectFile(file, url) {
			return new Promise(function (resolve, reject) {
				var probe = new Image();
				probe.onload = function () {
					if (probe.naturalWidth > 12000 || probe.naturalHeight > 12000 || probe.naturalWidth * probe.naturalHeight > 40000000) { URL.revokeObjectURL(url); reject(new Error('One of these photos has dimensions that are too large to process safely.')); return; }
					resolve({ file: file, url: url, width: probe.naturalWidth, height: probe.naturalHeight });
				};
				probe.onerror = function () { URL.revokeObjectURL(url); reject(new Error('One of these photos appears corrupted. Try another file.')); };
				probe.src = url;
			});
		}
		function useFiles(files) {
			var selected = Array.from(files || []);
			var version = loadVersion + 1;
			loadVersion = version;
			announce('', false); result.replaceChildren(); cityHint.hidden = true;
			if (!selected.length || selected.length > maxImageCount) { clearFiles(); announce('Choose between 1 and 4 photos of the same place.', true); return; }
			if (selected.some(function (file) { return ['image/jpeg', 'image/png', 'image/webp'].indexOf(file.type) === -1; })) { clearFiles(); announce('Every file must be a JPG, PNG, or WebP image.', true); return; }
			if (selected.some(function (file) { return file.size < 1 || file.size > maxImageBytes; })) { clearFiles(); announce('Each photo must be 20 MB or smaller.', true); return; }
			if (selected.reduce(function (total, file) { return total + file.size; }, 0) > maxUploadBytes) { clearFiles(); announce('The selected photos must total 60 MB or less.', true); return; }
			var candidateUrls = selected.map(function (file) { return URL.createObjectURL(file); });
			Promise.all(selected.map(function (file, index) { return inspectFile(file, candidateUrls[index]); })).then(function (items) {
				if (version !== loadVersion) { items.forEach(function (item) { URL.revokeObjectURL(item.url); }); return; }
				revokePreviews(); previewList.replaceChildren(); currentFiles = selected; previewUrls = items.map(function (item) { return item.url; });
				items.forEach(function (item, index) {
					var card = element('figure', 'stc-place-upload__preview-card');
					var image = document.createElement('img'); image.src = item.url; image.alt = 'Preview ' + (index + 1) + ' of ' + selected.length + ': ' + item.file.name;
					var caption = document.createElement('figcaption'); caption.append(element('strong', '', item.file.name)); caption.append(element('span', '', item.width + ' × ' + item.height));
					card.append(image, caption); previewList.append(card);
				});
				preview.hidden = false; dropzone.classList.add('has-preview'); submit.disabled = false;
				announce(selected.length === 1 ? '1 photo selected. Add different angles if you have them.' : selected.length + ' photos selected for one combined identification.', false);
			}).catch(function (error) { candidateUrls.forEach(function (url) { URL.revokeObjectURL(url); }); if (version === loadVersion) { clearFiles(); announce(error.message || 'One of these photos could not be read.', true); } });
		}

		input.addEventListener('change', function () { useFiles(input.files); });
		root.querySelector('[data-stc-place-change]').addEventListener('click', function () { input.click(); });
		root.querySelector('[data-stc-place-remove]').addEventListener('click', clearFiles);
		['dragenter', 'dragover'].forEach(function (name) { dropzone.addEventListener(name, function (event) { event.preventDefault(); dropzone.classList.add('is-dragging'); }); });
		['dragleave', 'drop'].forEach(function (name) { dropzone.addEventListener(name, function (event) { event.preventDefault(); dropzone.classList.remove('is-dragging'); }); });
		dropzone.addEventListener('drop', function (event) { useFiles(event.dataTransfer && event.dataTransfer.files); });
		document.addEventListener('paste', function (event) { if (!root.isConnected) { return; } var items = event.clipboardData ? Array.from(event.clipboardData.items) : []; var imageFiles = items.filter(function (item) { return item.type.indexOf('image/') === 0; }).map(function (item) { return item.getAsFile(); }).filter(Boolean); if (imageFiles.length) { useFiles(imageFiles); } });

		form.addEventListener('submit', async function (event) {
			event.preventDefault(); if (!currentFiles.length) { announce('Choose at least one photo or screenshot first.', true); return; }
			submit.disabled = true; root.classList.add('is-loading'); announce('Finding the place…', false); result.replaceChildren();
			var body = new FormData(); currentFiles.forEach(function (file) { body.append('images[]', file, file.name); }); var hint = form.querySelector('[name="city_hint"]'); if (hint && hint.value.trim()) { body.append('city_hint', hint.value.trim()); }
			try {
				var payload = await jsonRequest(config.placeEndpoint, { method: 'POST', body: body });
				if (payload.confidence === 'medium' && payload.alternative_candidates && payload.alternative_candidates.length) {
					var chooser = element('section', 'stc-place-choices'); chooser.append(element('h3', '', 'This looks like one of these places:')); chooser.append(element('p', '', 'Choose the closest match.'));
					payload.alternative_candidates.forEach(function (candidate) { var button = element('button', 'stc-place-choice', (candidate.name_en || candidate.name_zh) + (candidate.city_en ? ' — ' + candidate.city_en : '')); button.type = 'button'; button.addEventListener('click', function () { renderPlaceCard(result, candidate, 'LIKELY MATCH'); }); chooser.append(button); }); result.append(chooser); announce('Choose the closest match.', false);
				} else if (payload.primary_candidate) { renderPlaceCard(result, payload.primary_candidate, payload.confidence === 'high' ? 'HIGH CONFIDENCE' : 'POSSIBLE MATCH'); announce('Likely place found.', false); }
				else { var low = element('section', 'stc-place-low'); low.append(element('h3', '', 'We couldn’t identify this place confidently.')); ['Upload 2–4 photos from different angles', 'Include an entrance, sign, or nearby street', 'Add a wider view and an optional city hint'].forEach(function (copy) { low.append(element('p', '', '• ' + copy)); }); result.append(low); cityHint.hidden = false; announce('Try a different photo set or add an optional city hint.', true); }
			} catch (error) { announce(errorMessage(error, 'Place identification is temporarily unavailable.'), true); }
			finally { root.classList.remove('is-loading'); submit.disabled = !currentFiles.length; }
		});
	}

	function copyText(text, status, button) {
		var done = function () { if (status) { status.textContent = 'Copied ✓'; } if (button) { var old = button.textContent; button.textContent = 'Copied ✓'; window.setTimeout(function () { button.textContent = old; }, 1800); } };
		if (navigator.clipboard && window.isSecureContext) { navigator.clipboard.writeText(text).then(done).catch(function () { fallbackCopy(text, done); }); } else { fallbackCopy(text, done); }
	}

	function fallbackCopy(text, done) { var field = document.createElement('textarea'); field.value = text; field.setAttribute('readonly', ''); field.style.position = 'fixed'; field.style.left = '-9999px'; document.body.append(field); field.select(); try { if (document.execCommand('copy')) { done(); } } finally { field.remove(); } }

	function initTaxiCard(root) {
		var form = root.querySelector('[data-stc-taxi-form]'); var status = root.querySelector('[data-stc-taxi-status]'); var result = root.querySelector('[data-stc-taxi-result]'); var card = root.querySelector('[data-stc-taxi-card-output]'); var choices = root.querySelector('[data-stc-taxi-choices]'); var driver = root.querySelector('[data-stc-driver-mode]'); var lastFocus = null; var copyValue = '';
		function show(place, suppliedCopy) {
			copyValue = suppliedCopy || taxiDestination(place); card.hidden = false; result.hidden = false; choices.hidden = true;
			card.querySelector('[data-stc-taxi-name-zh]').textContent = place.name_zh; card.querySelector('[data-stc-taxi-name-en]').textContent = place.name_en || ''; card.querySelector('[data-stc-taxi-city-en]').textContent = place.city_en || '';
			card.querySelector('[data-stc-taxi-address]').textContent = place.sources && place.sources.address === 'VERIFIED' && place.verified_address_zh ? place.verified_address_zh : place.city_zh;
			status.textContent = 'Taxi card ready.'; result.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'nearest' });
		}
		function choose(candidates) { choices.replaceChildren(element('h3', '', 'Which place do you mean?')); candidates.forEach(function (place) { var button = element('button', 'stc-place-choice', place.name_en + ' — ' + place.city_en + ' · ' + place.name_zh); button.type = 'button'; button.addEventListener('click', function () { show(place); }); choices.append(button); }); choices.hidden = false; result.hidden = true; status.textContent = 'Choose the closest destination.'; }
		form.addEventListener('submit', async function (event) { event.preventDefault(); var query = form.querySelector('[name="query"]').value.trim(); if (!query) { status.textContent = 'Enter a destination name.'; return; } status.textContent = 'Resolving the destination…'; try { var payload = await jsonRequest(config.taxiEndpoint, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ query: query }) }); if (payload.status === 'ambiguous') { choose(payload.candidates || []); } else { show(payload.place, payload.copy_text); } } catch (error) { result.hidden = true; choices.hidden = true; status.textContent = errorMessage(error, 'We could not verify that destination. Try its full place name and city.'); } });
		var initialEntity = root.getAttribute('data-initial-entity'); if (initialEntity && !result.hidden) { copyValue = [card.querySelector('[data-stc-taxi-name-zh]').textContent, card.querySelector('[data-stc-taxi-address]').textContent].filter(Boolean).join('\n'); }
		root.querySelector('[data-stc-taxi-copy]').addEventListener('click', function (event) { if (copyValue) { copyText(copyValue, status, event.currentTarget); } });
		function closeDriver() { driver.hidden = true; document.body.classList.remove('stc-driver-mode-open'); if (lastFocus) { lastFocus.focus(); } }
		root.querySelector('[data-stc-taxi-fullscreen]').addEventListener('click', function (event) { if (!copyValue) { return; } lastFocus = event.currentTarget; driver.querySelector('[data-stc-driver-name-zh]').textContent = card.querySelector('[data-stc-taxi-name-zh]').textContent; driver.querySelector('[data-stc-driver-address]').textContent = card.querySelector('[data-stc-taxi-address]').textContent; driver.querySelector('[data-stc-driver-name-en]').textContent = card.querySelector('[data-stc-taxi-name-en]').textContent; driver.hidden = false; document.body.classList.add('stc-driver-mode-open'); driver.querySelector('[data-stc-driver-close]').focus(); });
		driver.querySelector('[data-stc-driver-copy]').addEventListener('click', function (event) { copyText(copyValue, status, event.currentTarget); }); driver.querySelector('[data-stc-driver-close]').addEventListener('click', closeDriver);
		driver.addEventListener('keydown', function (event) { if (event.key === 'Escape') { closeDriver(); } if (event.key === 'Tab') { var controls = Array.from(driver.querySelectorAll('button')); var index = controls.indexOf(document.activeElement); if (event.shiftKey && index === 0) { event.preventDefault(); controls[controls.length - 1].focus(); } else if (!event.shiftKey && index === controls.length - 1) { event.preventDefault(); controls[0].focus(); } } });
	}

	document.querySelectorAll('[data-stc-place-finder]').forEach(initPlaceFinder);
	document.querySelectorAll('[data-stc-taxi-tool]').forEach(initTaxiCard);
})();
