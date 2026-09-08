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

			function setCardVisibility(expanded, animate) {
				cards.forEach(function (card, index) {
					var shouldHide = media.matches && !expanded && index >= 4;

					card.hidden = shouldHide;
					card.classList.remove('is-revealing');

					if (animate && !shouldHide && index >= 4) {
						window.requestAnimationFrame(function () {
							card.classList.add('is-revealing');
						});
					}
				});
			}

			function syncResponsiveState() {
				if (!media.matches) {
					setCardVisibility(true, false);
					shell.classList.remove('is-ready', 'is-expanded');
					button.setAttribute('aria-expanded', 'false');
					button.hidden = true;
					return;
				}

				setCardVisibility(false, false);
				label.textContent = '+' + (cards.length - 4) + ' More ' + guideLabel;
				button.hidden = false;
				button.setAttribute('aria-expanded', 'false');
				shell.classList.remove('is-expanded');
				shell.classList.add('is-ready');
			}

			button.addEventListener('click', function () {
				var expanded = button.getAttribute('aria-expanded') === 'true';

				button.setAttribute('aria-expanded', expanded ? 'false' : 'true');
				shell.classList.toggle('is-expanded', !expanded);
				label.textContent = expanded ? '+' + (cards.length - 4) + ' More ' + guideLabel : 'Show fewer';
				setCardVisibility(!expanded, !expanded);
			});

			syncResponsiveState();
			if (media.addEventListener) {
				media.addEventListener('change', syncResponsiveState);
			} else {
				media.addListener(syncResponsiveState);
			}
		});
	}

	function stcPageShare() {
		var shareUtilities = document.querySelectorAll('[data-stc-share]');
		var finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');

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
			var copyLabelNode = utility.querySelector('[data-stc-share-copy-label]');
			var urlInput = utility.querySelector('[data-stc-share-url]');
			var status = utility.querySelector('[data-stc-share-status]');
			var whatsapp = utility.querySelector('[data-stc-share-whatsapp]');
			var email = utility.querySelector('[data-stc-share-email]');
			var title = utility.getAttribute('data-share-title') || document.title;
			var description = utility.getAttribute('data-share-description') || '';
			var canonicalUrl = utility.getAttribute('data-share-canonical') || window.location.href;
			var copyLabel = copyLabelNode ? copyLabelNode.textContent : 'Copy link';

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
				utility.classList.toggle('is-mobile-fallback', !finePointer.matches);
				announce(message || '');
				window.requestAnimationFrame(function () {
					(closeButton || whatsapp || email || copyButton).focus();
				});
			}

			function closePanel(restoreFocus) {
				panel.hidden = true;
				trigger.setAttribute('aria-expanded', 'false');
				utility.classList.remove('is-open', 'is-mobile-fallback');
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

				if (!finePointer.matches && navigator.share) {
					setBusy(true);
					announce('Opening sharing options');
					navigator.share(shareData).then(function () {
						announce('Page shared');
					}).catch(function () {
						openPanel('Choose another way to share');
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
						if (copyLabelNode) {
							copyLabelNode.textContent = 'Copied ✓';
						}
						announce('Copied');
						window.setTimeout(function () {
							if (copyLabelNode) {
								copyLabelNode.textContent = copyLabel;
							}
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
					return;
				}

				if (event.key === 'Tab' && !panel.hidden) {
					var focusable = Array.from(panel.querySelectorAll('a[href], button:not([disabled]), input:not([disabled])'));
					var first = focusable[0];
					var last = focusable[focusable.length - 1];

					if (event.shiftKey && document.activeElement === first) {
						event.preventDefault();
						last.focus();
					} else if (!event.shiftKey && document.activeElement === last) {
						event.preventDefault();
						first.focus();
					}
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
