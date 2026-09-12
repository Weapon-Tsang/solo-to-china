(function () {
	document.documentElement.classList.add('stc-js');

	function stcMobileNav() {
        var header = document.querySelector('.stc-header'), toggle = document.querySelector('.stc-menu-toggle'), nav = document.querySelector('.stc-nav');
        if (!header || !toggle || !nav) { return; }
        var media = matchMedia('(max-width: 840px)');
        function set(open, restore) {
            header.classList.toggle('is-menu-open', open); toggle.setAttribute('aria-expanded', String(open));
            var label = toggle.querySelector('.screen-reader-text'); if (label) { label.textContent = open ? 'Close menu' : 'Open menu'; }
            if (restore) { toggle.focus(); }
        }
        toggle.addEventListener('click', function () { set(toggle.getAttribute('aria-expanded') !== 'true', false); });
        document.addEventListener('keydown', function (event) { if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') { set(false, true); } });
        nav.addEventListener('click', function (event) { if (media.matches && event.target.closest('a')) { set(false, false); } });
        media.addEventListener('change', function () { if (!media.matches) { set(false, false); } });
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
			var expandedState = false;
			var cards = grid ? Array.from(grid.querySelectorAll('.stc-image-card')) : [];

			if (!grid || !button || !label || cards.length <= 4) {
				return;
			}

			function setCardVisibility(expanded, animate) {
				cards.forEach(function (card, index) {
					var shouldHide = media.matches && !expanded && index >= 4;

					if (shouldHide && card.contains(document.activeElement)) { button.focus(); }
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
					shell.classList.remove('is-expanded');
                    shell.classList.add('is-ready');
					button.setAttribute('aria-expanded', String(expandedState));
					button.hidden = true;
					return;
				}

				button.hidden = false;
                shell.classList.add('is-ready');
				setCardVisibility(expandedState, false);
				label.textContent = expandedState ? 'Show fewer' : '+' + (cards.length - 4) + ' More ' + guideLabel;
				button.hidden = false;
				button.setAttribute('aria-expanded', String(expandedState));
				shell.classList.toggle('is-expanded', expandedState);
				shell.classList.add('is-ready');
			}

			button.addEventListener('click', function () {
				var expanded = button.getAttribute('aria-expanded') === 'true';
				expandedState = !expanded;

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
		var closeOpen = function () {};
		document.addEventListener('keydown', function (event) { if (event.key === 'Escape') { closeOpen(true); } });
		document.addEventListener('click', function (event) { if (!event.target.closest('[data-stc-share]')) { closeOpen(false); } });
		var finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');

		function copyToClipboard(text) {
			if (navigator.clipboard && navigator.clipboard.writeText) {
				return Promise.resolve().then(function(){return navigator.clipboard.writeText(text);});
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
			var facebook = utility.querySelector('[data-stc-share-facebook]');
			var reddit = utility.querySelector('[data-stc-share-reddit]');
			var x = utility.querySelector('[data-stc-share-x]');

			var moreApps = utility.querySelector('[data-stc-share-more]');
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
			}
			if (facebook) { facebook.href = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(canonicalUrl); }
			if (reddit) { reddit.href = 'https://www.reddit.com/submit?url=' + encodeURIComponent(canonicalUrl) + '&title=' + encodeURIComponent(title); }
			if (x) { x.href = 'https://twitter.com/intent/tweet?url=' + encodeURIComponent(canonicalUrl) + '&text=' + encodeURIComponent(title); }
			if (moreApps && navigator.share) { moreApps.hidden = false; }

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

			function shareData() {
				return { title: title, text: description, url: canonicalUrl };
			}

			function shareNatively(fallback) {
				setBusy(true);
				announce('Opening sharing options');
				return Promise.resolve().then(function () { return navigator.share(shareData()); }).then(function () {
					announce('Page shared');
				}).catch(function (error) {
					if (error && error.name === 'AbortError') {announce('Sharing cancelled.');}
                    if (!error || error.name !== 'AbortError') {
						if (fallback) { fallback(); } else { announce('Could not open sharing options.'); }
					}
				}).finally(function () { setBusy(false); });
			}

			function openPanel(message) {
				closeOpen(false); closeOpen = closePanel;
				panel.hidden = false;
				trigger.setAttribute('aria-expanded', 'true');
				utility.classList.add('is-open');
				utility.classList.toggle('is-mobile-fallback', !finePointer.matches);
				announce(message || '');
				window.requestAnimationFrame(function () {
					(closeButton || whatsapp || facebook || reddit || x || copyButton).focus();
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
				if (!finePointer.matches && navigator.share) {
					shareNatively(function () { openPanel('Choose another way to share'); });
					return;
				}

				if (panel.hidden) {
					openPanel('');
				} else {
					closePanel(false);
				}
			});

			if (moreApps) {
				moreApps.addEventListener('click', function () { if (navigator.share) { shareNatively(); } });
			}


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
                            utility.classList.add('has-copy-fallback');
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


		});
	}
	function stcGuideToc() {
        var links = Array.from(document.querySelectorAll('[data-stc-guide-toc-list] a'));
        if (!('IntersectionObserver' in window) || !links.length) { return; }
        var ids = Array.from(new Set(links.map(function (link) { return decodeURIComponent(link.hash.slice(1)); })));
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) { if (entry.isIntersecting) { links.forEach(function (link) { if (decodeURIComponent(link.hash.slice(1)) === entry.target.id) { link.setAttribute('aria-current','location'); } else { link.removeAttribute('aria-current'); } }); } });
        }, {rootMargin:'-100px 0px -60% 0px'});
        ids.forEach(function (id) { var heading = document.getElementById(id); if (heading) { observer.observe(heading); } });
    }

	stcMobileNav();
	// Ordinary image links remain the no-JS fallback; native dialog handles modality.
	document.addEventListener('click', function (event) {
		var link = event.target.closest('[data-stc-enlarge], .stc-image-enlarge a');
		if (!link || !window.HTMLDialogElement || event.ctrlKey || event.metaKey) { return; }
		event.preventDefault();
		var dialog = document.createElement('dialog'); dialog.className = 'stc-image-dialog'; dialog.setAttribute('aria-label', 'Enlarged image');
		var close = document.createElement('button'); close.type = 'button'; close.textContent = 'Close image';
		var image = document.createElement('img'); image.src = link.href; image.alt = link.querySelector('img') ? link.querySelector('img').alt : '';
		dialog.append(close);
        var annotated = link.closest('.stc-annotated-media');
        if (annotated) {
            var enlarged = annotated.cloneNode(true), oldLink = enlarged.querySelector('a'); oldLink.replaceWith(image);
            dialog.append(enlarged);
            var notes = annotated.closest('section').querySelector('.stc-image-annotations'); if(notes){dialog.append(notes.cloneNode(true));}
        } else {dialog.append(image);}
        document.body.append(dialog);
		close.addEventListener('click', function () { dialog.close(); });
		dialog.addEventListener('close', function () { dialog.remove(); link.focus(); });
		dialog.showModal(); close.focus();
	});
	stcGuideGridReveal();
	stcPageShare();
	stcGuideToc();
})();
