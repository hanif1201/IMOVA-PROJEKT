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
		'company'         => 'Imova Projekt GmbH',
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
		'closing_costs'   => '5,5',
		'instagram'       => 'https://www.instagram.com/imovaprojekt/',
		'whatsapp'        => '+49 160 7946523',
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
 * Kaufnebenkosten mit Makler (Nebenkosten + Provision), gerundet, z. B. „9“.
 */
function imova_closing_costs_with_agent(): string {
	$sum = (float) str_replace( ',', '.', imova_opt( 'closing_costs' ) ) + (float) str_replace( ',', '.', imova_opt( 'commission_rate' ) );
	return number_format_i18n( round( $sum ), 0 );
}

/**
 * WhatsApp-Link (wa.me) mit vorausgefülltem Text. Leer, wenn keine Nummer hinterlegt ist.
 */
function imova_whatsapp_url( string $text = '' ): string {
	$digits = ltrim( preg_replace( '/\D/', '', imova_opt( 'whatsapp' ) ), '0' );
	if ( ! $digits ) {
		return '';
	}
	return 'https://wa.me/' . $digits . ( $text ? '?text=' . rawurlencode( $text ) : '' );
}

/**
 * Passender Einstiegstext für WhatsApp je nach aktueller Seite.
 */
function imova_whatsapp_context_text(): string {
	if ( is_singular( 'immobilie' ) ) {
		return sprintf( 'Hallo, ich interessiere mich für die Immobilie „%s“ (%s).', get_the_title(), get_permalink() );
	}
	if ( is_page_template( 'page-templates/verkaufen.php' ) ) {
		return 'Hallo, ich überlege, meine Immobilie zu verkaufen.';
	}
	if ( is_page_template( 'page-templates/tippgeber.php' ) ) {
		return 'Hallo, ich habe einen Tipp für eine Immobilie.';
	}
	return 'Hallo, ich habe eine Frage:';
}

/**
 * WhatsApp-Logo (gefüllt).
 */
function imova_whatsapp_icon( int $size = 20 ): string {
	return sprintf( '<svg class="icon icon-whatsapp" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>', $size );
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
		'instagram'=> '<rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r=".6" fill="currentColor"/>',
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
