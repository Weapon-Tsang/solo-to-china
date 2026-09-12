(function () {
    'use strict';
    var api = window.stcToolUI;
    if (!api) { return; }
    var config = window.stcToolsConfig || {};
    var el = api.element;

    // Decode one image at a time. Canvas export strips metadata; originals never leave the device.
    async function recognitionCopy(file) {
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
            throw new Error('Use a JPG, PNG or WebP image. For HEIC, take a screenshot first.');
        }
        if (!file.size || file.size > Number(config.maxImageBytes || 20971520)) { throw new Error('Each source photo must be 20 MB or smaller.'); }
        var url = URL.createObjectURL(file), image = new Image(), canvas;
        try {
            image.src = url;
            await new Promise(function (resolve, reject) { image.onload = resolve; image.onerror = function () { reject(new Error('This image could not be decoded. Try a screenshot or another photo.')); }; });
            var width = image.naturalWidth, height = image.naturalHeight;
            if (!width || !height || width * height > 40000000 || Math.max(width, height) > 12000) { throw new Error('This image has too many pixels. Crop or resize it before trying again.'); }
            // Keep long screenshots readable, with a separate bounded 6MP output budget.
            var textImage = file.type === 'image/png' || Math.max(width, height) / Math.min(width, height) > 3;
            var longest = textImage ? 4096 : 2048;
            var scale = Math.min(1, longest / Math.max(width, height), Math.sqrt(6000000 / (width * height)));
            canvas = document.createElement('canvas');
            canvas.width = Math.max(1, Math.round(width * scale)); canvas.height = Math.max(1, Math.round(height * scale));
            var ctx = canvas.getContext('2d');
            if (!ctx || !canvas.toBlob) { throw new Error('Your browser cannot prepare this image. Try a recent browser or a smaller screenshot.'); }
            ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, canvas.width, canvas.height); ctx.drawImage(image, 0, 0, canvas.width, canvas.height);
            var blob = await new Promise(function (resolve) { canvas.toBlob(resolve, textImage ? 'image/webp' : 'image/jpeg', textImage ? 0.92 : 0.86); });
            if (!blob || blob.size > 2500000) { throw new Error('This image is still too large after preparation. Crop the important area and try again.'); }
            return { file: new File([blob], 'recognition.' + (blob.type === 'image/webp' ? 'webp' : blob.type === 'image/png' ? 'png' : 'jpg'), {type:blob.type}), width:canvas.width, height:canvas.height };
        } finally { URL.revokeObjectURL(url); image.src = ''; if (canvas) { canvas.width = canvas.height = 1; } }
    }

    function init(root) {
        var form = root.querySelector('[data-stc-place-form]'), input = root.querySelector('[data-stc-place-input]');
        var zone = root.querySelector('[data-stc-place-dropzone]'), previews = root.querySelector('[data-stc-place-previews]');
        var preview = root.querySelector('[data-stc-place-preview]'), submit = root.querySelector('[data-stc-place-submit]');
        var status = root.querySelector('[data-stc-place-status]'), result = root.querySelector('[data-stc-place-result]');
        var hint = form.querySelector('[name="city_hint"]'), cancel = root.querySelector('[data-stc-place-cancel]');
        var items = [], generation = 0, request = null, processing = false;
        var cancelPreparation = null;
        function prepare(file) {
            if (!window.Worker || !window.OffscreenCanvas || !window.createImageBitmap || !config.imageWorkerUrl) { return recognitionCopy(file); }
            return new Promise(function(resolve,reject) {
                var worker;
                try { worker=new Worker(config.imageWorkerUrl); } catch (_) { recognitionCopy(file).then(resolve,reject); return; }
                function finish() { worker.terminate();cancelPreparation=null; }
                cancelPreparation=function(){finish();reject(new Error('Preparation cancelled.'));};
                worker.onerror=function(){finish();recognitionCopy(file).then(resolve,reject);};
                worker.onmessage=function(event){
                    finish();var data=event.data;
                    if(data.error){reject(new Error(data.error));return;}
                    var ext=data.blob.type==='image/webp'?'webp':data.blob.type==='image/png'?'png':'jpg';
                    resolve({file:new File([data.blob],'recognition.'+ext,{type:data.blob.type}),width:data.width,height:data.height});
                };
                worker.postMessage(file);
            });
        }
        var configured = root.getAttribute('data-service-configured') !== 'false';
        function state(name, message) {
            root.dataset.state = name; status.textContent = message || '';
            status.classList.toggle('is-error', name === 'error');
            root.classList.toggle('is-loading', name === 'identifying' || name === 'processing');
            submit.disabled = !configured || !items.length || processing || Boolean(request);
            cancel.hidden = !(request || processing);
        }
        function invalidate() {
            if (cancelPreparation) { cancelPreparation(); }
            generation++; if (request) { request.abort(); request = null; }
            processing = false; result.replaceChildren();
        }
        function renderPreviews() {
            previews.replaceChildren(); preview.hidden = !items.length; zone.classList.toggle('has-preview', Boolean(items.length));
            items.forEach(function (item, index) {
                var figure = el('figure', 'stc-place-upload__preview-card');
                var image = el('img'); image.src = item.url; image.alt = 'Selected photo ' + (index + 1); image.width = item.width; image.height = item.height;
                var caption = el('figcaption', '', 'Photo ' + (index + 1) + ' · ' + Math.round(item.file.size / 1024) + ' KB ready');
                var remove = el('button', '', 'Remove photo ' + (index + 1)); remove.type = 'button';
                remove.addEventListener('click', function () { invalidate(); URL.revokeObjectURL(item.url); items.splice(index, 1); renderPreviews(); state('idle', items.length + ' photos selected.'); input.focus(); });
                figure.append(image, caption, remove);
                if (index > 0) {
                    var move = el('button', '', 'Move earlier'); move.type = 'button'; move.setAttribute('aria-label', 'Move photo ' + (index + 1) + ' earlier');
                    move.addEventListener('click', function () { invalidate(); items.splice(index, 1); items.splice(index - 1, 0, item); renderPreviews(); state('idle', 'Photo order updated.'); input.focus(); }); figure.append(move);
                }
                previews.append(figure);
            });
        }
        async function add(files) {
            var selected = Array.from(files || []);
            if (!selected.length) { return; }
            invalidate(); var version = generation;
            if (items.length + selected.length > 4) { state('error', 'You can add up to 4 photos. Remove one before adding more.'); return; }
            processing = true; state('processing', 'Preparing a smaller, private copy of each photo…');
            try {
                for (var file of selected) {
                    // Yield between decodes so controls and cancel remain responsive.
                    await new Promise(function (resolve) { requestAnimationFrame(resolve); });
                    if (version !== generation) { return; }
                    var item = await prepare(file);
                    if (version !== generation) { return; }
                    item.url = URL.createObjectURL(item.file); items.push(item); renderPreviews();
                }
                processing = false; state('idle', items.length + ' photos ready. You can add another angle or a city hint.');
            } catch (error) { if (version === generation) { processing = false; state('error', error.message); } }
            finally { if (version === generation) { input.value = ''; } }
        }
        input.addEventListener('change', function () { add(input.files); });
        root.querySelector('[data-stc-place-change]').addEventListener('click', function () { input.click(); });
        root.querySelector('[data-stc-place-remove]').addEventListener('click', function () { invalidate(); items.forEach(function (item) { URL.revokeObjectURL(item.url); }); items = []; input.value = ''; renderPreviews(); state('idle', 'Photos removed.'); input.focus(); });
        hint.addEventListener('input', function () { invalidate(); state('idle', 'City hint updated. Submit when ready.'); });
        cancel.addEventListener('click', function () { invalidate(); state('cancelled', 'Cancelled. Your prepared photos are still here.'); });
        ['dragenter','dragover'].forEach(function (name) { zone.addEventListener(name, function (event) { event.preventDefault(); zone.classList.add('is-dragging'); }); });
        ['dragleave','drop'].forEach(function (name) { zone.addEventListener(name, function (event) { event.preventDefault(); zone.classList.remove('is-dragging'); }); });
        zone.addEventListener('drop', function (event) { add(event.dataTransfer.files); });
        root.addEventListener('paste', function (event) {
            if (event.target.matches('input:not([type=file]), textarea, [contenteditable=true]')) { return; }
            var files = Array.from(event.clipboardData ? event.clipboardData.items : []).filter(function (item) { return item.type.startsWith('image/'); }).map(function (item) { return item.getAsFile(); }).filter(Boolean);
            if (files.length) { event.preventDefault(); add(files); }
        });
        form.addEventListener('submit', async function (event) {
            event.preventDefault(); if (!items.length || processing || request || !configured) { return; }
            invalidate(); var version = generation, controller = new AbortController(), timedOut = false;
            request = controller; var snapshot = items.map(function (item) { return item.file; });
            var body = new FormData(); snapshot.forEach(function (file) { body.append('images[]', file, file.name); }); body.append('city_hint', hint.value.trim());
            state('identifying', 'Sending prepared photos and checking for likely places…');
            var timeout = setTimeout(function () { timedOut = true; controller.abort(); }, 45000);
            try {
                var payload = await api.jsonRequest(config.placeEndpoint, {method:'POST',body:body,signal:controller.signal});
                if (version !== generation) { return; }
                if (!payload || !['high','medium','low'].includes(payload.confidence)) { throw new Error('The service returned an unreadable result. Please try again.'); }
                request = null;
                if (payload.confidence === 'medium' && Array.isArray(payload.alternative_candidates) && payload.alternative_candidates.length) {
                    var chooser = el('section', 'stc-place-choices'); chooser.append(el('h3', '', 'Which place looks closest?'));
                    payload.alternative_candidates.slice(0,4).forEach(function (candidate) {
                        var button = el('button', 'stc-place-choice', (candidate.name_en || candidate.name_zh) + ' · ' + (candidate.city_en || 'City not confirmed')); button.type = 'button';
                        button.addEventListener('click', function () { if (version === generation) { api.renderPlaceCard(result, candidate, 'LIKELY MATCH'); state('success', 'Candidate selected. Check its details below.'); } }); chooser.append(button);
                    }); result.append(chooser); state('ambiguous', 'Several possible places found. Compare the candidates below.');
                } else if (payload.confidence === 'high' && payload.primary_candidate) { api.renderPlaceCard(result, payload.primary_candidate, 'LIKELY MATCH'); state('success', 'Likely place found. Arrival details are verified separately.'); }
                else { result.append(el('p', 'stc-place-low', 'No confident match. Add a visible sign, a wider view, or an optional city hint.')); state('unknown', 'No confident match. Your photos are still selected.'); }
            } catch (error) { if (version === generation) { request = null; state('error', timedOut ? 'This is taking too long. Your photos are ready to retry.' : error.name === 'AbortError' ? 'Request cancelled.' : error.message); } }
            finally { clearTimeout(timeout); if (version === generation) { request = null; submit.disabled = !items.length || !configured; cancel.hidden = true; root.classList.remove('is-loading'); } }
        });
        window.addEventListener('pagehide', function () { invalidate(); items.forEach(function (item) { URL.revokeObjectURL(item.url); }); items = []; renderPreviews(); state('idle', 'Choose your photos.'); });
        state('idle', configured ? 'Choose photos or paste an image here. City hint is optional.' : 'Photo identification is not configured yet. You can still browse guides or use Taxi Card.');
    }
    document.querySelectorAll('[data-stc-place-finder]').forEach(init);
})();
