/**
 * IMOVA Projekt – Frontend-Skripte (ohne Abhängigkeiten).
 */
(function () {
	'use strict';

	const $ = (sel, ctx = document) => ctx.querySelector(sel);
	const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
	const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	const storage = {
		get(key) { try { return window.localStorage.getItem(key); } catch (e) { return null; } },
		set(key, value) { try { window.localStorage.setItem(key, value); } catch (e) { /* privater Modus */ } },
	};

	/* Header-Schatten beim Scrollen -------------------------------------- */
	const header = $('[data-header]');
	if (header) {
		const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 8);
		onScroll();
		window.addEventListener('scroll', onScroll, { passive: true });
	}

	/* Mobile Navigation -------------------------------------------------- */
	const toggle = $('[data-nav-toggle]');
	const nav = $('[data-nav]');
	if (toggle && nav) {
		const setOpen = (open) => {
			toggle.setAttribute('aria-expanded', String(open));
			nav.classList.toggle('is-open', open);
			document.body.classList.toggle('nav-open', open);
		};
		toggle.addEventListener('click', () => setOpen(toggle.getAttribute('aria-expanded') !== 'true'));
		document.addEventListener('keydown', (e) => {
			if (e.key === 'Escape' && nav.classList.contains('is-open')) {
				setOpen(false);
				toggle.focus();
			}
		});
		nav.addEventListener('click', (e) => { if (e.target.closest('a')) setOpen(false); });
		window.matchMedia('(min-width: 1024px)').addEventListener('change', (e) => { if (e.matches) setOpen(false); });
	}

	/* Einblenden beim Scrollen ------------------------------------------- */
	const reveals = $$('[data-reveal]');
	if (reveals.length && 'IntersectionObserver' in window && !reduceMotion) {
		const io = new IntersectionObserver((entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					entry.target.classList.add('is-visible');
					io.unobserve(entry.target);
				}
			});
		}, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
		reveals.forEach((el) => io.observe(el));
	} else {
		reveals.forEach((el) => el.classList.add('is-visible'));
	}

	/* Filter: automatisch absenden --------------------------------------- */
	$$('[data-autosubmit]').forEach((select) => {
		select.addEventListener('change', () => {
			const form = select.form;
			// Leere Parameter nicht in die URL schreiben.
			$$('select', form).forEach((s) => { s.disabled = !s.value; });
			form.submit();
		});
	});
	// Beim Zurücknavigieren (bfcache) Filter wieder aktivieren.
	window.addEventListener('pageshow', () => $$('[data-autosubmit]').forEach((s) => { s.disabled = false; }));

	/* Provisionsrechner -------------------------------------------------- */
	const calc = $('[data-calc]');
	if (calc) {
		const input = $('[data-calc-input]', calc);
		const priceOut = $('[data-calc-price]', calc);
		const resultOut = $('[data-calc-result]', calc);
		const rate = parseFloat(calc.dataset.rate) || 0;
		const fmt = new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 });
		const update = () => {
			const price = Number(input.value);
			priceOut.textContent = fmt.format(price);
			resultOut.textContent = fmt.format(Math.round(price * rate / 100));
			const pct = ((price - input.min) / (input.max - input.min)) * 100;
			input.style.setProperty('--p', pct + '%');
		};
		input.addEventListener('input', update);
		update();
	}

	/* Einwilligung & Karten ---------------------------------------------- */
	const CONSENT_KEY = 'imova_consent_v1';
	const banner = $('[data-consent]');
	const consent = () => storage.get(CONSENT_KEY);

	const loadMap = (map) => {
		if (map.classList.contains('is-loaded')) return;
		const iframe = document.createElement('iframe');
		iframe.src = map.dataset.src;
		iframe.loading = 'lazy';
		iframe.referrerPolicy = 'no-referrer-when-downgrade';
		iframe.title = 'Karte';
		iframe.setAttribute('allowfullscreen', '');
		map.appendChild(iframe);
		map.classList.add('is-loaded');
	};
	const loadAllMaps = () => $$('[data-map]').forEach(loadMap);

	const setConsent = (value) => {
		storage.set(CONSENT_KEY, value);
		if (banner) banner.hidden = true;
		if (value === 'all') {
			loadAllMaps();
		} else {
			$$('[data-map].is-loaded').forEach((map) => {
				const iframe = $('iframe', map);
				if (iframe) iframe.remove();
				map.classList.remove('is-loaded');
			});
		}
	};

	if (banner) {
		if (!consent()) banner.hidden = false;
		$$('[data-consent-choice]', banner).forEach((btn) => {
			btn.addEventListener('click', () => setConsent(btn.dataset.consentChoice));
		});
	}
	$$('[data-consent-open]').forEach((btn) => btn.addEventListener('click', () => {
		if (!banner) return;
		banner.hidden = false;
		$('button', banner).focus();
	}));

	$$('[data-map]').forEach((map) => {
		const remember = $('[data-map-remember]', map);
		$('[data-map-load]', map).addEventListener('click', () => {
			if (remember && remember.checked) {
				setConsent('all');
			} else {
				loadMap(map);
			}
		});
	});
	if (consent() === 'all') loadAllMaps();

	/* Galerie-Lightbox --------------------------------------------------- */
	const gallery = $('[data-gallery]');
	const tpl = gallery && $('[data-gallery-items]', gallery);
	if (gallery && tpl) {
		const images = $$('img', tpl.content).map((img) => ({ src: img.getAttribute('src'), alt: img.getAttribute('alt') || '' }));
		let index = 0;
		let lastFocus = null;

		const box = document.createElement('div');
		box.className = 'lightbox';
		box.hidden = true;
		box.setAttribute('role', 'dialog');
		box.setAttribute('aria-modal', 'true');
		box.setAttribute('aria-label', 'Bildergalerie');
		box.innerHTML =
			'<div class="lightbox__bar"><span data-lb-count></span>' +
			'<button type="button" class="lightbox__btn" data-lb-close aria-label="Schließen"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 6l12 12M18 6 6 18"/></svg></button></div>' +
			'<div class="lightbox__stage"><img alt=""></div>' +
			'<div class="lightbox__nav">' +
			'<button type="button" class="lightbox__btn lightbox__prev" data-lb-prev aria-label="Vorheriges Bild"><svg class="icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>' +
			'<button type="button" class="lightbox__btn" data-lb-next aria-label="Nächstes Bild"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>' +
			'</div>';
		document.body.appendChild(box);

		const img = $('.lightbox__stage img', box);
		const count = $('[data-lb-count]', box);

		const show = (i) => {
			index = (i + images.length) % images.length;
			img.src = images[index].src;
			img.alt = images[index].alt;
			count.textContent = (index + 1) + ' / ' + images.length;
			// Nachbarbild vorladen
			new Image().src = images[(index + 1) % images.length].src;
		};
		const open = (i) => {
			lastFocus = document.activeElement;
			show(i);
			box.hidden = false;
			document.body.style.overflow = 'hidden';
			$('[data-lb-close]', box).focus();
		};
		const close = () => {
			box.hidden = true;
			document.body.style.overflow = '';
			if (lastFocus) lastFocus.focus();
		};

		$$('[data-gallery-open]', gallery).forEach((btn) => {
			btn.addEventListener('click', () => open(parseInt(btn.dataset.galleryOpen, 10) || 0));
		});
		$('[data-lb-close]', box).addEventListener('click', close);
		$('[data-lb-prev]', box).addEventListener('click', () => show(index - 1));
		$('[data-lb-next]', box).addEventListener('click', () => show(index + 1));
		box.addEventListener('click', (e) => { if (e.target === box || e.target.classList.contains('lightbox__stage')) close(); });
		document.addEventListener('keydown', (e) => {
			if (box.hidden) return;
			if (e.key === 'Escape') close();
			if (e.key === 'ArrowLeft') show(index - 1);
			if (e.key === 'ArrowRight') show(index + 1);
			if (e.key === 'Tab') {
				const focusables = $$('button', box);
				const first = focusables[0];
				const last = focusables[focusables.length - 1];
				if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
				else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
			}
		});
		let startX = null;
		box.addEventListener('touchstart', (e) => { startX = e.touches[0].clientX; }, { passive: true });
		box.addEventListener('touchend', (e) => {
			if (startX === null) return;
			const dx = e.changedTouches[0].clientX - startX;
			if (Math.abs(dx) > 50) show(index + (dx < 0 ? 1 : -1));
			startX = null;
		});
	}

	/* Formulare ---------------------------------------------------------- */
	$$('[data-imova-form]').forEach((form) => {
		// Abhängige Felder (z. B. Preisvorstellung nur bei entsprechender Auswahl)
		$$('[data-depends]', form).forEach((field) => {
			const inputs = $$('[name="' + field.dataset.depends + '"]', form);
			const update = () => {
				const checked = inputs.find((i) => (i.type === 'radio' ? i.checked : true));
				field.hidden = !checked || checked.value !== field.dataset.dependsValue;
			};
			inputs.forEach((i) => i.addEventListener('change', update));
			update();
		});

		// Dateiauswahl anzeigen + Drag & Drop
		$$('.file-drop', form).forEach((drop) => {
			const input = $('input[type="file"]', drop);
			const label = $('[data-file-label]', drop);
			const initial = label.textContent;
			const render = () => {
				const files = Array.from(input.files || []);
				label.textContent = files.length ? files.map((f) => f.name).join(', ') : initial;
			};
			input.addEventListener('change', render);
			['dragenter', 'dragover'].forEach((ev) => drop.addEventListener(ev, (e) => { e.preventDefault(); drop.classList.add('is-dragover'); }));
			['dragleave', 'drop'].forEach((ev) => drop.addEventListener(ev, () => drop.classList.remove('is-dragover')));
			drop.addEventListener('drop', (e) => {
				e.preventDefault();
				if (e.dataTransfer && e.dataTransfer.files.length) {
					input.files = e.dataTransfer.files;
					render();
				}
			});
		});

		// Clientseitige Prüfung mit verständlichen Hinweisen
		form.addEventListener('submit', (e) => {
			$$('.field.has-error', form).forEach((f) => {
				f.classList.remove('has-error');
				const err = $('.field__error[data-client]', f);
				if (err) err.remove();
			});
			let firstInvalid = null;
			$$('input, textarea, select', form).forEach((el) => {
				const field = el.closest('.field');
				if (!field || field.hidden || el.checkValidity()) return;
				if (field.classList.contains('has-error')) return;
				field.classList.add('has-error');
				const msg = document.createElement('p');
				msg.className = 'field__error';
				msg.dataset.client = '';
				msg.textContent = el.validity.typeMismatch ? 'Bitte prüfen Sie das Format.' : (el.type === 'checkbox' ? 'Bitte bestätigen Sie die Datenschutzhinweise.' : 'Bitte füllen Sie dieses Feld aus.');
				field.appendChild(msg);
				firstInvalid = firstInvalid || el;
			});
			if (firstInvalid) {
				e.preventDefault();
				firstInvalid.focus();
				return;
			}
			const btn = $('button[type="submit"]', form);
			if (btn) {
				btn.disabled = true;
				btn.style.opacity = '.6';
			}
		});
	});

	// Nach dem Absenden zur Rückmeldung springen
	const feedback = $('.form-success, .form-errors');
	if (feedback) {
		feedback.focus({ preventScroll: true });
		feedback.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'center' });
	}
})();
