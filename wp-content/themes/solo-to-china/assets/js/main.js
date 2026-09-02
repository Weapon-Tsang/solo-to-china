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

	function stcGuideGridReveal() {
		var media = window.matchMedia('(max-width: 840px)');
		var shells = document.querySelectorAll('[data-stc-guide-grid-shell]');

		shells.forEach(function (shell) {
			var grid = shell.querySelector('[data-stc-guide-grid]');
			var button = shell.querySelector('[data-stc-guide-reveal]');
			var label = shell.querySelector('[data-stc-guide-reveal-label]');
			var guideLabel = shell.getAttribute('data-stc-guide-label') || 'Guides';
			var cards = grid ? Array.from(grid.querySelectorAll('.stc-image-card')) : [];

			if (!grid || !button || !label || cards.length <= 4) {
				return;
			}

			function syncCollapsedHeight() {
				if (!media.matches) {
					shell.classList.remove('is-ready');
					grid.style.removeProperty('--stc-guide-collapsed-height');
					grid.style.removeProperty('--stc-guide-expanded-height');
					button.hidden = true;
					return;
				}

				var firstCard = cards[0].getBoundingClientRect();
				var fourthCard = cards[3].getBoundingClientRect();
				var lastCard = cards[cards.length - 1].getBoundingClientRect();
				var collapsedHeight = Math.ceil(fourthCard.bottom - firstCard.top);
				var expandedHeight = Math.ceil(lastCard.bottom - firstCard.top);

				grid.style.setProperty('--stc-guide-collapsed-height', collapsedHeight + 'px');
				grid.style.setProperty('--stc-guide-expanded-height', expandedHeight + 'px');
				label.textContent = '+' + (cards.length - 4) + ' More ' + guideLabel;
				button.hidden = false;
				shell.classList.add('is-ready');
			}

			button.addEventListener('click', function () {
				var firstRevealedLink = cards[4].querySelector('a');

				shell.classList.add('is-expanded');
				button.setAttribute('aria-expanded', 'true');

				if (firstRevealedLink) {
					firstRevealedLink.focus({ preventScroll: true });
				}
			});

			syncCollapsedHeight();
			window.addEventListener('resize', syncCollapsedHeight);
		});
	}

	function stcPageShare() {
		var shareUtilities = document.querySelectorAll('[data-stc-share]');

		function copyToClipboard(text) {
			if (navigator.clipboard && navigator.clipboard.writeText) {
				return navigator.clipboard.writeText(text);
			}

			return new Promise(function (resolve, reject) {
				var input = document.createElement('textarea');

				input.className = 'stc-share__clipboard-proxy';
				input.value = text;
				input.setAttribute('readonly', '');
				document.body.append(input);
				input.select();

				try {
					if (!document.execCommand('copy')) {
						throw new Error('Copy command was rejected.');
					}
					resolve();
				} catch (error) {
					reject(error);
				} finally {
					input.remove();
				}
			});
		}

		shareUtilities.forEach(function (utility) {
			var trigger = utility.querySelector('[data-stc-share-trigger]');
			var panel = utility.querySelector('[data-stc-share-panel]');
			var closeButton = utility.querySelector('[data-stc-share-close]');
			var copyButton = utility.querySelector('[data-stc-share-copy]');
			var urlInput = utility.querySelector('[data-stc-share-url]');
			var status = utility.querySelector('[data-stc-share-status]');
			var whatsapp = utility.querySelector('[data-stc-share-whatsapp]');
			var email = utility.querySelector('[data-stc-share-email]');
			var title = utility.getAttribute('data-share-title') || document.title;
			var description = utility.getAttribute('data-share-description') || '';
			var canonicalUrl = utility.getAttribute('data-share-canonical') || window.location.href;
			var copyLabel = copyButton ? copyButton.textContent : 'Copy link';

			if (!trigger || !panel) {
				return;
			}

			if (urlInput) {
				urlInput.value = canonicalUrl;
			}
			if (whatsapp) {
				whatsapp.href = 'https://wa.me/?text=' + encodeURIComponent(title + ' — ' + canonicalUrl);
				whatsapp.target = '_blank';
				whatsapp.rel = 'noopener';
			}
			if (email) {
				email.href = 'mailto:?subject=' + encodeURIComponent(title) + '&body=' + encodeURIComponent((description ? description + '\n\n' : '') + canonicalUrl);
			}

			function announce(message) {
				if (status) {
					status.textContent = message;
				}
			}

			function setBusy(isBusy) {
				trigger.disabled = isBusy;
				trigger.setAttribute('aria-busy', isBusy ? 'true' : 'false');
				utility.classList.toggle('is-sharing', isBusy);
			}

			function openPanel(message) {
				panel.hidden = false;
				trigger.setAttribute('aria-expanded', 'true');
				utility.classList.add('is-open');
				announce(message || '');
				window.requestAnimationFrame(function () {
					(closeButton || copyButton || urlInput).focus();
				});
			}

			function closePanel(restoreFocus) {
				panel.hidden = true;
				trigger.setAttribute('aria-expanded', 'false');
				utility.classList.remove('is-open');
				announce('');
				if (restoreFocus) {
					trigger.focus();
				}
			}

			trigger.addEventListener('click', function () {
				var shareData = {
					title: title,
					text: description,
					url: canonicalUrl
				};

				if (navigator.share) {
					setBusy(true);
					announce('Opening sharing options');
					navigator.share(shareData).then(function () {
						announce('Page shared');
					}).catch(function (error) {
						if (!error || error.name !== 'AbortError') {
							openPanel('Choose another way to share');
						}
					}).finally(function () {
						setBusy(false);
					});
					return;
				}

				if (panel.hidden) {
					openPanel('');
				} else {
					closePanel(false);
				}
			});

			if (copyButton) {
				copyButton.addEventListener('click', function () {
					copyButton.disabled = true;
					copyToClipboard(canonicalUrl).then(function () {
						copyButton.textContent = 'Link copied';
						announce('Link copied');
						window.setTimeout(function () {
							copyButton.textContent = copyLabel;
							copyButton.disabled = false;
						}, 1800);
					}).catch(function () {
						copyButton.disabled = false;
						announce('Copy failed. Select the link and copy it manually.');
						if (urlInput) {
							urlInput.focus();
							urlInput.select();
						}
					});
				});
			}

			if (closeButton) {
				closeButton.addEventListener('click', function () {
					closePanel(true);
				});
			}

			document.addEventListener('keydown', function (event) {
				if (event.key === 'Escape' && !panel.hidden) {
					event.preventDefault();
					closePanel(true);
				}
			});

			document.addEventListener('click', function (event) {
				if (!panel.hidden && !utility.contains(event.target)) {
					closePanel(false);
				}
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
	stcGuideGridReveal();
	stcPageShare();
	stcGuideToc();
})();
