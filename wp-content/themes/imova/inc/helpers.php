<?php
/**
 * Kleine Hilfsfunktionen, die überall im Theme gebraucht werden.
 *
 * @package imova
 */

defined( 'ABSPATH' ) || exit;

/**
 * Standardwerte der globalen Einstellungen (Einstellungen → IMOVA).
 */
function imova_default_settings(): array {
	return array(
		'company'         => 'IMOVA PROJEKT GmbH',
		'director'        => 'Sebastian Fäth',
		'street'          => 'Prinzregentenstraße 157',
		'zip_city'        => '81677 München',
		'phone'           => '+49 160 7946523',
		'email'           => 'sebastian@imova-projekt.de',
		'notify_email'    => 'sebastian@imova-projekt.de',
		'register_court'  => 'Amtsgericht München',
		'register_number' => 'HRB 255675',
		'vat_id'          => '',
		'commission_rate' => '3,57',
		'referral_reward' => 'Attraktive Provision bei erfolgreichem Ankauf',
		'logo_dark'       => '',
		'logo_light'      => '',
	);
}

/**
 * Liest eine globale Einstellung.
 */
function imova_opt( string $key ): string {
	static $settings = null;
	if ( null === $settings ) {
		$settings = wp_parse_args( (array) get_option( 'imova_settings', array() ), imova_default_settings() );
	}
	return (string) ( $settings[ $key ] ?? '' );
}

/**
 * Telefonnummer als tel:-Link-Ziel.
 */
function imova_tel_href( string $phone ): string {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', $phone );
}

/**
 * Formatiert einen Betrag im deutschen Format, z. B. „349.000 €“.
 */
function imova_format_price( $amount ): string {
	if ( '' === $amount || null === $amount || ! is_numeric( $amount ) ) {
		return '';
	}
	return number_format_i18n( (float) $amount, 0 ) . ' €';
}

/**
 * Formatiert eine Zahl mit deutschem Dezimaltrennzeichen (ohne überflüssige Nachkommastellen).
 */
function imova_format_number( $value ): string {
	if ( '' === $value || ! is_numeric( $value ) ) {
		return '';
	}
	$value = (float) $value;
	return number_format_i18n( $value, floor( $value ) == $value ? 0 : 1 );
}

/**
 * URL eines mitgelieferten Platzhalterbilds.
 */
function imova_demo_img( string $name ): string {
	return IMOVA_URI . '/assets/img/demo/' . $name . '.jpg';
}

/**
 * Bild-URL aus einer Anhang-ID – mit Platzhalter als Rückfall.
 */
function imova_img_url( $attachment_id, string $size = 'large', string $fallback = '' ): string {
	if ( $attachment_id ) {
		$src = wp_get_attachment_image_url( (int) $attachment_id, $size );
		if ( $src ) {
			return $src;
		}
	}
	return $fallback ? imova_demo_img( $fallback ) : '';
}

/**
 * Inline-SVG-Icons (Linienstil, erben currentColor).
 */
function imova_icon( string $name, int $size = 20 ): string {
	$paths = array(
		'arrow'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'phone'    => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/>',
		'mail'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
		'pin'      => '<path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
		'check'    => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
		'plus'     => '<path d="M12 5v14M5 12h14"/>',
		'close'    => '<path d="M6 6l12 12M18 6 6 18"/>',
		'menu'     => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'search'   => '<circle cx="11" cy="11" r="6.5"/><path d="m20 20-4.2-4.2"/>',
		'chevron'  => '<path d="m6 9 6 6 6-6"/>',
		'shield'   => '<path d="M12 3 5 6v6c0 4.5 3 7.6 7 9 4-1.4 7-4.5 7-9V6z"/><path d="m9 12 2 2 4-4"/>',
		'leaf'     => '<path d="M5 19c0-8 5-13 14-14-1 9-6 14-14 14z"/><path d="M5 19 13 11"/>',
		'layers'   => '<path d="m12 3 9 5-9 5-9-5z"/><path d="m3 13 9 5 9-5"/>',
		'hammer'   => '<path d="M14 6 9 11l4 4 5-5"/><path d="m11 13-7 7"/><path d="M13 4l5 5 2-2-5-5z"/>',
		'clock'    => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
		'percent'  => '<path d="M19 5 5 19"/><circle cx="7" cy="7" r="2.5"/><circle cx="17" cy="17" r="2.5"/>',
		'handshake'=> '<path d="m3 12 4-4 4 2 3-3 4 1 3 4"/><path d="m7 14 3 3a1.5 1.5 0 0 0 2-2l2 2a1.5 1.5 0 0 0 2-2l1-1"/>',
		'file'     => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h4"/>',
		'home'     => '<path d="M4 11 12 4l8 7"/><path d="M6 10v10h12V10"/><path d="M10 20v-5h4v5"/>',
		'user'     => '<circle cx="12" cy="8" r="4"/><path d="M4 21c1-4 4.5-6 8-6s7 2 8 6"/>',
		'upload'   => '<path d="M12 16V4M7 9l5-5 5 5"/><path d="M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3"/>',
		'expand'   => '<path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"/>',
		'lock'     => '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return sprintf(
		'<svg class="icon icon-%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%3$s</svg>',
		esc_attr( $name ),
		$size,
		$paths[ $name ]
	);
}

/**
 * Wandelt einfachen Text mit Leerzeilen in Absätze um (für Felder aus „Seiteninhalte“).
 */
function imova_text( string $text ): string {
	return wpautop( wp_kses_post( $text ) );
}

/**
 * Seiten-URL über das hinterlegte Seitentemplate finden (z. B. „page-templates/tippgeber.php“).
 */
function imova_page_url( string $template, string $fallback_slug = '' ): string {
	static $cache = array();
	if ( isset( $cache[ $template ] ) ) {
		return $cache[ $template ];
	}
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => $template,
			'fields'         => 'ids',
		)
	);
	$url = $pages ? get_permalink( $pages[0] ) : home_url( '/' . $fallback_slug . '/' );
	return $cache[ $template ] = $url;
}
