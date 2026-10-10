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
			Wir verwenden keine Tracking- oder Marketing-Cookies. Nur für eingebettete Karten (Google Maps) benötigen wir Ihre Zustimmung, da dabei Daten an Google übertragen werden.
			<a href="<?php echo esc_url( (string) get_privacy_policy_url() ?: home_url( '/datenschutz/' ) ); ?>">Mehr erfahren</a>
		</p>
		<div class="consent__actions">
			<button type="button" class="btn btn--outline btn--sm" data-consent-choice="necessary">Nur notwendige</button>
			<button type="button" class="btn btn--outline btn--sm" data-consent-choice="all">Karten erlauben</button>
		</div>
	</div>
</div>
