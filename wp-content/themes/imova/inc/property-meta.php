<?php
/**
 * Objektdaten für Immobilien: Felder, Meta-Box, Galerie, Exposé, Admin-Spalten.
 *
 * @package imova
 */

defined( 'ABSPATH' ) || exit;

function imova_property_statuses(): array {
	return array(
		'verfuegbar' => 'Verfügbar',
		'demnaechst' => 'Demnächst',
		'reserviert' => 'Reserviert',
		'verkauft'   => 'Verkauft',
	);
}

/**
 * Felddefinitionen, gruppiert nach Abschnitt der Meta-Box.
 * type: text | number | select | textarea | checkbox
 */
function imova_property_fields(): array {
	return array(
		'Vermarktung' => array(
			'status'         => array( 'label' => 'Status', 'type' => 'select', 'options' => imova_property_statuses() ),
			'price'          => array( 'label' => 'Kaufpreis (€)', 'type' => 'number', 'step' => '1000', 'help' => 'Leer lassen für „Preis auf Anfrage“.' ),
			'price_note'     => array( 'label' => 'Hinweis zum Preis', 'type' => 'text', 'placeholder' => 'z. B. zzgl. Tiefgaragenstellplatz 25.000 €' ),
			'available_from' => array( 'label' => 'Verfügbar ab', 'type' => 'text', 'placeholder' => 'z. B. sofort, nach Vereinbarung' ),
		),
		'Eckdaten' => array(
			'property_type' => array(
				'label'   => 'Objektart',
				'type'    => 'select',
				'options' => array_combine(
					$t = array( 'Etagenwohnung', 'Erdgeschosswohnung', 'Dachgeschosswohnung', 'Maisonette', 'Penthouse', 'Apartment', 'Einfamilienhaus', 'Doppelhaushälfte', 'Reihenhaus', 'Mehrfamilienhaus', 'Gewerbeeinheit', 'Grundstück' ),
					$t
				),
			),
			'rooms'         => array( 'label' => 'Zimmer', 'type' => 'number', 'step' => '0.5' ),
			'area'          => array( 'label' => 'Wohnfläche (m²)', 'type' => 'number', 'step' => '0.1' ),
			'floor'         => array( 'label' => 'Etage', 'type' => 'text', 'placeholder' => 'z. B. 2. OG von 4' ),
			'year_built'    => array( 'label' => 'Baujahr', 'type' => 'number', 'step' => '1' ),
			'modernized'    => array( 'label' => 'Modernisierung', 'type' => 'text', 'placeholder' => 'z. B. 2025 kernsaniert' ),
			'condition'     => array(
				'label'   => 'Zustand',
				'type'    => 'select',
				'options' => array_combine(
					$c = array( 'Erstbezug nach Sanierung', 'Saniert', 'Modernisiert', 'Neuwertig', 'Gepflegt', 'Renovierungsbedürftig', 'In Planung' ),
					$c
				),
			),
			'heating'       => array( 'label' => 'Heizungsart', 'type' => 'text', 'placeholder' => 'z. B. Zentralheizung' ),
		),
		'Lage' => array(
			'street'       => array( 'label' => 'Straße & Hausnummer', 'type' => 'text' ),
			'zip'          => array( 'label' => 'PLZ', 'type' => 'text' ),
			'city'         => array( 'label' => 'Ort / Stadtteil', 'type' => 'text', 'help' => 'Für die Filterung zusätzlich rechts einen „Standort“ wählen.' ),
			'show_address' => array( 'label' => 'Genaue Adresse & Karte öffentlich anzeigen', 'type' => 'checkbox' ),
		),
		'Energieausweis (Pflichtangaben nach GEG)' => array(
			'energy_cert'    => array(
				'label'   => 'Art des Energieausweises',
				'type'    => 'select',
				'options' => array_combine(
					$e = array( 'Bedarfsausweis', 'Verbrauchsausweis', 'Liegt zur Besichtigung vor', 'In Erstellung', 'Nicht erforderlich (Denkmalschutz)' ),
					$e
				),
			),
			'energy_value'   => array( 'label' => 'Endenergie (kWh/(m²·a))', 'type' => 'number', 'step' => '0.1' ),
			'energy_class'   => array(
				'label'   => 'Energieeffizienzklasse',
				'type'    => 'select',
				'options' => array_combine( $k = array( 'A+', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H' ), $k ),
			),
			'energy_carrier' => array( 'label' => 'Wesentlicher Energieträger', 'type' => 'text', 'placeholder' => 'z. B. Fernwärme' ),
		),
		'Texte' => array(
			'features' => array( 'label' => 'Ausstattungsmerkmale', 'type' => 'textarea', 'help' => 'Ein Merkmal pro Zeile, z. B. „Echtholzparkett“.' ),
			'teaser'   => array( 'label' => 'Teaser für „Demnächst“', 'type' => 'textarea', 'help' => 'Wird angezeigt, solange das Objekt den Status „Demnächst“ hat.' ),
		),
	);
}

/**
 * Objektwert lesen.
 */
function imova_prop( string $key, $post_id = null ): string {
	$post_id = $post_id ?: get_the_ID();
	return (string) get_post_meta( (int) $post_id, '_imova_' . $key, true );
}

function imova_prop_status( $post_id = null ): string {
	$status = imova_prop( 'status', $post_id );
	return isset( imova_property_statuses()[ $status ] ) ? $status : 'verfuegbar';
}

function imova_prop_status_label( $post_id = null ): string {
	return imova_property_statuses()[ imova_prop_status( $post_id ) ];
}

function imova_prop_price_label( $post_id = null ): string {
	$status = imova_prop_status( $post_id );
	if ( 'verkauft' === $status ) {
		return 'Verkauft';
	}
	$price = imova_format_price( imova_prop( 'price', $post_id ) );
	if ( $price ) {
		return $price;
	}
	return 'demnaechst' === $status ? 'Preis folgt' : 'Auf Anfrage';
}

/**
 * Ortsangabe für Karten und Kopfzeilen: Ort-Feld, sonst erster Standort.
 */
function imova_prop_location( $post_id = null ): string {
	$post_id = $post_id ?: get_the_ID();
	$city    = imova_prop( 'city', $post_id );
	if ( $city ) {
		return $city;
	}
	$terms = get_the_terms( $post_id, 'standort' );
	return ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
}

function imova_prop_gallery_ids( $post_id = null ): array {
	$ids = array_filter( array_map( 'absint', explode( ',', imova_prop( 'gallery', $post_id ) ) ) );
	$thumb = get_post_thumbnail_id( $post_id ?: get_the_ID() );
	if ( $thumb ) {
		$ids = array_values( array_unique( array_merge( array( (int) $thumb ), $ids ) ) );
	}
	return $ids;
}

/* --------------------------------------------------------------------------
 * Meta-Box
 * ----------------------------------------------------------------------- */

add_action(
	'add_meta_boxes_immobilie',
	function () {
		add_meta_box( 'imova-property', 'Objektdaten', 'imova_render_property_box', 'immobilie', 'normal', 'high' );
		add_meta_box( 'imova-property-media', 'Bildergalerie & Exposé', 'imova_render_property_media_box', 'immobilie', 'normal', 'high' );
	}
);

function imova_render_property_box( WP_Post $post ): void {
	wp_nonce_field( 'imova_property_save', 'imova_property_nonce' );
	echo '<div class="imova-admin-fields">';
	foreach ( imova_property_fields() as $section => $fields ) {
		printf( '<h3 class="imova-admin-fields__title">%s</h3><div class="imova-admin-fields__grid">', esc_html( $section ) );
		foreach ( $fields as $key => $field ) {
			imova_admin_field( '_imova_' . $key, $field, get_post_meta( $post->ID, '_imova_' . $key, true ) );
		}
		echo '</div>';
	}
	echo '</div>';
}

/**
 * Gemeinsamer Feld-Renderer für Objekt- und Seitenfelder.
 */
function imova_admin_field( string $name, array $field, $value ): void {
	$id    = 'f' . md5( $name );
	$type  = $field['type'];
	$wide  = in_array( $type, array( 'textarea', 'image' ), true ) ? ' is-wide' : '';
	$label = esc_html( $field['label'] );
	echo '<div class="imova-admin-field' . esc_attr( $wide ) . '">';

	switch ( $type ) {
		case 'checkbox':
			printf( '<label><input type="checkbox" name="%s" value="1" %s> %s</label>', esc_attr( $name ), checked( $value, '1', false ), $label );
			break;
		case 'select':
			printf( '<label for="%s">%s</label><select id="%1$s" name="%s"><option value="">– bitte wählen –</option>', esc_attr( $id ), $label, esc_attr( $name ) );
			foreach ( $field['options'] as $opt_value => $opt_label ) {
				printf( '<option value="%s" %s>%s</option>', esc_attr( $opt_value ), selected( $value, $opt_value, false ), esc_html( $opt_label ) );
			}
			echo '</select>';
			break;
		case 'textarea':
			printf( '<label for="%s">%s</label><textarea id="%1$s" name="%s" rows="%d" placeholder="%s">%s</textarea>', esc_attr( $id ), $label, esc_attr( $name ), (int) ( $field['rows'] ?? 4 ), esc_attr( $field['placeholder'] ?? '' ), esc_textarea( (string) $value ) );
			break;
		case 'image':
			$src = $value ? wp_get_attachment_image_url( (int) $value, 'medium' ) : '';
			printf(
				'<label>%s</label><div class="imova-image-field" data-imova-image><input type="hidden" name="%s" value="%s"><div class="imova-image-field__preview">%s</div><button type="button" class="button" data-imova-image-select>Bild wählen</button> <button type="button" class="button-link-delete" data-imova-image-remove %s>Entfernen</button></div>',
				$label,
				esc_attr( $name ),
				esc_attr( (string) $value ),
				$src ? '<img src="' . esc_url( $src ) . '" alt="">' : '',
				$value ? '' : 'hidden'
			);
			break;
		default:
			printf(
				'<label for="%s">%s</label><input id="%1$s" type="%s" name="%s" value="%s" placeholder="%s" %s>',
				esc_attr( $id ),
				$label,
				'number' === $type ? 'number' : 'text',
				esc_attr( $name ),
				esc_attr( (string) $value ),
				esc_attr( $field['placeholder'] ?? '' ),
				isset( $field['step'] ) ? 'step="' . esc_attr( $field['step'] ) . '" min="0"' : ''
			);
	}

	if ( ! empty( $field['help'] ) ) {
		printf( '<p class="description">%s</p>', esc_html( $field['help'] ) );
	}
	echo '</div>';
}

function imova_render_property_media_box( WP_Post $post ): void {
	$ids    = array_filter( array_map( 'absint', explode( ',', (string) get_post_meta( $post->ID, '_imova_gallery', true ) ) ) );
	$expose = (int) get_post_meta( $post->ID, '_imova_expose', true );
	?>
	<p class="description">Das <strong>Titelbild</strong> (rechte Spalte) erscheint zuerst. Weitere Bilder hier hinzufügen und per Drag & Drop sortieren.</p>
	<div class="imova-gallery" data-imova-gallery>
		<input type="hidden" name="_imova_gallery" value="<?php echo esc_attr( implode( ',', $ids ) ); ?>">
		<ul class="imova-gallery__list">
			<?php foreach ( $ids as $id ) : ?>
				<li data-id="<?php echo esc_attr( $id ); ?>"><?php echo wp_get_attachment_image( $id, 'thumbnail' ); ?><button type="button" class="imova-gallery__remove" aria-label="Bild entfernen">×</button></li>
			<?php endforeach; ?>
		</ul>
		<button type="button" class="button button-secondary" data-imova-gallery-add>Bilder hinzufügen</button>
	</div>

	<h3 class="imova-admin-fields__title">Exposé (PDF)</h3>
	<div class="imova-file-field" data-imova-file>
		<input type="hidden" name="_imova_expose" value="<?php echo esc_attr( $expose ?: '' ); ?>">
		<span class="imova-file-field__name"><?php echo $expose ? esc_html( basename( (string) get_attached_file( $expose ) ) ) : 'Kein Exposé hinterlegt'; ?></span>
		<button type="button" class="button" data-imova-file-select>PDF wählen</button>
		<button type="button" class="button-link-delete" data-imova-file-remove <?php echo $expose ? '' : 'hidden'; ?>>Entfernen</button>
	</div>
	<?php
}

add_action(
	'save_post_immobilie',
	function ( int $post_id ) {
		if ( ! isset( $_POST['imova_property_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['imova_property_nonce'] ), 'imova_property_save' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		foreach ( imova_property_fields() as $fields ) {
			foreach ( $fields as $key => $field ) {
				$name = '_imova_' . $key;
				$raw  = isset( $_POST[ $name ] ) ? wp_unslash( $_POST[ $name ] ) : '';
				switch ( $field['type'] ) {
					case 'checkbox':
						$value = $raw ? '1' : '';
						break;
					case 'number':
						$value = '' === $raw ? '' : (string) (float) str_replace( ',', '.', (string) $raw );
						break;
					case 'select':
						$value = array_key_exists( $raw, $field['options'] ) ? $raw : '';
						break;
					case 'textarea':
						$value = sanitize_textarea_field( $raw );
						break;
					default:
						$value = sanitize_text_field( $raw );
				}
				if ( '' === $value ) {
					delete_post_meta( $post_id, $name );
				} else {
					update_post_meta( $post_id, $name, $value );
				}
			}
		}

		// Status immer setzen, damit Filter & Sortierung zuverlässig greifen.
		if ( ! get_post_meta( $post_id, '_imova_status', true ) ) {
			update_post_meta( $post_id, '_imova_status', 'verfuegbar' );
		}

		$gallery = isset( $_POST['_imova_gallery'] ) ? implode( ',', array_filter( array_map( 'absint', explode( ',', wp_unslash( $_POST['_imova_gallery'] ) ) ) ) ) : '';
		update_post_meta( $post_id, '_imova_gallery', $gallery );
		update_post_meta( $post_id, '_imova_expose', isset( $_POST['_imova_expose'] ) ? absint( $_POST['_imova_expose'] ) : 0 );
	}
);

/* --------------------------------------------------------------------------
 * Admin-Spalten & Assets
 * ----------------------------------------------------------------------- */

add_filter(
	'manage_immobilie_posts_columns',
	function ( array $columns ) {
		$new = array();
		foreach ( $columns as $key => $label ) {
			if ( 'title' === $key ) {
				$new['imova_thumb'] = '';
			}
			$new[ $key ] = $label;
			if ( 'title' === $key ) {
				$new['imova_status'] = 'Status';
				$new['imova_price']  = 'Preis';
				$new['imova_facts']  = 'Zimmer / Fläche';
			}
		}
		return $new;
	}
);

add_action(
	'manage_immobilie_posts_custom_column',
	function ( string $column, int $post_id ) {
		switch ( $column ) {
			case 'imova_thumb':
				echo get_the_post_thumbnail( $post_id, array( 72, 52 ) ) ?: '<span class="imova-thumb-empty"></span>';
				break;
			case 'imova_status':
				printf( '<span class="imova-badge imova-badge--%s">%s</span>', esc_attr( imova_prop_status( $post_id ) ), esc_html( imova_prop_status_label( $post_id ) ) );
				break;
			case 'imova_price':
				echo esc_html( imova_prop_price_label( $post_id ) );
				break;
			case 'imova_facts':
				$rooms = imova_format_number( imova_prop( 'rooms', $post_id ) );
				$area  = imova_format_number( imova_prop( 'area', $post_id ) );
				echo esc_html( trim( ( $rooms ? $rooms . ' Zi.' : '' ) . ( $rooms && $area ? ' · ' : '' ) . ( $area ? $area . ' m²' : '' ) ) ?: '–' );
				break;
		}
	},
	10,
	2
);

add_action(
	'admin_enqueue_scripts',
	function ( string $hook ) {
		wp_enqueue_style( 'imova-admin', IMOVA_URI . '/assets/css/admin.css', array(), IMOVA_VERSION . '.' . filemtime( IMOVA_DIR . '/assets/css/admin.css' ) );

		$screen = get_current_screen();
		if ( $screen && in_array( $screen->post_type, array( 'immobilie', 'page' ), true ) && in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			wp_enqueue_media();
			wp_enqueue_script( 'imova-admin', IMOVA_URI . '/assets/js/admin.js', array( 'jquery', 'jquery-ui-sortable' ), IMOVA_VERSION . '.' . filemtime( IMOVA_DIR . '/assets/js/admin.js' ), true );
		}
	}
);
