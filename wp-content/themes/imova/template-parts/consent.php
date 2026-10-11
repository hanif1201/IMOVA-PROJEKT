<?php
/**
 * Hinweis zu externen Medien (Einwilligung nach § 25 TDDDG). Beide Optionen gleichwertig.
 *
 * @package imova
 */

?>
<div class="consent" role="dialog" aria-modal="false" aria-labelledby="consent-title" hidden data-consent>
	<div class="consent__inner">
		<p class="consent__title" id="consent-title">Datenschutz-Einstellungen</p>
		<p class="consent__text">
			Wir verwenden keine Tracking- oder Marketing-Cookies. Mit „Alle akzeptieren“ erlauben Sie zusätzlich eingebettete Karten (Google Maps), wobei Daten an Google übertragen werden. Mit „Nur notwendige“ lehnen Sie das ab – die Website funktioniert trotzdem vollständig.
			<a href="<?php echo esc_url( (string) get_privacy_policy_url() ?: home_url( '/datenschutz/' ) ); ?>">Mehr erfahren</a>
		</p>
		<div class="consent__actions">
			<button type="button" class="btn btn--outline btn--sm" data-consent-choice="necessary">Nur notwendige</button>
			<button type="button" class="btn btn--outline btn--sm" data-consent-choice="all">Alle akzeptieren</button>
		</div>
	</div>
</div>
