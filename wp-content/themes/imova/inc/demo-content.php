<?php
/**
 * Ersteinrichtung: Seiten, Menüs, erstes Objekt (Augsburg) und Beispielobjekte.
 * Läuft einmalig beim Aktivieren des Themes (oder per `wp imova setup`).
 *
 * @package imova
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_switch_theme', 'imova_run_setup' );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command(
		'imova setup',
		function ( $args, $assoc ) {
			imova_run_setup( ! empty( $assoc['force'] ) );
			WP_CLI::success( 'IMOVA-Inhalte eingerichtet.' );
		}
	);
}

function imova_run_setup( bool $force = false ): void {
	if ( get_option( 'imova_setup_done' ) && ! $force ) {
		return;
	}

	$pages = array(
		'startseite' => array( 'Startseite', '', '' ),
		'verkaufen'  => array( 'Verkaufen', 'page-templates/verkaufen.php', '' ),
		'tippgeber'  => array( 'Tippgeber', 'page-templates/tippgeber.php', '' ),
		'ueber-uns'  => array( 'Über uns', 'page-templates/ueber-uns.php', imova_default_about_story() ),
		'kontakt'    => array( 'Kontakt', 'page-templates/kontakt.php', '' ),
		'impressum'  => array( 'Impressum', '', imova_default_imprint() ),
		'datenschutz' => array( 'Datenschutzerklärung', '', imova_default_privacy() ),
	);

	$ids = array();
	foreach ( $pages as $slug => [ $title, $template, $content ] ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			$ids[ $slug ] = $existing->ID;
			continue;
		}
		$ids[ $slug ] = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_content' => $content,
			)
		);
		if ( $template ) {
			update_post_meta( $ids[ $slug ], '_wp_page_template', $template );
		}
	}

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $ids['startseite'] );
	update_option( 'wp_page_for_privacy_policy', $ids['datenschutz'] );

	imova_setup_menus( $ids );
	imova_setup_properties();

	update_option( 'imova_setup_done', IMOVA_VERSION );
	flush_rewrite_rules();
}

function imova_setup_menus( array $ids ): void {
	$menus = array(
		'primary' => array( 'Hauptnavigation', array( 'immobilien', 'verkaufen', 'tippgeber', 'ueber-uns', 'kontakt' ) ),
		'footer'  => array( 'Footer', array( 'immobilien', 'verkaufen', 'tippgeber', 'ueber-uns', 'kontakt' ) ),
		'legal'   => array( 'Rechtliches', array( 'impressum', 'datenschutz' ) ),
	);
	$locations = (array) get_theme_mod( 'nav_menu_locations', array() );

	foreach ( $menus as $location => [ $name, $items ] ) {
		if ( ! empty( $locations[ $location ] ) && wp_get_nav_menu_object( $locations[ $location ] ) ) {
			continue;
		}
		$menu = wp_get_nav_menu_object( $name );
		$menu_id = $menu ? $menu->term_id : wp_create_nav_menu( $name );
		if ( is_wp_error( $menu_id ) ) {
			continue;
		}
		if ( ! $menu || ! wp_get_nav_menu_items( $menu_id ) ) {
			foreach ( $items as $i => $slug ) {
				$item = array(
					'menu-item-status'   => 'publish',
					'menu-item-position' => $i + 1,
				);
				if ( 'immobilien' === $slug ) {
					$item += array(
						'menu-item-title'  => 'Immobilien',
						'menu-item-type'   => 'post_type_archive',
						'menu-item-object' => 'immobilie',
					);
				} else {
					$item += array(
						'menu-item-object-id' => $ids[ $slug ],
						'menu-item-object'    => 'page',
						'menu-item-type'      => 'post_type',
						'menu-item-title'     => 'datenschutz' === $slug ? 'Datenschutz' : get_the_title( $ids[ $slug ] ),
					);
				}
				wp_update_nav_menu_item( $menu_id, 0, $item );
			}
		}
		$locations[ $location ] = $menu_id;
	}
	set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * Legt das erste echte Objekt (Augsburg, „Demnächst“) und drei markierte Beispielobjekte an.
 */
function imova_setup_properties(): void {
	$terms = array();
	foreach ( array( 'München', 'Augsburg' ) as $name ) {
		$term           = term_exists( $name, 'standort' ) ?: wp_insert_term( $name, 'standort' );
		$terms[ $name ] = is_array( $term ) ? (int) $term['term_id'] : 0;
	}

	$properties = array(
		array(
			'title'   => '2-Zimmer-Wohnung in Augsburg',
			'excerpt' => 'Unser nächstes Projekt: eine durchdacht modernisierte 2-Zimmer-Wohnung in Augsburg. Bilder und Details folgen in Kürze.',
			'content' => "<p>Diese Wohnung befindet sich gerade in Vorbereitung. Wie bei allen unseren Projekten erhalten wir, was gut ist, und modernisieren mit langlebigen Materialien – für zeitgemäße Wohnqualität mit Charakter.</p>\n<p>Fotos, Grundriss und alle Eckdaten veröffentlichen wir hier in Kürze. Tragen Sie sich gern unverbindlich ein – Sie erfahren als Erste(r), wenn es losgeht.</p>",
			'term'    => 'Augsburg',
			'demo'    => false,
			'meta'    => array(
				'status'        => 'demnaechst',
				'property_type' => 'Etagenwohnung',
				'rooms'         => '2',
				'street'        => 'Wilhelm-Hauff-Str. 34',
				'zip'           => '86161',
				'city'          => 'Augsburg',
				'show_address'  => '1',
				'teaser'        => "Durchdacht modernisiert, mit Liebe zum Bestand\nZwei gut geschnittene Zimmer\nBilder, Grundriss & Preis folgen in Kürze",
			),
			'images'  => array(),
		),
		array(
			'title'   => 'Altbauwohnung mit Erker (Beispiel)',
			'excerpt' => 'Beispielobjekt zur Vorschau: lichtdurchflutete Altbauwohnung mit Erker, aufgearbeiteten Dielen und neuem Bad.',
			'content' => "<p><strong>Hinweis: Dies ist ein Beispielobjekt zur Vorschau des Designs.</strong> Es kann unter <em>Immobilien</em> gelöscht werden.</p>\n<p>Hohe Decken, Stuck und originale Dielen: In dieser Altbauwohnung haben wir die historische Substanz bewahrt und behutsam ergänzt – mit einem neuen Bad, moderner Haustechnik und einer offenen Küche.</p>",
			'term'    => 'München',
			'demo'    => true,
			'meta'    => array(
				'status'         => 'verfuegbar',
				'property_type'  => 'Etagenwohnung',
				'price'          => '849000',
				'rooms'          => '3',
				'area'           => '92',
				'floor'          => '2. OG von 4',
				'year_built'     => '1908',
				'modernized'     => '2026 behutsam saniert',
				'condition'      => 'Erstbezug nach Sanierung',
				'heating'        => 'Zentralheizung',
				'city'           => 'München-Haidhausen',
				'energy_cert'    => 'Verbrauchsausweis',
				'energy_value'   => '118',
				'energy_class'   => 'D',
				'energy_carrier' => 'Fernwärme',
				'available_from' => 'nach Vereinbarung',
				'features'       => "Aufgearbeitete Original-Dielen\nStuckdecken, ca. 3,40 m Raumhöhe\nNeues Tageslichtbad mit Dusche\nOffene Küche mit Einbaugeräten\nKellerabteil",
			),
			'images'  => array( 'altbau', 'wohnen', 'kueche', 'detail' ),
		),
		array(
			'title'   => 'Helle Stadtwohnung mit Balkon (Beispiel)',
			'excerpt' => 'Beispielobjekt zur Vorschau: modernisierte 2-Zimmer-Wohnung mit Westbalkon in ruhiger Lage.',
			'content' => "<p><strong>Hinweis: Dies ist ein Beispielobjekt zur Vorschau des Designs.</strong></p>\n<p>Eine gut geschnittene Wohnung, die wir mit hochwertigen, langlebigen Materialien modernisiert haben.</p>",
			'term'    => 'Augsburg',
			'demo'    => true,
			'meta'    => array(
				'status'        => 'reserviert',
				'property_type' => 'Etagenwohnung',
				'price'         => '389000',
				'rooms'         => '2',
				'area'          => '61.5',
				'floor'         => '3. OG',
				'year_built'    => '1962',
				'condition'     => 'Modernisiert',
				'city'          => 'Augsburg',
				'energy_cert'   => 'Bedarfsausweis',
				'energy_value'  => '96.4',
				'energy_class'  => 'C',
				'energy_carrier' => 'Gas',
			),
			'images'  => array( 'wohnzimmer', 'detail', 'kueche' ),
		),
		array(
			'title'   => 'Dachgeschoss mit Weitblick (Beispiel)',
			'excerpt' => 'Beispielobjekt zur Vorschau: ausgebautes Dachgeschoss mit Sichtdachstuhl.',
			'content' => '<p><strong>Hinweis: Dies ist ein Beispielobjekt zur Vorschau des Designs.</strong></p>',
			'term'    => 'München',
			'demo'    => true,
			'meta'    => array(
				'status'        => 'verkauft',
				'property_type' => 'Dachgeschosswohnung',
				'price'         => '695000',
				'rooms'         => '2.5',
				'area'          => '74',
				'city'          => 'München-Bogenhausen',
				'condition'     => 'Saniert',
			),
			'images'  => array( 'wohnen', 'altbau' ),
		),
	);

	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	foreach ( array_reverse( $properties ) as $p ) {
		if ( get_posts( array( 'post_type' => 'immobilie', 'title' => $p['title'], 'post_status' => 'any', 'fields' => 'ids' ) ) ) {
			continue;
		}
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'immobilie',
				'post_status'  => 'publish',
				'post_title'   => $p['title'],
				'post_excerpt' => $p['excerpt'],
				'post_content' => $p['content'],
			)
		);
		if ( ! $post_id || is_wp_error( $post_id ) ) {
			continue;
		}
		wp_set_object_terms( $post_id, $terms[ $p['term'] ] ?: array(), 'standort' );
		foreach ( $p['meta'] as $key => $value ) {
			update_post_meta( $post_id, '_imova_' . $key, $value );
		}
		if ( $p['demo'] ) {
			update_post_meta( $post_id, '_imova_demo', 1 );
		}

		$gallery = array();
		foreach ( $p['images'] as $i => $image ) {
			$attachment = imova_sideload_demo_image( $image, $post_id );
			if ( ! $attachment ) {
				continue;
			}
			if ( 0 === $i ) {
				set_post_thumbnail( $post_id, $attachment );
			} else {
				$gallery[] = $attachment;
			}
		}
		update_post_meta( $post_id, '_imova_gallery', implode( ',', $gallery ) );
	}
}

/**
 * Importiert ein mitgeliefertes Platzhalterbild einmalig in die Mediathek.
 */
function imova_sideload_demo_image( string $name, int $post_id ): int {
	static $cache = array();
	if ( isset( $cache[ $name ] ) ) {
		return $cache[ $name ];
	}
	$source = IMOVA_DIR . '/assets/img/demo/' . $name . '.jpg';
	if ( ! file_exists( $source ) ) {
		return 0;
	}
	$tmp = wp_tempnam( $name . '.jpg' );
	copy( $source, $tmp );
	$id = media_handle_sideload(
		array(
			'name'     => 'imova-' . $name . '.jpg',
			'tmp_name' => $tmp,
		),
		$post_id,
		'Platzhalterbild'
	);
	if ( is_wp_error( $id ) ) {
		wp_delete_file( $tmp );
		return 0;
	}
	update_post_meta( $id, '_imova_demo', 1 );
	update_post_meta( $id, '_wp_attachment_image_alt', 'Wohnbeispiel (Platzhalterbild)' );
	return $cache[ $name ] = (int) $id;
}

/* --------------------------------------------------------------------------
 * Hinweis & Ein-Klick-Entfernung der Beispielobjekte
 * ----------------------------------------------------------------------- */

add_action(
	'admin_notices',
	function () {
		if ( ! current_user_can( 'delete_posts' ) ) {
			return;
		}
		$demo = get_posts( array( 'post_type' => 'immobilie', 'meta_key' => '_imova_demo', 'posts_per_page' => -1, 'fields' => 'ids', 'post_status' => 'any' ) );
		if ( ! $demo ) {
			return;
		}
		printf(
			'<div class="notice notice-info"><p><strong>IMOVA:</strong> Es sind noch %d Beispielobjekte mit Platzhalterbildern online. Vor dem Livegang bitte entfernen. <a class="button button-small" href="%s" onclick="return confirm(\'Beispielobjekte und ihre Platzhalterbilder endgültig löschen?\')">Beispielobjekte löschen</a></p></div>',
			count( $demo ),
			esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=imova_remove_demo' ), 'imova_remove_demo' ) )
		);
	}
);

add_action(
	'admin_post_imova_remove_demo',
	function () {
		check_admin_referer( 'imova_remove_demo' );
		if ( ! current_user_can( 'delete_posts' ) ) {
			wp_die( 'Keine Berechtigung.' );
		}
		foreach ( get_posts( array( 'post_type' => 'immobilie', 'meta_key' => '_imova_demo', 'posts_per_page' => -1, 'fields' => 'ids', 'post_status' => 'any' ) ) as $id ) {
			wp_delete_post( $id, true );
		}
		foreach ( get_posts( array( 'post_type' => 'attachment', 'meta_key' => '_imova_demo', 'posts_per_page' => -1, 'fields' => 'ids', 'post_status' => 'any' ) ) as $id ) {
			wp_delete_attachment( $id, true );
		}
		wp_safe_redirect( admin_url( 'edit.php?post_type=immobilie' ) );
		exit;
	}
);

/* --------------------------------------------------------------------------
 * Standardtexte
 * ----------------------------------------------------------------------- */

function imova_default_about_story(): string {
	return <<<HTML
<!-- wp:heading -->
<h2 class="wp-block-heading">Wie alles begann</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>[Platzhalter – dieser Abschnitt wird mit Sebastians eigener Geschichte gefüllt.] Hier erzählt Sebastian Fäth, woher er kommt, wie er zur Immobilie gefunden hat und welche Erfahrung er mitbringt – persönlich, in eigenen Worten.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>[Platzhalter] Was ihn an Bestandsimmobilien reizt: das Potenzial, das in Häusern mit Geschichte steckt, und die Freude daran, es mit durchdachten Modernisierungen sichtbar zu machen.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Die Idee hinter IMOVA</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>[Platzhalter] Mit IMOVA PROJEKT verbindet er zeitgemäße Wohnqualität mit einem verantwortungsvollen Umgang mit Ressourcen – und mit einem Geschäftsmodell, das Käufern wie Verkäufern die Maklerprovision erspart.</p>
<!-- /wp:paragraph -->
HTML;
}

function imova_default_imprint(): string {
	$s = 'imova_opt';
	return <<<HTML
<!-- wp:heading -->
<h2 class="wp-block-heading">Angaben gemäß § 5 DDG</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>{$s('company')}<br>{$s('street')}<br>{$s('zip_city')}</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Vertreten durch den Geschäftsführer:</strong><br>{$s('director')}</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Kontakt</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Telefon: {$s('phone')}<br>E-Mail: <a href="mailto:{$s('email')}">{$s('email')}</a></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Registereintrag</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Eintragung im Handelsregister<br>Registergericht: {$s('register_court')}<br>Registernummer: {$s('register_number')}</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Umsatzsteuer-ID</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>[Platzhalter – Umsatzsteuer-Identifikationsnummer gemäß § 27a UStG, sofern vorhanden. Andernfalls diesen Abschnitt löschen.]</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Erlaubnis nach § 34c GewO / Aufsichtsbehörde</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>[Platzhalter – sofern eine Erlaubnis nach § 34c GewO besteht (z. B. als Bauträger oder Immobilienmakler), hier die zuständige Aufsichtsbehörde mit Anschrift angeben. Andernfalls diesen Abschnitt löschen.]</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Verantwortlich für den Inhalt nach § 18 Abs. 2 MStV</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>{$s('director')}<br>{$s('street')}, {$s('zip_city')}</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Verbraucherstreitbeilegung</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Wir sind nicht bereit oder verpflichtet, an Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teilzunehmen.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Bildnachweise</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>[Platzhalter – Bildquellen angeben, z. B. „Fotos: Unsplash“ bzw. die Fotografen der Objektbilder.]</p>
<!-- /wp:paragraph -->
HTML;
}

function imova_default_privacy(): string {
	$s = 'imova_opt';
	return <<<HTML
<!-- wp:paragraph -->
<p><em>Stand: Oktober 2026. [Hinweis für den Betreiber: Diese Vorlage ist auf die Funktionen dieser Website zugeschnitten, ersetzt aber keine Rechtsberatung. Bitte vor Veröffentlichung prüfen lassen und die Angaben zum Hosting ergänzen.]</em></p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">1. Verantwortlicher</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>{$s('company')}<br>{$s('street')}, {$s('zip_city')}<br>Vertreten durch: {$s('director')}<br>Telefon: {$s('phone')}<br>E-Mail: <a href="mailto:{$s('email')}">{$s('email')}</a></p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">2. Allgemeines</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Wir verarbeiten personenbezogene Daten nur, soweit dies für den Betrieb dieser Website, die Bearbeitung Ihrer Anfragen oder die Anbahnung und Durchführung von Verträgen erforderlich ist. Rechtsgrundlagen sind insbesondere Art. 6 Abs. 1 lit. a DSGVO (Einwilligung), lit. b (Vertrag bzw. vorvertragliche Maßnahmen), lit. c (rechtliche Verpflichtung) und lit. f (berechtigtes Interesse). Wir setzen keine Analyse- oder Marketing-Tools ein.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">3. Hosting und Server-Logfiles</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Diese Website wird bei [Platzhalter: Name und Anschrift des Hosting-Anbieters] gehostet. Mit diesem besteht ein Vertrag zur Auftragsverarbeitung nach Art. 28 DSGVO. Beim Aufruf der Website werden technisch notwendige Daten (IP-Adresse, Datum und Uhrzeit, aufgerufene Seite, Browser und Betriebssystem, Referrer-URL) in Server-Logfiles gespeichert, um den sicheren und stabilen Betrieb zu gewährleisten (Art. 6 Abs. 1 lit. f DSGVO). Die Logfiles werden nach spätestens [Platzhalter: z. B. 14] Tagen gelöscht.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Die Übertragung erfolgt verschlüsselt (TLS). Schriftarten werden lokal von unserem Server geladen; es besteht keine Verbindung zu Google Fonts oder anderen Schriftanbietern.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">4. Kontakt- und Anfrageformulare</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Auf dieser Website bieten wir Formulare für allgemeine Anfragen, für Interesse an einer Immobilie, für Verkaufsangebote und für Tippgeber-Hinweise an. Wir verarbeiten die von Ihnen eingegebenen Daten (z. B. Name, E-Mail-Adresse, Telefonnummer, Angaben zur Immobilie, Nachricht und ggf. hochgeladene Unterlagen), um Ihre Anfrage zu bearbeiten und mit Ihnen in Kontakt zu treten. Rechtsgrundlage ist Art. 6 Abs. 1 lit. b DSGVO, soweit Ihre Anfrage auf einen Vertrag gerichtet ist, im Übrigen Art. 6 Abs. 1 lit. f DSGVO (berechtigtes Interesse an der Beantwortung von Anfragen).</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Die Anfragen werden in unserem Website-System gespeichert und uns per E-Mail gemeldet. Hochgeladene Dateien werden in einem geschützten, nicht öffentlich zugänglichen Bereich abgelegt. Wir löschen die Daten, sobald die Anfrage abschließend bearbeitet ist und keine gesetzlichen Aufbewahrungspflichten entgegenstehen.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Angaben zu Dritten (Tippgeber):</strong> Wenn Sie uns im Rahmen eines Tipps Daten des Eigentümers einer Immobilie mitteilen, verarbeiten wir diese auf Grundlage unseres berechtigten Interesses am Erwerb von Immobilien (Art. 6 Abs. 1 lit. f DSGVO). Bitte geben Sie solche Daten nur weiter, wenn Sie dazu berechtigt sind. Wir informieren betroffene Eigentümer bei der ersten Kontaktaufnahme gemäß Art. 14 DSGVO.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Weitergabe an Finanzierungsberater:</strong> Objektunterlagen oder Ihre Kontaktdaten geben wir nur mit Ihrer ausdrücklichen Einwilligung an einen von Ihnen gewünschten Finanzierungsberater weiter (Art. 6 Abs. 1 lit. a DSGVO).</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">5. Kontakt per E-Mail oder Telefon</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Wenn Sie uns per E-Mail oder Telefon kontaktieren, verarbeiten wir Ihre Angaben zur Bearbeitung Ihres Anliegens (Art. 6 Abs. 1 lit. b bzw. f DSGVO).</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">6. Cookies und lokale Speicherung</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Wir verwenden keine Tracking-Cookies. Ihre Auswahl im Hinweisfenster zu externen Medien speichern wir im lokalen Speicher Ihres Browsers (localStorage), damit wir Sie nicht bei jedem Besuch erneut fragen müssen. Dies ist technisch erforderlich (§ 25 Abs. 2 Nr. 2 TDDDG). Sie können Ihre Auswahl jederzeit über den Link „Cookie-Einstellungen“ im Footer ändern.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">7. Google Maps</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Zur Darstellung von Karten nutzen wir Google Maps der Google Ireland Limited, Gordon House, Barrow Street, Dublin 4, Irland. Die Karte wird erst geladen, wenn Sie dem ausdrücklich zustimmen (Art. 6 Abs. 1 lit. a DSGVO, § 25 Abs. 1 TDDDG). Dabei wird u. a. Ihre IP-Adresse an Google übertragen; eine Übermittlung in die USA ist möglich. Google ist unter dem EU-US Data Privacy Framework zertifiziert. Ihre Einwilligung können Sie jederzeit über die „Cookie-Einstellungen“ widerrufen. Weitere Informationen: <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">policies.google.com/privacy</a>.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">8. Ihre Rechte</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Sie haben das Recht auf Auskunft (Art. 15 DSGVO), Berichtigung (Art. 16), Löschung (Art. 17), Einschränkung der Verarbeitung (Art. 18), Datenübertragbarkeit (Art. 20) sowie Widerspruch gegen Verarbeitungen auf Grundlage berechtigter Interessen (Art. 21). Eine erteilte Einwilligung können Sie jederzeit mit Wirkung für die Zukunft widerrufen. Wenden Sie sich dazu einfach an die oben genannten Kontaktdaten.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Zudem haben Sie das Recht, sich bei einer Datenschutz-Aufsichtsbehörde zu beschweren. Für uns zuständig ist das Bayerische Landesamt für Datenschutzaufsicht (BayLDA), Promenade 18, 91522 Ansbach.</p>
<!-- /wp:paragraph -->
HTML;
}
