<?php
/**
 * Ersteinrichtung: Seiten, Menüs, erstes Objekt (Augsburg) und Beispielobjekte.
 * Läuft einmalig beim Aktivieren des Themes (oder per `wp imova setup`).
 *
 * @package imova
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_switch_theme', 'imova_run_setup' );
add_action( 'init', 'imova_run_updates', 20 );

/**
 * Aktualisiert bei bestehenden Installationen einmalig Inhalte, die noch dem
 * ursprünglichen Standardtext entsprechen. Selbst bearbeitete Inhalte bleiben unberührt.
 */
function imova_run_updates(): void {
	$version = (string) get_option( 'imova_content_version', '1.0.0' );
	if ( version_compare( $version, '1.3.0', '>=' ) || ! get_option( 'imova_setup_done' ) ) {
		if ( version_compare( $version, IMOVA_VERSION, '<' ) ) {
			update_option( 'imova_content_version', IMOVA_VERSION );
		}
		return;
	}

	// Firmenname in der Schreibweise des Handelsregisters.
	$settings = (array) get_option( 'imova_settings', array() );
	if ( ( $settings['company'] ?? '' ) === 'IMOVA PROJEKT GmbH' ) {
		$settings['company'] = 'Imova Projekt GmbH';
		update_option( 'imova_settings', $settings );
	}

	$update = function ( string $slug, string $marker, callable $content ) {
		$page = get_page_by_path( $slug );
		if ( $page && str_contains( $page->post_content, $marker ) ) {
			wp_update_post( array( 'ID' => $page->ID, 'post_content' => $content( $page->post_content ) ) );
		}
	};
	$update( 'impressum', '[Platzhalter – Umsatzsteuer', fn() => imova_default_imprint() );
	$update( 'ueber-uns', '[Platzhalter – dieser Abschnitt', fn() => imova_default_about_story() );
	$update( 'datenschutz', 'IMOVA PROJEKT GmbH', fn( $c ) => str_replace( 'IMOVA PROJEKT GmbH', 'Imova Projekt GmbH', $c ) );
	$update( 'datenschutz', '[Platzhalter: z. B. 14]', fn( $c ) => str_replace( 'Die Logfiles werden nach spätestens [Platzhalter: z. B. 14] Tagen gelöscht.', 'Die Logfiles werden nach kurzer Zeit automatisch gelöscht, sofern nicht ein Sicherheitsvorfall eine längere Aufbewahrung erfordert.', $c ) );
	$update( 'datenschutz', '[Platzhalter: Name und Anschrift des Hosting-Anbieters]', fn( $c ) => str_replace( array( '[Platzhalter: Name und Anschrift des Hosting-Anbieters]', 'Bitte vor Veröffentlichung prüfen lassen und die Angaben zum Hosting ergänzen.' ), array( 'der IONOS SE, Elgendorfer Straße 57, 56410 Montabaur', 'Bitte vor Veröffentlichung prüfen lassen.' ), $c ) );

	// 1.2.0
	$update( 'ueber-uns', 'Mit IMOVA PROJEKT möchte ich', fn( $c ) => str_replace( 'Mit IMOVA PROJEKT möchte ich', 'Mit Imova möchte ich', $c ) );
	$update( 'impressum', '[Platzhalter – Bildquellen', fn( $c ) => str_replace( '[Platzhalter – Bildquellen angeben, z. B. „Fotos: Unsplash“ bzw. die Fotografen der Objektbilder.]', 'Symbolbilder: Unsplash (unsplash.com)', $c ) );

	// 1.2.1 – Hinweis für den Betreiber nicht öffentlich anzeigen
	$update( 'datenschutz', '[Hinweis für den Betreiber:', fn( $c ) => preg_replace( '/Stand: ([^.<]+)\. \[Hinweis für den Betreiber:[^\]]*\]/u', 'Stand: $1', $c ) );

	// 1.3.0 – WhatsApp in der Datenschutzerklärung
	$wa_block = <<<'HTML'
<!-- wp:heading -->
<h2 class="wp-block-heading">8. Kontakt über WhatsApp</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Sie können uns freiwillig über WhatsApp kontaktieren, einen Dienst der WhatsApp Ireland Limited (Dublin, Irland). Der Button auf unserer Website ist ein einfacher Link: Daten werden erst übertragen, wenn Sie WhatsApp öffnen und uns eine Nachricht senden. Dabei verarbeitet WhatsApp u. a. Ihre Telefonnummer und Metadaten der Kommunikation; eine Übermittlung in die USA ist möglich (Meta Platforms ist unter dem EU-US Data Privacy Framework zertifiziert). Wir nutzen Ihre Nachricht ausschließlich zur Bearbeitung Ihres Anliegens (Art. 6 Abs. 1 lit. b bzw. f DSGVO). Wenn Sie WhatsApp nicht nutzen möchten, erreichen Sie uns ebenso per Telefon, E-Mail oder Kontaktformular. Weitere Informationen: <a href="https://www.whatsapp.com/legal/privacy-policy-eea" target="_blank" rel="noopener">whatsapp.com/legal/privacy-policy-eea</a>.</p>
<!-- /wp:paragraph -->
HTML;
	$update( 'datenschutz', '<h2 class="wp-block-heading">8. Ihre Rechte</h2>', fn( $c ) => str_contains( $c, 'WhatsApp' ) ? $c : str_replace( "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">8. Ihre Rechte</h2>", $wa_block . "\n\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">9. Ihre Rechte</h2>", $c ) );

	update_option( 'imova_content_version', '1.3.0' );
}

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
<p>Mein beruflicher Weg begann ursprünglich in der Automobilbranche. Durch meine Familie haben Immobilien aber schon immer eine wichtige Rolle in meinem Leben gespielt. Aus diesem Interesse wurde mit der Zeit eine echte Leidenschaft – und irgendwann der Wunsch, beruflich meinen eigenen Weg zu gehen.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Also habe ich den Schritt gewagt und mich in der Immobilienbranche selbstständig gemacht. Eine Entscheidung, über die ich heute sehr froh bin.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Gemeinsam mit meinem Vater bin ich auch im Neubau tätig. Mindestens genauso spannend finde ich es aber, das Potenzial bestehender Immobilien zu entdecken, sie weiterzuentwickeln und neuen Wohnraum zu schaffen. Besonders wichtig ist mir dabei, vorhandene Substanz sinnvoll zu erhalten und meinen Beitrag zu attraktivem, bezahlbarem Wohnraum zu leisten.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Die Idee hinter IMOVA</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><em>Aus Alt mach Neu – mit Respekt vor dem Bestehenden.</em></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Nicht jede Immobilie muss neu gebaut werden, um etwas Besonderes zu sein. Oft steckt gerade in älteren Wohnungen und Häusern viel mehr Potenzial, als man auf den ersten Blick vermutet.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Genau das begeistert mich: Bestehende Immobilien neu zu denken, ihre Stärken hervorzuheben und mit durchdachten Modernisierungen etwas zu schaffen, das wieder viele Jahre Freude bereitet.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Mit Imova möchte ich zeigen, wie sich der Charme bestehender Immobilien mit modernem Wohnkomfort verbinden lässt. Dabei geht es mir nicht darum, alles Alte einfach durch Neues zu ersetzen, sondern vorhandene Werte zu erhalten, Ressourcen bewusst einzusetzen und Wohnraum zu schaffen, der auch langfristig überzeugt.</p>
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
<h3 class="wp-block-heading">Erlaubnis nach § 34c GewO / Aufsichtsbehörde</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Erlaubnis nach § 34c Abs. 1 Satz 1 Nr. 1 GewO (Immobilienmakler) und Nr. 3b GewO (Baubetreuer), erteilt durch die zuständige Aufsichtsbehörde:</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>IHK für München und Oberbayern<br>Max-Joseph-Straße 2, 80333 München<br><a href="https://www.ihk-muenchen.de" target="_blank" rel="noopener">www.ihk-muenchen.de</a></p>
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
<p>Symbolbilder: Unsplash (unsplash.com)</p>
<!-- /wp:paragraph -->
HTML;
}

function imova_default_privacy(): string {
	$s = 'imova_opt';
	return <<<HTML
<!-- wp:paragraph -->
<p><em>Stand: Oktober 2026</em></p>
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
<p>Diese Website wird bei der IONOS SE, Elgendorfer Straße 57, 56410 Montabaur gehostet. Mit diesem besteht ein Vertrag zur Auftragsverarbeitung nach Art. 28 DSGVO. Beim Aufruf der Website werden technisch notwendige Daten (IP-Adresse, Datum und Uhrzeit, aufgerufene Seite, Browser und Betriebssystem, Referrer-URL) in Server-Logfiles gespeichert, um den sicheren und stabilen Betrieb zu gewährleisten (Art. 6 Abs. 1 lit. f DSGVO). Die Logfiles werden nach kurzer Zeit automatisch gelöscht, sofern nicht ein Sicherheitsvorfall eine längere Aufbewahrung erfordert.</p>
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
<h2 class="wp-block-heading">8. Kontakt über WhatsApp</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Sie können uns freiwillig über WhatsApp kontaktieren, einen Dienst der WhatsApp Ireland Limited (Dublin, Irland). Der Button auf unserer Website ist ein einfacher Link: Daten werden erst übertragen, wenn Sie WhatsApp öffnen und uns eine Nachricht senden. Dabei verarbeitet WhatsApp u. a. Ihre Telefonnummer und Metadaten der Kommunikation; eine Übermittlung in die USA ist möglich (Meta Platforms ist unter dem EU-US Data Privacy Framework zertifiziert). Wir nutzen Ihre Nachricht ausschließlich zur Bearbeitung Ihres Anliegens (Art. 6 Abs. 1 lit. b bzw. f DSGVO). Wenn Sie WhatsApp nicht nutzen möchten, erreichen Sie uns ebenso per Telefon, E-Mail oder Kontaktformular. Weitere Informationen: <a href="https://www.whatsapp.com/legal/privacy-policy-eea" target="_blank" rel="noopener">whatsapp.com/legal/privacy-policy-eea</a>.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">9. Ihre Rechte</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Sie haben das Recht auf Auskunft (Art. 15 DSGVO), Berichtigung (Art. 16), Löschung (Art. 17), Einschränkung der Verarbeitung (Art. 18), Datenübertragbarkeit (Art. 20) sowie Widerspruch gegen Verarbeitungen auf Grundlage berechtigter Interessen (Art. 21). Eine erteilte Einwilligung können Sie jederzeit mit Wirkung für die Zukunft widerrufen. Wenden Sie sich dazu einfach an die oben genannten Kontaktdaten.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Zudem haben Sie das Recht, sich bei einer Datenschutz-Aufsichtsbehörde zu beschweren. Für uns zuständig ist das Bayerische Landesamt für Datenschutzaufsicht (BayLDA), Promenade 18, 91522 Ansbach.</p>
<!-- /wp:paragraph -->
HTML;
}
