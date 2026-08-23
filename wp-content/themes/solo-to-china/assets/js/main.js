(function () {
	document.documentElement.classList.add('stc-js');

	function stcMobileNav() {
		var header = document.querySelector('.stc-header');
		var toggle = document.querySelector('.stc-menu-toggle');

		if (!header || !toggle) {
			return;
		}

		toggle.addEventListener('click', function () {
			var isOpen = header.classList.toggle('is-menu-open');
			toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
		});
	}

	function stcClampText(value, maxLength) {
		return String(value || '').trim().slice(0, maxLength);
	}

	function stcCollapsibleGuideGrid() {
		var grid = document.querySelector('[data-stc-collapsible-grid]');
		var toggle = document.querySelector('[data-stc-grid-toggle]');
		var label = toggle ? toggle.querySelector('[data-stc-grid-toggle-label]') : null;

		if (!grid || !toggle || !label) {
			return;
		}

		grid.classList.add('is-collapsible');
		toggle.hidden = false;

		toggle.addEventListener('click', function () {
			var isExpanded = grid.classList.toggle('is-expanded');
			toggle.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
			label.textContent = isExpanded ? 'Show fewer cities' : 'Show 4 more cities';
		});
	}

	function stcGuideType(value) {
		var guideType = stcClampText(value, 40);
		var allowedTypes = ['Survival Kit', 'City Guide', 'Attraction Guide'];

		return allowedTypes.indexOf(guideType) === -1 ? '' : guideType;
	}

	function stcSavedGuides() {
		var list = document.querySelector('[data-stc-saved-guides]');
		var buttons = document.querySelectorAll('[data-stc-save-guide]');
		var exportButton = document.querySelector('[data-stc-export-guides]');
		var importInput = document.querySelector('[data-stc-import-guides]');
		var clearButton = document.querySelector('[data-stc-clear-guides]');
		var storageKey = 'stcSavedGuides';

		if (!list && !buttons.length && !exportButton && !importInput && !clearButton) {
			return;
		}

		function readGuides() {
			try {
				return JSON.parse(window.localStorage.getItem(storageKey) || '[]');
			} catch (error) {
				return [];
			}
		}

		function writeGuides(guides) {
			window.localStorage.setItem(storageKey, JSON.stringify(guides));
		}

		function stcExportGuides() {
			var payload = {
				source: 'SoloToChina',
				exportedAt: new Date().toISOString(),
				guides: readGuides()
			};
			var url = window.URL.createObjectURL(new Blob([JSON.stringify(payload, null, 2)], { type: 'application/json;charset=utf-8' }));
			var link = document.createElement('a');

			link.href = url;
			link.download = 'solotochina-saved-guides.json';
			document.body.append(link);
			link.click();
			link.remove();
			window.URL.revokeObjectURL(url);
		}

		function normalizeImportedGuides(payload) {
			var importedGuides = Array.isArray(payload) ? payload : payload.guides;

			if (!Array.isArray(importedGuides)) {
				return [];
			}

			return importedGuides.filter(function (guide) {
				return guide && guide.id && stcGuideType(guide.type) && guide.title && guide.copy;
			}).map(function (guide) {
				return {
					id: stcClampText(guide.id, 96),
					type: stcGuideType(guide.type),
					title: stcClampText(guide.title, 120),
					copy: stcClampText(guide.copy, 220)
				};
			});
		}

		function stcImportGuides(file) {
			var reader = new FileReader();

			reader.addEventListener('load', function () {
				var importedGuides = [];

				try {
					importedGuides = normalizeImportedGuides(JSON.parse(String(reader.result || '{}')));
				} catch (error) {
					window.alert('This saved guides file could not be imported.');
					return;
				}

				if (!importedGuides.length) {
					window.alert('No saved guides were found in this file.');
					return;
				}

				var merged = importedGuides.concat(readGuides()).filter(function (guide, index, guides) {
					return guides.findIndex(function (candidate) {
						return candidate.id === guide.id;
					}) === index;
				});

				writeGuides(merged.slice(0, 24));
				renderGuides();
				updateButtons();
			});

			reader.readAsText(file);
		}

		function updateButtons() {
			var savedIds = readGuides().map(function (guide) {
				return guide.id;
			});

			buttons.forEach(function (button) {
				var isSaved = savedIds.indexOf(button.getAttribute('data-guide-id')) !== -1;

				if (button.classList.contains('stc-save-guide--image-card')) {
					button.classList.toggle('is-saved', isSaved);
					button.setAttribute('aria-label', isSaved ? 'Saved guide' : 'Save guide');
					return;
				}

				button.textContent = isSaved ? 'Saved' : 'Save';
			});
		}

		function renderGuides() {
			if (!list) {
				updateButtons();
				return;
			}

			var guides = readGuides();
			list.replaceChildren();

			if (!guides.length) {
				var empty = document.createElement('p');
				empty.textContent = 'No saved guides on this device yet.';
				list.append(empty);
				return;
			}

			guides.forEach(function (guide) {
				var item = document.createElement('article');
				item.className = 'stc-saved-guide-item';

				var copy = document.createElement('div');
				var type = document.createElement('span');
				type.textContent = guide.type;

				var title = document.createElement('strong');
				title.textContent = guide.title;

				var summary = document.createElement('p');
				summary.textContent = guide.copy;

				var button = document.createElement('button');
				button.type = 'button';
				button.setAttribute('data-stc-delete-guide', guide.id);
				button.textContent = 'Delete';

				copy.append(type, title, summary);
				item.append(copy, button);
				list.append(item);
			});
		}

		buttons.forEach(function (button) {
			button.addEventListener('click', function (event) {
				event.preventDefault();
				event.stopPropagation();

				var guide = {
					id: button.getAttribute('data-guide-id'),
					type: button.getAttribute('data-guide-type'),
					title: button.getAttribute('data-guide-title'),
					copy: button.getAttribute('data-guide-copy')
				};
				var guides = readGuides().filter(function (savedGuide) {
					return savedGuide.id !== guide.id;
				});

				guides.unshift(guide);
				writeGuides(guides.slice(0, 24));
				renderGuides();
				updateButtons();
			});
		});

		if (exportButton) {
			exportButton.addEventListener('click', stcExportGuides);
		}

		if (importInput) {
			importInput.addEventListener('change', function () {
				if (importInput.files && importInput.files[0]) {
					stcImportGuides(importInput.files[0]);
				}

				importInput.value = '';
			});
		}

		if (clearButton) {
			clearButton.addEventListener('click', function () {
				if (!readGuides().length || !window.confirm('Clear all saved guides on this device?')) {
					return;
				}

				writeGuides([]);
				renderGuides();
				updateButtons();
			});
		}

		if (list) {
			list.addEventListener('click', function (event) {
				var button = event.target.closest('[data-stc-delete-guide]');

				if (!button) {
					return;
				}

				writeGuides(readGuides().filter(function (guide) {
					return guide.id !== button.getAttribute('data-stc-delete-guide');
				}));
				renderGuides();
				updateButtons();
			});
		}

		renderGuides();
		updateButtons();
	}

	function stcPageShare() {
		var buttons = document.querySelectorAll('[data-stc-share-page]');

		function copyToClipboard(text) {
			if (navigator.clipboard && navigator.clipboard.writeText) {
				return navigator.clipboard.writeText(text);
			}

			var input = document.createElement('textarea');
			input.value = text;
			input.setAttribute('readonly', '');
			input.style.position = 'fixed';
			input.style.top = '-999px';
			document.body.append(input);
			input.select();
			document.execCommand('copy');
			input.remove();
			return Promise.resolve();
		}

		function markCopied(button) {
			var originalText = button.textContent;
			button.textContent = 'Link copied';

			window.setTimeout(function () {
				button.textContent = originalText;
			}, 1800);
		}

		buttons.forEach(function (button) {
			button.addEventListener('click', function () {
				var shareData = {
					title: button.getAttribute('data-share-title') || document.title,
					url: button.getAttribute('data-share-url') || window.location.href
				};

				if (navigator.share) {
					navigator.share(shareData).catch(function () {});
					return;
				}

				copyToClipboard(shareData.url).then(function () {
					markCopied(button);
				});
			});
		});
	}

	function stcTocSlug(text, index) {
		var slug = String(text || '')
			.toLowerCase()
			.replace(/[^a-z0-9]+/g, '-')
			.replace(/^-|-$/g, '');

		return slug || 'section-' + (index + 1);
	}

	function stcGuideToc() {
		var tocs = document.querySelectorAll('[data-stc-guide-toc]');
		var content = document.querySelector('.stc-entry-content--guide');

		if (!tocs.length || !content) {
			return;
		}

		var headings = Array.prototype.slice.call(content.querySelectorAll('h2')).filter(function (heading) {
			return stcClampText(heading.textContent, 80);
		});

		if (!headings.length) {
			tocs.forEach(function (toc) {
				toc.hidden = true;
			});
			return;
		}

		var usedIds = {};
		var tocItems = headings.map(function (heading, index) {
			var baseId = heading.id || stcTocSlug(heading.textContent, index);
			var id = baseId;
			var count = 2;
			var existing = document.getElementById(id);

			while (usedIds[id] || (existing && existing !== heading)) {
				id = baseId + '-' + count;
				count += 1;
				existing = document.getElementById(id);
			}

			usedIds[id] = true;
			heading.id = id;

			return {
				id: id,
				text: stcClampText(heading.textContent, 80)
			};
		});

		tocs.forEach(function (toc) {
			var list = toc.querySelector('[data-stc-guide-toc-list]');
			var fragment = document.createDocumentFragment();

			if (!list) {
				return;
			}

			tocItems.forEach(function (tocItem) {
				var item = document.createElement('li');
				var link = document.createElement('a');
				link.href = '#' + tocItem.id;
				link.textContent = tocItem.text;
				item.append(link);
				fragment.append(item);
			});

			list.replaceChildren(fragment);
		});
	}

	stcMobileNav();
	stcCollapsibleGuideGrid();
	stcSavedGuides();
	stcPageShare();
	stcGuideToc();
})();
