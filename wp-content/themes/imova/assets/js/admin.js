/**
 * IMOVA – Backend: Medienauswahl für Galerie, Exposé und Bildfelder.
 */
(function ($) {
	'use strict';

	// Galerie
	$('[data-imova-gallery]').each(function () {
		const $wrap = $(this);
		const $input = $wrap.find('input[type="hidden"]');
		const $list = $wrap.find('.imova-gallery__list');
		let frame;

		const sync = () => $input.val($list.children().map((i, el) => $(el).data('id')).get().join(','));

		$list.sortable({ update: sync, tolerance: 'pointer' });
		$list.on('click', '.imova-gallery__remove', function () {
			$(this).closest('li').remove();
			sync();
		});

		$wrap.find('[data-imova-gallery-add]').on('click', function () {
			if (!frame) {
				frame = wp.media({ title: 'Bilder zur Galerie hinzufügen', button: { text: 'Hinzufügen' }, library: { type: 'image' }, multiple: 'add' });
				frame.on('select', function () {
					frame.state().get('selection').each(function (att) {
						const a = att.toJSON();
						if ($list.find('[data-id="' + a.id + '"]').length) return;
						const src = (a.sizes && a.sizes.thumbnail ? a.sizes.thumbnail.url : a.url);
						$list.append('<li data-id="' + a.id + '"><img src="' + src + '" alt=""><button type="button" class="imova-gallery__remove" aria-label="Bild entfernen">×</button></li>');
					});
					sync();
				});
			}
			frame.open();
		});
	});

	// Exposé (PDF)
	$('[data-imova-file]').each(function () {
		const $wrap = $(this);
		const $input = $wrap.find('input[type="hidden"]');
		const $name = $wrap.find('.imova-file-field__name');
		const $remove = $wrap.find('[data-imova-file-remove]');
		let frame;
		$wrap.find('[data-imova-file-select]').on('click', function () {
			if (!frame) {
				frame = wp.media({ title: 'Exposé wählen', button: { text: 'Übernehmen' }, library: { type: 'application/pdf' }, multiple: false });
				frame.on('select', function () {
					const a = frame.state().get('selection').first().toJSON();
					$input.val(a.id);
					$name.text(a.filename);
					$remove.prop('hidden', false);
				});
			}
			frame.open();
		});
		$remove.on('click', function () {
			$input.val('');
			$name.text('Kein Exposé hinterlegt');
			$remove.prop('hidden', true);
		});
	});

	// Einzelne Bildfelder (Seiteninhalte)
	$('[data-imova-image]').each(function () {
		const $wrap = $(this);
		const $input = $wrap.find('input[type="hidden"]');
		const $preview = $wrap.find('.imova-image-field__preview');
		const $remove = $wrap.find('[data-imova-image-remove]');
		let frame;
		$wrap.find('[data-imova-image-select]').on('click', function () {
			if (!frame) {
				frame = wp.media({ title: 'Bild wählen', button: { text: 'Übernehmen' }, library: { type: 'image' }, multiple: false });
				frame.on('select', function () {
					const a = frame.state().get('selection').first().toJSON();
					const src = a.sizes && a.sizes.medium ? a.sizes.medium.url : a.url;
					$input.val(a.id);
					$preview.html('<img src="' + src + '" alt="">');
					$remove.prop('hidden', false);
				});
			}
			frame.open();
		});
		$remove.on('click', function () {
			$input.val('');
			$preview.empty();
			$remove.prop('hidden', true);
		});
	});
})(jQuery);
