/* LU3G Masonry Gallery */
(function () {
	'use strict';

	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function getColumns(grid) {
		var tpl = getComputedStyle(grid).gridTemplateColumns;
		return tpl && tpl !== 'none' ? tpl.split(' ').filter(Boolean).length : 1;
	}

	function getGap(grid) {
		var gap = parseFloat(getComputedStyle(grid).columnGap);
		return isNaN(gap) ? 0 : gap;
	}

	/* Calcola quante righe da 1px occupa ogni foto. Le proporzioni arrivano dal PHP,
	   quindi il layout è pronto subito senza aspettare il caricamento delle immagini. */
	function layout(grid) {
		var items = grid.querySelectorAll('.lmg__item');

		// Mosaico: il layout lo fa tutto il CSS.
		if (grid.classList.contains('lmg--mosaic')) {
			if (document.body.classList.contains('elementor-editor-active')) {
				flagLowRes(items);
			}
			return;
		}

		var cols = getColumns(grid);
		var gap = getGap(grid);
		var colWidth = (grid.clientWidth - gap * (cols - 1)) / cols;

		grid.classList.add('is-masonry');

		items.forEach(function (item) {
			var ratio = parseFloat(item.getAttribute('data-ratio')) || 1;
			var height = colWidth / ratio;
			item.style.gridRowEnd = 'span ' + Math.max(1, Math.round(height + gap));
		});

		if (document.body.classList.contains('elementor-editor-active')) {
			flagLowRes(items);
		}
	}

	/* Solo nell'editor: segnala foto con risoluzione troppo bassa per la cella. */
	function flagLowRes(items) {
		var dpr = Math.min(window.devicePixelRatio || 1, 2);
		items.forEach(function (item) {
			var needed = item.clientWidth * dpr * 0.8;
			var orig = parseInt(item.getAttribute('data-orig-w'), 10) || 0;
			var badge = item.querySelector('.lmg__badge');
			if (orig && orig < needed) {
				if (!badge) {
					badge = document.createElement('span');
					badge.className = 'lmg__badge';
					item.appendChild(badge);
				}
				badge.textContent = 'Risoluzione bassa (' + orig + 'px)';
			} else if (badge) {
				badge.remove();
			}
		});
	}

	function watchImages(grid) {
		grid.querySelectorAll('.lmg__item').forEach(function (item) {
			var img = item.querySelector('img');
			if (!img) {
				item.classList.add('is-loaded');
				return;
			}
			var done = function () {
				item.classList.add('is-loaded');
			};
			if (img.complete && img.naturalWidth) {
				done();
			} else {
				img.addEventListener('load', done, { once: true });
				img.addEventListener('error', done, { once: true });
			}
		});
	}

	function animate(grid) {
		var items = grid.querySelectorAll('.lmg__item');
		var noAnim = grid.classList.contains('lmg--anim-none');

		if (noAnim || reduceMotion || !('IntersectionObserver' in window)) {
			items.forEach(function (item) {
				item.classList.add('is-visible');
			});
			return;
		}

		var stagger = parseInt(grid.getAttribute('data-stagger'), 10) || 0;
		grid.classList.add('is-animated');

		var observer = new IntersectionObserver(
			function (entries) {
				var batch = entries
					.filter(function (e) {
						return e.isIntersecting;
					})
					.map(function (e) {
						return e.target;
					})
					.sort(function (a, b) {
						var ra = a.getBoundingClientRect();
						var rb = b.getBoundingClientRect();
						return ra.top - rb.top || ra.left - rb.left;
					});

				batch.forEach(function (item, i) {
					item.style.setProperty('--lmg-delay', i * stagger + 'ms');
					item.classList.add('is-visible');
					observer.unobserve(item);
				});
			},
			{ rootMargin: '0px 0px -8% 0px', threshold: 0.05 }
		);

		items.forEach(function (item) {
			observer.observe(item);
		});
	}

	function init(grid) {
		if (!grid || grid.__lmgReady) {
			return;
		}
		grid.__lmgReady = true;

		layout(grid);
		watchImages(grid);
		animate(grid);

		var frame;
		var relayout = function () {
			cancelAnimationFrame(frame);
			frame = requestAnimationFrame(function () {
				layout(grid);
			});
		};

		if ('ResizeObserver' in window) {
			new ResizeObserver(relayout).observe(grid);
		} else {
			window.addEventListener('resize', relayout);
		}
	}

	function initAll(scope) {
		(scope || document).querySelectorAll('.lmg').forEach(init);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			initAll();
		});
	} else {
		initAll();
	}

	/* Editor Elementor: il widget viene ri-renderizzato a ogni modifica. */
	window.addEventListener('elementor/frontend/init', function () {
		if (!window.elementorFrontend || !window.elementorFrontend.hooks) {
			return;
		}
		window.elementorFrontend.hooks.addAction('frontend/element_ready/lu3g_masonry_gallery.default', function ($scope) {
			initAll($scope && $scope[0] ? $scope[0] : document);
		});
	});
})();
