(function () {
	document.documentElement.classList.add('stc-tools-js');

	function formatDate(date) {
		return date.toLocaleDateString('en-US', {
			year: 'numeric',
			month: 'long',
			day: 'numeric', timeZone: 'UTC'
		});
	}

	function bookingWindowStatus(visitDate, bookingDate) {
		var chinaDate = new Intl.DateTimeFormat('en-CA', {timeZone:'Asia/Shanghai',year:'numeric',month:'2-digit',day:'2-digit'}).format(new Date());
		var today = new Date(chinaDate + 'T00:00:00Z');

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
				label: 'SUGGESTED CHECKING DATE REACHED',
				copy: 'Your estimated booking window has started. Check the latest ticket information now.'
			};
		}

		return {
			key: 'not-open',
			label: 'CHECKING DATE AHEAD',
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
		var visitDate = new Date(dateInput.value + 'T00:00:00Z');

		if (Number.isNaN(visitDate.getTime()) || !Number.isFinite(leadDays) || leadDays < 0) {
			return null;
		}

		var validUntil = option.getAttribute('data-rule-valid-until');
		var checkedAt = option.getAttribute('data-rule-checked-at'), ruleSource = option.getAttribute('data-rule-source');
        var ruleKnown = Boolean(checkedAt && /^\d{4}-\d{2}-\d{2}$/.test(checkedAt) && checkedAt <= new Date().toISOString().slice(0,10) && /^https:\/\//.test(ruleSource || '') && validUntil && /^\d{4}-\d{2}-\d{2}$/.test(validUntil) && new Date(validUntil + 'T23:59:59+08:00') >= new Date());
		var bookingDate = new Date(visitDate.getTime());
		bookingDate.setUTCDate(bookingDate.getUTCDate() - leadDays);

		return {
			attraction: option.getAttribute('data-name'),
			visitDate: visitDate,
			bookingDate: bookingDate,
			leadDays: leadDays,
            checkedAt: checkedAt, ruleSource: ruleSource,
			status: bookingWindowStatus(visitDate, bookingDate).key === 'passed' ? bookingWindowStatus(visitDate, bookingDate) : ruleKnown ? bookingWindowStatus(visitDate, bookingDate) : {key:'unknown',label:'RULE NOT CONFIRMED',copy:'There is no current reviewed booking rule for this place. Check its official guide before planning around an opening date.'}
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

			if (plan.status.key !== 'passed' && plan.status.key !== 'unknown') {
				appendLine(result, 'stc-ticket-result__label', 'Recommended time to start checking');
				appendLine(result, 'stc-ticket-result__date', 'Around ' + formatDate(plan.bookingDate), 'strong');
				appendLine(result, 'stc-ticket-result__rule', 'Planning estimate: begin checking ' + plan.leadDays + ' days ahead. This is not an exact sale time.');
			}

			appendLine(result, 'stc-ticket-result__guidance', plan.status.copy);
            if(plan.checkedAt && /^https:\/\//.test(plan.ruleSource || '')) {var source=document.createElement('a');source.href=plan.ruleSource;source.textContent='Rule source, reviewed '+plan.checkedAt;source.rel='noopener';result.append(source);}
			result.classList.add('is-visible');
			var ticketUrl = select.options[select.selectedIndex].getAttribute('data-ticket-url');
			bookingLink.hidden = plan.status.key === 'passed' || !ticketUrl;
			if (ticketUrl) { bookingLink.href = ticketUrl; }
			disclosure.hidden = bookingLink.hidden;
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
		var payload = await response.json().catch(function () { return {message:'The service returned an unreadable response. Try again shortly.'}; });
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
		card.append(element('p','stc-place-result-card__precision','Match level: ' + (place.match_level || 'unknown').replace(/_/g,' ')));
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
		if (place.entity_key && place.sources && place.sources.name_zh === 'VERIFIED') {
			var action = element('button', 'stc-tool-primary', 'Show Taxi Card');
			action.type = 'button'; action.addEventListener('click', function () { taxiHandoff(place); }); card.append(action);
		} else { card.append(element('p', 'stc-place-result-card__unverified', 'Taxi Card is available after the destination is verified.')); }
		container.append(card);
	}

    function copyText(text, status, button) {
        var container = button.closest('[data-stc-driver-mode]') || button.closest('[data-stc-taxi-tool]');
        var done = function () { status.textContent = 'Copied'; };
        function manual() {
            var field = container.querySelector('[data-manual-copy]');
            if (!field) { field = element('textarea', 'stc-manual-copy'); field.dataset.manualCopy = ''; field.readOnly = true; field.setAttribute('aria-label','Select and copy destination'); container.append(field); }
            field.value = text; field.focus(); field.select();
            try { if (document.execCommand('copy')) { done(); return; } } catch (_) {}
            status.textContent = 'Copy was blocked. Select the destination text and copy it manually.';
        }
        if (navigator.clipboard && window.isSecureContext) { Promise.resolve().then(function(){return navigator.clipboard.writeText(text);}).then(done).catch(manual); } else { manual(); }
    }

    function initTaxiCard(root) {
        var form = root.querySelector('[data-stc-taxi-form]'), input = form.querySelector('[name="query"]'), submit = form.querySelector('button[type=submit]');
        var status = root.querySelector('[data-stc-taxi-status]'), result = root.querySelector('[data-stc-taxi-result]');
        var card = root.querySelector('[data-stc-taxi-card-output]'), choices = root.querySelector('[data-stc-taxi-choices]'), driver = root.querySelector('[data-stc-driver-mode]');
        var serial = 0, request = null, copyValue = '', lastFocus;
        function arrival(place) {
            var sources = place.sources || {};
            return [sources.dropoff === 'VERIFIED' ? place.recommended_dropoff_zh : '', sources.entrance === 'VERIFIED' ? place.recommended_entrance_zh : '', sources.address === 'VERIFIED' ? place.verified_address_zh : '', sources.arrival_note === 'VERIFIED' ? place.arrival_note : ''].filter(Boolean).join('\n');
        }
        function show(place) {
            if (!place || !place.entity_key || !place.name_zh || !place.sources || place.sources.name_zh !== 'VERIFIED') { throw new Error('We could not confirm this destination name. Add its city or full name.'); }
            var details = arrival(place);
            copyValue = [place.name_zh, place.city_zh, details].filter(Boolean).join('\n');
            card.hidden = false; result.hidden = false; choices.hidden = true;
            card.querySelector('[data-stc-taxi-name-zh]').textContent = place.name_zh;
            card.querySelector('[data-stc-taxi-name-en]').textContent = place.name_en || '';
            card.querySelector('[data-stc-taxi-city-en]').textContent = place.city_en || '';
            card.querySelector('[data-stc-taxi-address]').textContent = [place.city_zh,details].filter(Boolean).join('\n');
            var note = card.querySelector('[data-stc-arrival-status]');
            note.textContent = place.sources.dropoff === 'VERIFIED' || place.sources.entrance === 'VERIFIED' ? 'Arrival point confirmed in the destination record.' : details ? 'Official address recorded. Visitor entrance and vehicle drop-off are not confirmed.' : 'Chinese name confirmed. ' + (place.sources.city === 'VERIFIED' ? 'City confirmed. ' : 'City not verified. ') + 'Specific address, entrance and drop-off are not verified.';
            status.textContent = note.textContent; root.dataset.state = 'success';
        }
        function choose(candidates) {
            choices.replaceChildren(element('h3', '', 'Which place do you mean?'));
            candidates.slice(0,4).forEach(function (place) { var button = element('button','stc-place-choice',place.name_en + ' · ' + place.city_en + ' · ' + place.name_zh); button.type='button'; button.addEventListener('click',function(){show(place);});choices.append(button); });
            choices.hidden=false; result.hidden=true; root.dataset.state='ambiguous'; status.textContent='Choose the matching city and destination.';
        }
        function invalidate() { serial++; if(request){request.abort();request=null;}submit.disabled=false; }
        input.addEventListener('input',function(){invalidate();result.hidden=true;choices.hidden=true;copyValue='';root.dataset.state='idle';status.textContent='';});
        form.addEventListener('submit',async function(event){
            event.preventDefault(); if(request){return;}var query=input.value.trim();if(!query){status.textContent='Enter a destination name.';return;}
            invalidate();var version=serial,controller=new AbortController();request=controller;submit.disabled=true;result.hidden=true;choices.hidden=true;root.dataset.state='loading';status.textContent='Checking the destination…';
            var timeout=setTimeout(function(){controller.abort();},20000);
            try {
                var payload=await jsonRequest(config.taxiEndpoint,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({query:query}),signal:controller.signal});
                if(version!==serial){return;}
                if(payload.status==='ambiguous' && Array.isArray(payload.candidates) && payload.candidates.length){choose(payload.candidates);}else{show(payload.place);}
            }catch(error){if(version===serial){root.dataset.state='error';status.textContent=error.name==='AbortError'?'The lookup took too long. Try again.':error.message;}}
            finally{clearTimeout(timeout);if(version===serial){request=null;submit.disabled=false;}}
        });
        if(root.getAttribute('data-initial-entity') && !result.hidden){copyValue=[card.querySelector('[data-stc-taxi-name-zh]').textContent,card.querySelector('[data-stc-taxi-address]').textContent].filter(Boolean).join('\n');}
        root.querySelector('[data-stc-taxi-copy]').addEventListener('click',function(event){if(copyValue){copyText(copyValue,status,event.currentTarget);}});
        var driverStatus=driver.querySelector('[data-stc-driver-status]');
        var driverActions=driver.querySelector('[data-stc-driver-copy]').parentNode;
        driverActions.classList.add('stc-driver-mode__actions');
        driver.querySelector('.stc-driver-mode__inner').prepend(driverActions);
        function closed(){driver.hidden=true;document.body.classList.remove('stc-driver-mode-open');if(lastFocus){lastFocus.focus();}}
        function closeDriver(){if(driver.open){driver.close();}else{closed();}}
        root.querySelector('[data-stc-taxi-fullscreen]').addEventListener('click',function(event){
            if(!copyValue){return;}lastFocus=event.currentTarget;
            driver.querySelector('[data-stc-driver-name-zh]').textContent=card.querySelector('[data-stc-taxi-name-zh]').textContent;
            driver.classList.toggle('has-long-name',driver.querySelector('[data-stc-driver-name-zh]').textContent.length>18);
            driver.querySelector('[data-stc-driver-address]').textContent=card.querySelector('[data-stc-taxi-address]').textContent;
            driver.querySelector('[data-stc-driver-name-en]').textContent=card.querySelector('[data-stc-taxi-name-en]').textContent;
            driver.querySelector('[data-stc-driver-arrival]').textContent=card.querySelector('[data-stc-arrival-status]').textContent;
            driver.hidden=false;driver.showModal();driver.scrollTop=0;document.body.classList.add('stc-driver-mode-open');driver.querySelector('[data-stc-driver-close]').focus();
        });
        driver.querySelector('[data-stc-driver-copy]').addEventListener('click',function(event){copyText(copyValue,driverStatus,event.currentTarget);});
        driver.querySelector('[data-stc-driver-close]').addEventListener('click',closeDriver);
        driver.addEventListener('close',closed);
        window.addEventListener('pagehide',function(){invalidate();closeDriver();});
    }

	window.stcToolUI = { element: element, jsonRequest: jsonRequest, renderPlaceCard: renderPlaceCard };
	document.querySelectorAll('[data-stc-taxi-tool]').forEach(initTaxiCard);
})();
