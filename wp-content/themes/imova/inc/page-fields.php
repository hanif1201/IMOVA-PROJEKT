<?php
/**
 * „Seiteninhalte“: bearbeitbare Texte & Bilder je Seitentemplate.
 * Jedes Feld hat einen Standardtext, damit die Seite sofort vollständig aussieht.
 * Im Text kann {provision} verwendet werden – wird durch die Provision aus den Einstellungen ersetzt.
 *
 * @package imova
 */

defined( 'ABSPATH' ) || exit;

function imova_page_field_config(): array {
	$t  = fn( $label, $default = '' ) => array( 'label' => $label, 'type' => 'text', 'default' => $default );
	$ta = fn( $label, $default = '', $rows = 3 ) => array( 'label' => $label, 'type' => 'textarea', 'default' => $default, 'rows' => $rows );
	$im = fn( $label, $fallback = '' ) => array( 'label' => $label, 'type' => 'image', 'fallback' => $fallback );

	return array(
		'front' => array(
			'Hero' => array(
				'hero_eyebrow' => $t( 'Überzeile', 'Immobilien & Projektentwicklung · München & Augsburg' ),
				'hero_title'   => $ta( 'Überschrift', "Bestehendes bewahren.\nWerte für morgen schaffen.", 2 ),
				'hero_text'    => $ta( 'Einleitung', 'Wir erkennen das Potenzial bestehender Immobilien und entwickeln sie mit durchdachten Modernisierungen weiter – modern, wo es zählt, mit Charme, wo er erhalten ist. Persönlich und direkt, ohne Maklerprovision.' ),
				'hero_image'   => $im( 'Hero-Bild', 'hero' ),
			),
			'Philosophie' => array(
				'phil_eyebrow' => $t( 'Überzeile', 'Unsere Haltung' ),
				'phil_title'   => $t( 'Überschrift', 'Das Potenzial im Bestand sehen.' ),
				'phil_text'    => $ta( 'Text', 'Wir erkennen das Potenzial bestehender Immobilien und entwickeln sie durch durchdachte Modernisierungen weiter. Dabei verbinden wir Neues mit Bewährtem: Gut erhaltene Materialien und Altbaucharme bleiben bestehen, wo sie den Charakter einer Immobilie prägen – alles andere bringen wir mit hochwertigen, langlebigen Materialien auf den Stand von heute. So entsteht zeitgemäße Wohnqualität mit Charakter.', 5 ),
				'phil_image'   => $im( 'Bild', 'altbau' ),
				'pillar1_title' => $t( 'Grundsatz 1 – Titel', 'Altbaucharme bewahren' ),
				'pillar1_text'  => $ta( 'Grundsatz 1 – Text', 'Stuck, Dielen, hohe Decken: Was gut erhalten ist und den Charakter eines Hauses ausmacht, bleibt bestehen.' ),
				'pillar2_title' => $t( 'Grundsatz 2 – Titel', 'Modern, wo es zählt' ),
				'pillar2_text'  => $ta( 'Grundsatz 2 – Text', 'Bäder, Küchen, Haustechnik und Oberflächen erneuern wir zeitgemäß – für Wohnqualität, wie man sie heute erwartet.' ),
				'pillar3_title' => $t( 'Grundsatz 3 – Titel', 'Langlebige Materialien' ),
				'pillar3_text'  => $ta( 'Grundsatz 3 – Text', 'Wir setzen auf Materialien, die in Würde altern – und auch in zwanzig Jahren noch Freude machen.' ),
			),
			'Immobilien' => array(
				'list_title' => $t( 'Überschrift', 'Aktuelle Immobilien' ),
				'list_text'  => $ta( 'Text', 'Ausgewählte Wohnungen und Häuser – direkt vom Eigentümer, ohne Maklerprovision.' ),
			),
			'Provision & Finanzierung' => array(
				'savings_eyebrow' => $t( 'Überzeile', 'Direkt statt über Makler' ),
				'savings_title'   => $t( 'Überschrift', '{provision} % Provision sparen.' ),
				'savings_text'    => $ta( 'Text', 'Ob Sie von uns kaufen oder an uns verkaufen: Sie handeln direkt mit dem Eigentümer. Die übliche Maklerprovision von {provision} % entfällt – Geld, das in Ihrem Zuhause besser aufgehoben ist.' ),
				'finance_title'   => $t( 'Finanzierung – Titel', 'Finanzierung aus einer Hand' ),
				'finance_text'    => $ta( 'Finanzierung – Text', 'Wir arbeiten mit erfahrenen Finanzierungsberatern zusammen, die Ihnen direkt günstige Konditionen vermitteln. Und Sie sparen sich den Papierkram: Alle Unterlagen, die für die Finanzierung benötigt werden, leiten wir direkt an Ihren Finanzberater weiter – Sie müssen nichts beim Verkäufer anfordern oder selbst weiterleiten.', 4 ),
				'finance_point1'  => $t( 'Finanzierung – Punkt 1', 'Günstige Konditionen über erfahrene Partner' ),
				'finance_point2'  => $t( 'Finanzierung – Punkt 2', 'Keine Unterlagen beim Verkäufer anfordern' ),
				'finance_point3'  => $t( 'Finanzierung – Punkt 3', 'Wir senden alles direkt an Ihren Finanzberater' ),
				'finance_point4'  => $t( 'Finanzierung – Punkt 4', 'Kein Aufwand für Sie – schnell Klarheit' ),
			),
			'Tippgeber-Teaser' => array(
				'tipp_eyebrow' => $t( 'Überzeile', 'Tippgeber werden' ),
				'tipp_title'   => $t( 'Überschrift', 'Sie wissen von einer Immobilie, die verkauft werden soll?' ),
				'tipp_text'    => $ta( 'Text', 'Geben Sie uns einen Hinweis. Kommt der Ankauf durch Ihren Tipp zustande, erhalten Sie eine Provision – vertraulich und ohne jede Verpflichtung.' ),
				'tipp_image'   => $im( 'Bild', 'wohnen' ),
			),
			'Vorteile-Leiste (unter dem Hero)' => array(
				'trust1_title' => $t( 'Punkt 1 – Titel', 'München & Augsburg' ),
				'trust1_text'  => $t( 'Punkt 1 – Text', 'Regional verwurzelt, vor Ort für Sie da' ),
				'trust2_title' => $t( 'Punkt 2 – Titel', 'Finanzierung ohne Papierkram' ),
				'trust2_text'  => $t( 'Punkt 2 – Text', 'Unterlagen gehen direkt an Ihren Berater' ),
				'trust3_title' => $t( 'Punkt 3 – Titel', 'Ohne Maklerprovision' ),
				'trust3_text'  => $t( 'Punkt 3 – Text', 'Direkt vom bzw. an den Eigentümer' ),
				'trust4_title' => $t( 'Punkt 4 – Titel', 'Persönlich erreichbar' ),
				'trust4_text'  => $t( 'Punkt 4 – Text', 'Direkt beim Geschäftsführer' ),
			),
			'Kontakt-Abschluss' => array(
				'cta_title' => $t( 'Überschrift', 'Lassen Sie uns sprechen.' ),
				'cta_text'  => $ta( 'Text', 'Ob Kauf, Verkauf oder ein Tipp – bei IMOVA sprechen Sie direkt mit dem Geschäftsführer. Persönlich, verbindlich und ohne Umwege.' ),
			),
		),

		'page-templates/ueber-uns.php' => array(
			'Kopfbereich' => array(
				'hero_title' => $t( 'Überschrift', 'Über uns' ),
				'hero_text'  => $ta( 'Einleitung', 'Hinter IMOVA steht kein anonymer Konzern, sondern ein persönlich geführtes Unternehmen mit klarer Haltung – und zwei treuen Begleitern auf vier Pfoten.' ),
			),
			'Gründer' => array(
				'founder_image' => $im( 'Foto' ),
				'founder_name'  => $t( 'Name', 'Sebastian Fäth' ),
				'founder_role'  => $t( 'Funktion', 'Geschäftsführer' ),
				'founder_quote' => $ta( 'Zitat', 'Jedes Haus hat eine Geschichte. Unsere Aufgabe ist es, das nächste Kapitel gut zu schreiben.', 2 ),
			),
			'Team auf vier Pfoten' => array(
				'dogs_title' => $t( 'Überschrift', 'Das Team auf vier Pfoten' ),
				'dogs_text'  => $ta( 'Einleitung', 'Ohne sie läuft bei uns nichts – zumindest nicht pünktlich zur Gassi-Zeit.' ),
				'dog1_image' => $im( 'Hund 1 – Foto', 'hund-1' ),
				'dog1_name'  => $t( 'Hund 1 – Name', 'Name folgt' ),
				'dog1_role'  => $t( 'Hund 1 – Rolle', 'Chief Happiness Officer' ),
				'dog1_text'  => $ta( 'Hund 1 – Beschreibung', 'Verantwortlich für gute Laune bei Besichtigungen, herzliche Begrüßungen und die tägliche Qualitätskontrolle aller Snacks.' ),
				'dog2_image' => $im( 'Hund 2 – Foto', 'hund-2' ),
				'dog2_name'  => $t( 'Hund 2 – Name', 'Name folgt' ),
				'dog2_role'  => $t( 'Hund 2 – Rolle', 'Head of Security' ),
				'dog2_text'  => $ta( 'Hund 2 – Beschreibung', 'Prüft jede Immobilie mit vollem Einsatz auf Herz und Nieren – sofern er nicht gerade schläft. Jeder Besucher wird gründlich (und sehr freundlich) kontrolliert.' ),
			),
			'Werte' => array(
				'values_title' => $t( 'Überschrift', 'Wofür wir stehen' ),
				'value1_title' => $t( 'Wert 1 – Titel', 'Verantwortung' ),
				'value1_text'  => $ta( 'Wert 1 – Text', 'Wir modernisieren mit Blick auf Ressourcen: erhalten, was gut ist, erneuern, was nötig ist.' ),
				'value2_title' => $t( 'Wert 2 – Titel', 'Ehrlichkeit' ),
				'value2_text'  => $ta( 'Wert 2 – Text', 'Klare Aussagen, faire Preise, keine versteckten Kosten. Was wir zusagen, halten wir.' ),
				'value3_title' => $t( 'Wert 3 – Titel', 'Nähe' ),
				'value3_text'  => $ta( 'Wert 3 – Text', 'Kurze Wege, ein fester Ansprechpartner und echtes Interesse an den Menschen hinter jeder Immobilie.' ),
			),
		),

		'page-templates/tippgeber.php' => array(
			'Kopfbereich' => array(
				'hero_title' => $t( 'Überschrift', 'Tippgeber werden' ),
				'hero_text'  => $ta( 'Einleitung', 'Sie wissen von einer Wohnung oder einem Haus, das verkauft werden soll? Geben Sie uns einen Hinweis – kommt der Ankauf zustande, werden Sie am Erfolg beteiligt.' ),
				'hero_image' => $im( 'Bild', 'detail' ),
			),
			'So funktioniert’s' => array(
				'steps_title' => $t( 'Überschrift', 'So funktioniert’s' ),
				'step1_title' => $t( 'Schritt 1 – Titel', 'Tipp abgeben' ),
				'step1_text'  => $ta( 'Schritt 1 – Text', 'Teilen Sie uns über das Formular mit, um welche Immobilie es geht. Ein paar Eckdaten genügen.' ),
				'step2_title' => $t( 'Schritt 2 – Titel', 'Wir prüfen' ),
				'step2_text'  => $ta( 'Schritt 2 – Text', 'Wir bewerten das Objekt und nehmen – wenn gewünscht – diskret Kontakt zum Eigentümer auf.' ),
				'step3_title' => $t( 'Schritt 3 – Titel', 'Ankauf' ),
				'step3_text'  => $ta( 'Schritt 3 – Text', 'Passt die Immobilie, verhandeln wir direkt mit dem Eigentümer bis zum notariellen Kaufvertrag.' ),
				'step4_title' => $t( 'Schritt 4 – Titel', 'Provision' ),
				'step4_text'  => $ta( 'Schritt 4 – Text', 'Nach erfolgreichem Ankauf erhalten Sie Ihre Tippgeber-Provision – unkompliziert und transparent.' ),
			),
			'Konditionen' => array(
				'terms_title' => $t( 'Überschrift', 'Konditionen' ),
				'terms_text'  => $ta( 'Text', "[Platzhalter – bitte durch die finalen Konditionen ersetzen]\nHöhe der Provision: … % des notariellen Kaufpreises (bzw. pauschal … €).\nFälligkeit: nach vollständiger Kaufpreiszahlung.\nVoraussetzung: Die Immobilie war uns zum Zeitpunkt Ihres Tipps noch nicht bekannt, und der Ankauf kommt innerhalb von 12 Monaten zustande.\nVor der Auszahlung schließen wir eine kurze schriftliche Tippgebervereinbarung.", 7 ),
			),
			'Vertrauen' => array(
				'trust1_title' => $t( 'Punkt 1 – Titel', 'Vertraulich' ),
				'trust1_text'  => $ta( 'Punkt 1 – Text', 'Ihre Angaben und die des Eigentümers behandeln wir diskret. Ihr Name wird ohne Ihr Einverständnis nicht genannt.' ),
				'trust2_title' => $t( 'Punkt 2 – Titel', 'Unverbindlich' ),
				'trust2_text'  => $ta( 'Punkt 2 – Text', 'Für Sie entstehen keinerlei Kosten oder Verpflichtungen – auch dann nicht, wenn kein Ankauf zustande kommt.' ),
				'trust3_title' => $t( 'Punkt 3 – Titel', 'Unkompliziert' ),
				'trust3_text'  => $ta( 'Punkt 3 – Text', 'Sie kennen den Eigentümer nicht persönlich? Auch eine Adresse und ein kurzer Hinweis können genügen.' ),
			),
			'Formular' => array(
				'form_title' => $t( 'Überschrift', 'Ihren Tipp abgeben' ),
				'form_text'  => $ta( 'Text', 'Füllen Sie aus, was Sie wissen – wir melden uns innerhalb von zwei Werktagen bei Ihnen.' ),
			),
		),

		'page-templates/verkaufen.php' => array(
			'Kopfbereich' => array(
				'hero_title' => $t( 'Überschrift', 'Ihre Immobilie direkt verkaufen' ),
				'hero_text'  => $ta( 'Einleitung', 'Sie möchten Ihre Wohnung oder Ihr Haus verkaufen? Wir kaufen direkt – ohne Makler, ohne Provision und mit einem klaren, schnellen Ablauf.' ),
				'hero_image' => $im( 'Bild', 'kueche' ),
			),
			'Vorteile' => array(
				'benefits_title' => $t( 'Überschrift', 'Warum an IMOVA verkaufen?' ),
				'benefit1_title' => $t( 'Vorteil 1 – Titel', 'Keine Maklerprovision' ),
				'benefit1_text'  => $ta( 'Vorteil 1 – Text', 'Sie verkaufen direkt an uns. Die übliche Provision von {provision} % entfällt vollständig.' ),
				'benefit2_title' => $t( 'Vorteil 2 – Titel', 'Schnell & unkompliziert' ),
				'benefit2_text'  => $ta( 'Vorteil 2 – Text', 'Nach der Besichtigung erhalten Sie zeitnah eine fundierte Einschätzung und ein konkretes Angebot.' ),
				'benefit3_title' => $t( 'Vorteil 3 – Titel', 'Finanzierung gesichert' ),
				'benefit3_text'  => $ta( 'Vorteil 3 – Text', 'Unsere Finanzierungspartner sind von Anfang an eingebunden und vermitteln direkt günstige Konditionen.' ),
				'benefit4_title' => $t( 'Vorteil 4 – Titel', 'Unterlagen direkt zum Berater' ),
				'benefit4_text'  => $ta( 'Vorteil 4 – Text', 'Ihre Objektunterlagen gehen ohne Umwege an den Finanzierungsberater – das spart Zeit und Rückfragen.' ),
			),
			'Ablauf' => array(
				'process_title' => $t( 'Überschrift', 'In vier Schritten zum Verkauf' ),
				'step1_title'   => $t( 'Schritt 1 – Titel', 'Anfrage senden' ),
				'step1_text'    => $ta( 'Schritt 1 – Text', 'Erzählen Sie uns kurz von Ihrer Immobilie – gern mit Grundriss oder Fotos.' ),
				'step2_title'   => $t( 'Schritt 2 – Titel', 'Besichtigung' ),
				'step2_text'    => $ta( 'Schritt 2 – Text', 'Wir sehen uns die Immobilie gemeinsam mit Ihnen vor Ort an.' ),
				'step3_title'   => $t( 'Schritt 3 – Titel', 'Angebot' ),
				'step3_text'    => $ta( 'Schritt 3 – Text', 'Sie erhalten ein transparentes Angebot – ohne versteckte Abzüge.' ),
				'step4_title'   => $t( 'Schritt 4 – Titel', 'Notartermin' ),
				'step4_text'    => $ta( 'Schritt 4 – Text', 'Wir kümmern uns um die Vorbereitung bis zur Beurkundung beim Notar.' ),
			),
			'Formular' => array(
				'form_title' => $t( 'Überschrift', 'Ihre Immobilie anbieten' ),
				'form_text'  => $ta( 'Text', 'Je mehr wir wissen, desto schneller können wir Ihnen eine Einschätzung geben. Alle Angaben sind unverbindlich.' ),
			),
		),

		'page-templates/kontakt.php' => array(
			'Kopfbereich' => array(
				'hero_title' => $t( 'Überschrift', 'Kontakt' ),
				'hero_text'  => $ta( 'Einleitung', 'Wir freuen uns auf Ihre Nachricht. In der Regel melden wir uns innerhalb eines Werktags persönlich bei Ihnen.' ),
			),
			'Termine' => array(
				'office_text' => $ta( 'Hinweis zu Terminen', 'Persönliche Termine vereinbaren wir direkt vor Ort in der Immobilie – oder ganz einfach telefonisch.' ),
			),
		),
	);
}

/**
 * Kontext einer Seite für die Feldkonfiguration.
 */
function imova_page_field_context( int $page_id ): string {
	if ( $page_id && (int) get_option( 'page_on_front' ) === $page_id ) {
		return 'front';
	}
	return (string) get_page_template_slug( $page_id );
}

function imova_page_field_defs( int $page_id ): array {
	$config = imova_page_field_config();
	$flat   = array();
	foreach ( $config[ imova_page_field_context( $page_id ) ] ?? array() as $fields ) {
		$flat += $fields;
	}
	return $flat;
}

/**
 * Feldwert der aktuellen Seite (oder Standardtext).
 */
function imova_field( string $key, ?int $page_id = null ): string {
	$page_id = $page_id ?? (int) get_queried_object_id();
	$defs    = imova_page_field_defs( $page_id );
	$value   = (string) get_post_meta( $page_id, '_imova_pf_' . $key, true );
	if ( '' === $value && 'image' !== ( $defs[ $key ]['type'] ?? '' ) ) {
		$value = (string) ( $defs[ $key ]['default'] ?? '' );
	}
	return str_replace( '{provision}', imova_opt( 'commission_rate' ), $value );
}

/**
 * Bild-URL eines Bildfelds (mit Platzhalter-Rückfall aus der Konfiguration).
 */
function imova_field_img( string $key, string $size = 'imova-hero', ?int $page_id = null ): string {
	$page_id = $page_id ?? (int) get_queried_object_id();
	$defs    = imova_page_field_defs( $page_id );
	return imova_img_url( get_post_meta( $page_id, '_imova_pf_' . $key, true ), $size, $defs[ $key ]['fallback'] ?? '' );
}

/**
 * Überschrift mit Zeilenumbrüchen ausgeben.
 */
function imova_field_lines( string $key ): string {
	return nl2br( esc_html( imova_field( $key ) ), false );
}

/* --------------------------------------------------------------------------
 * Meta-Box „Seiteninhalte“
 * ----------------------------------------------------------------------- */

add_action(
	'add_meta_boxes_page',
	function ( WP_Post $post ) {
		$config = imova_page_field_config();
		if ( isset( $config[ imova_page_field_context( $post->ID ) ] ) ) {
			add_meta_box( 'imova-page-fields', 'Seiteninhalte', 'imova_render_page_fields_box', 'page', 'normal', 'high' );
		}
	}
);

function imova_render_page_fields_box( WP_Post $post ): void {
	wp_nonce_field( 'imova_page_fields_save', 'imova_page_fields_nonce' );
	$config = imova_page_field_config()[ imova_page_field_context( $post->ID ) ];
	echo '<p class="description">Texte und Bilder dieser Seite. Ein geleertes Feld stellt den Standardtext wieder her. <code>{provision}</code> wird automatisch durch die Provision aus <em>Einstellungen → IMOVA</em> ersetzt.</p>';
	echo '<div class="imova-admin-fields">';
	foreach ( $config as $section => $fields ) {
		printf( '<details class="imova-admin-section" open><summary class="imova-admin-fields__title">%s</summary><div class="imova-admin-fields__grid">', esc_html( $section ) );
		foreach ( $fields as $key => $field ) {
			if ( isset( $field['default'] ) ) {
				$field['placeholder'] = $field['default'];
			}
			$name  = '_imova_pf_' . $key;
			$value = get_post_meta( $post->ID, $name, true );
			imova_admin_field( $name, $field, '' === $value ? ( $field['default'] ?? '' ) : $value );
		}
		echo '</div></details>';
	}
	echo '</div>';
}

add_action(
	'save_post_page',
	function ( int $post_id ) {
		if ( ! isset( $_POST['imova_page_fields_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['imova_page_fields_nonce'] ), 'imova_page_fields_save' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		foreach ( imova_page_field_defs( $post_id ) as $key => $field ) {
			$name = '_imova_pf_' . $key;
			$raw  = isset( $_POST[ $name ] ) ? wp_unslash( $_POST[ $name ] ) : '';
			$value = match ( $field['type'] ) {
				'image'    => (string) absint( $raw ),
				'textarea' => sanitize_textarea_field( $raw ),
				default    => sanitize_text_field( $raw ),
			};
			if ( '' === $value || '0' === $value || $value === ( $field['default'] ?? null ) ) {
				delete_post_meta( $post_id, $name );
			} else {
				update_post_meta( $post_id, $name, $value );
			}
		}
	}
);
