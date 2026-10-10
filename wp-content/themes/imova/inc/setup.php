<?php
/**
 * Theme-Grundeinrichtung: Supports, Menüs, Bildgrößen, Assets.
 *
 * @package imova
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'imova', IMOVA_DIR . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
		add_theme_support( 'responsive-embeds' );
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 80,
				'width'       => 260,
				'flex-height' => true,
				'flex-width'  => true,
			)
		);

		register_nav_menus(
			array(
				'primary' => 'Hauptnavigation',
				'footer'  => 'Footer-Navigation',
				'legal'   => 'Rechtliches (Footer)',
			)
		);

		add_image_size( 'imova-card', 960, 680, true );
		add_image_size( 'imova-hero', 2200, 1300, false );
		add_image_size( 'imova-thumb', 320, 220, true );
		add_image_size( 'imova-portrait', 900, 1100, true );
	}
);

/**
 * Styles & Scripts. Schriften werden lokal ausgeliefert (keine Google-Fonts-Verbindung).
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		$css = IMOVA_DIR . '/assets/css/main.css';
		$js  = IMOVA_DIR . '/assets/js/main.js';

		wp_enqueue_style( 'imova', IMOVA_URI . '/assets/css/main.css', array(), IMOVA_VERSION . '.' . filemtime( $css ) );
		wp_enqueue_script( 'imova', IMOVA_URI . '/assets/js/main.js', array(), IMOVA_VERSION . '.' . filemtime( $js ), array( 'strategy' => 'defer', 'in_footer' => true ) );

		// Blockbibliothek-Styles werden nur für Inhaltsbereiche gebraucht; die globalen Styles nicht.
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'classic-theme-styles' );
	},
	20
);

add_action(
	'wp_head',
	function () {
		foreach ( array( 'cormorant-garamond-latin-500-normal', 'inter-latin-400-normal' ) as $font ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( IMOVA_URI . '/assets/fonts/' . $font . '.woff2' )
			);
		}
		echo '<meta name="theme-color" content="#ffffff">' . "\n";
		echo "<script>document.documentElement.classList.add('js');</script>\n";
	},
	1
);

// Emoji-Skripte laden externe Ressourcen – nicht nötig.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
add_filter( 'emoji_svg_url', '__return_false' );

add_filter( 'document_title_separator', fn() => '–' );
add_filter( 'excerpt_length', fn() => 24 );
add_filter( 'excerpt_more', fn() => ' …' );

/**
 * Body-Klassen: Seiten mit Vollbild-Hero bekommen einen transparenten Header.
 */
add_filter(
	'body_class',
	function ( array $classes ) {
		if ( is_front_page() ) {
			$classes[] = 'has-hero';
		}
		return $classes;
	}
);

/**
 * Fallback für die Hauptnavigation, solange kein Menü zugewiesen ist.
 */
function imova_fallback_menu(): void {
	$items = array(
		'Immobilien' => get_post_type_archive_link( 'immobilie' ),
		'Verkaufen'  => imova_page_url( 'page-templates/verkaufen.php', 'verkaufen' ),
		'Tippgeber'  => imova_page_url( 'page-templates/tippgeber.php', 'tippgeber' ),
		'Über uns'   => imova_page_url( 'page-templates/ueber-uns.php', 'ueber-uns' ),
		'Kontakt'    => imova_page_url( 'page-templates/kontakt.php', 'kontakt' ),
	);
	echo '<ul class="nav-list">';
	foreach ( $items as $label => $url ) {
		printf( '<li class="menu-item"><a href="%s">%s</a></li>', esc_url( (string) $url ), esc_html( $label ) );
	}
	echo '</ul>';
}

/**
 * URL des hochgeladenen Logos (Einstellungen → IMOVA). „dark“ fällt auf das Customizer-Logo zurück.
 */
function imova_logo_url( string $variant = 'dark' ): string {
	$id = (int) imova_opt( 'light' === $variant ? 'logo_light' : 'logo_dark' );
	if ( ! $id && 'dark' === $variant ) {
		$id = (int) get_theme_mod( 'custom_logo' );
	}
	return $id ? (string) wp_get_attachment_image_url( $id, 'full' ) : '';
}

/**
 * Markenzeichen auf dunklen Platzhalterflächen: helles Logo oder Schriftzug.
 */
function imova_mark( string $class = 'placeholder-media__mark' ): string {
	$logo = imova_logo_url( 'light' );
	if ( $logo ) {
		return sprintf( '<img class="%s %s--logo" src="%s" alt="">', esc_attr( $class ), esc_attr( $class ), esc_url( $logo ) );
	}
	return sprintf( '<span class="%s">IMOVA</span>', esc_attr( $class ) );
}

/**
 * Logo: hochgeladenes Logo (Design → Customizer → Website-Informationen) oder Wortmarke.
 */
function imova_logo( string $variant = 'dark' ): void {
	$logo = imova_logo_url( $variant );
	if ( $logo ) {
		printf(
			'<a class="site-logo site-logo--%s" href="%s" rel="home"><img src="%s" alt="%s"></a>',
			esc_attr( $variant ),
			esc_url( home_url( '/' ) ),
			esc_url( $logo ),
			esc_attr( imova_opt( 'company' ) )
		);
		return;
	}
	printf(
		'<a class="wordmark wordmark--%s" href="%s" rel="home" aria-label="%s – Startseite"><span class="wordmark__main">IMOVA</span><span class="wordmark__sub">Projekt</span></a>',
		esc_attr( $variant ),
		esc_url( home_url( '/' ) ),
		esc_attr( imova_opt( 'company' ) )
	);
}
