<?php
/**
 * Formulare: Kontakt, Interesse an einer Immobilie, Tippgeber, Verkauf.
 * Einsendungen werden als „Anfrage“ gespeichert und per E-Mail gemeldet.
 * Spam-Schutz: Honeypot, signierter Zeitstempel (cache-freundlich, ohne Nonce) und Rate-Limit.
 *
 * @package imova
 */

defined( 'ABSPATH' ) || exit;

const IMOVA_UPLOAD_MAX_FILES = 5;
const IMOVA_UPLOAD_MAX_BYTES = 10 * MB_IN_BYTES;

function imova_form_types(): array {
	return array(
		'kontakt'   => 'Kontakt',
		'interesse' => 'Interesse an Immobilie',
		'tipp'      => 'Tippgeber',
		'verkauf'   => 'Verkaufsangebot',
	);
}

function imova_forms(): array {
	$name    = array( 'label' => 'Vor- und Nachname', 'type' => 'text', 'required' => true, 'autocomplete' => 'name' );
	$email   = array( 'label' => 'E-Mail', 'type' => 'email', 'required' => true, 'autocomplete' => 'email', 'width' => 'half' );
	$phone   = array( 'label' => 'Telefon', 'type' => 'tel', 'autocomplete' => 'tel', 'width' => 'half' );
	$types   = array( 'Wohnung', 'Haus', 'Mehrfamilienhaus', 'Gewerbe', 'Grundstück', 'Sonstiges' );

	return array(
		'kontakt' => array(
			'submit'  => 'Nachricht senden',
			'success' => 'Vielen Dank für Ihre Nachricht! Wir melden uns in der Regel innerhalb eines Werktags bei Ihnen.',
			'fields'  => array(
				'name'    => $name,
				'email'   => $email,
				'phone'   => $phone,
				'subject' => array(
					'label'   => 'Ihr Anliegen',
					'type'    => 'select',
					'options' => array( 'Allgemeine Anfrage', 'Interesse an einer Immobilie', 'Immobilie verkaufen', 'Tippgeber', 'Sonstiges' ),
				),
				'message' => array( 'label' => 'Nachricht', 'type' => 'textarea', 'required' => true, 'rows' => 6 ),
			),
		),

		'interesse' => array(
			'submit'  => 'Interesse vormerken',
			'success' => 'Vielen Dank! Wir haben Sie vorgemerkt und informieren Sie, sobald Bilder und Details verfügbar sind.',
			'fields'  => array(
				'name'     => $name,
				'email'    => $email,
				'phone'    => $phone,
				'message'  => array( 'label' => 'Nachricht (optional)', 'type' => 'textarea', 'rows' => 4 ),
				'property' => array( 'label' => 'Immobilie', 'type' => 'hidden' ),
			),
		),

		'tipp' => array(
			'submit'  => 'Tipp absenden',
			'success' => 'Vielen Dank für Ihren Tipp! Wir prüfen die Immobilie und melden uns innerhalb von zwei Werktagen bei Ihnen.',
			'fields'  => array(
				'name'             => $name + array( 'section' => 'Ihre Kontaktdaten' ),
				'email'            => $email,
				'phone'            => array( 'required' => true ) + $phone,
				'property_address' => array( 'label' => 'Adresse bzw. Lage der Immobilie', 'type' => 'text', 'required' => true, 'section' => 'Die Immobilie', 'placeholder' => 'Straße, PLZ Ort – oder so genau wie bekannt' ),
				'property_type'    => array( 'label' => 'Art der Immobilie', 'type' => 'select', 'options' => $types, 'width' => 'half' ),
				'owner_aware'      => array( 'label' => 'Weiß der Eigentümer von Ihrem Tipp?', 'type' => 'select', 'options' => array( 'Ja', 'Nein', 'Weiß nicht' ), 'width' => 'half' ),
				'owner_name'       => array( 'label' => 'Name des Eigentümers (falls bekannt)', 'type' => 'text', 'width' => 'half', 'section' => 'Eigentümer (optional)' ),
				'owner_contact'    => array( 'label' => 'Kontakt des Eigentümers (falls bekannt)', 'type' => 'text', 'width' => 'half', 'placeholder' => 'Telefon oder E-Mail' ),
				'message'          => array( 'label' => 'Weitere Hinweise', 'type' => 'textarea', 'rows' => 4, 'placeholder' => 'z. B. Größe, Zustand, Verkaufsgrund, Zeitrahmen …' ),
			),
		),

		'verkauf' => array(
			'submit'  => 'Immobilie anbieten',
			'success' => 'Vielen Dank! Wir sehen uns Ihre Angaben an und melden uns kurzfristig für ein persönliches Gespräch.',
			'fields'  => array(
				'name'             => $name + array( 'section' => 'Ihre Kontaktdaten' ),
				'email'            => $email,
				'phone'            => array( 'required' => true ) + $phone,
				'property_address' => array( 'label' => 'Adresse der Immobilie', 'type' => 'text', 'required' => true, 'section' => 'Ihre Immobilie', 'autocomplete' => 'street-address' ),
				'property_type'    => array( 'label' => 'Art der Immobilie', 'type' => 'select', 'required' => true, 'options' => $types, 'width' => 'third' ),
				'area'             => array( 'label' => 'Wohnfläche ca. (m²)', 'type' => 'number', 'width' => 'third', 'step' => '1' ),
				'rooms'            => array( 'label' => 'Zimmer', 'type' => 'number', 'width' => 'third', 'step' => '0.5' ),
				'price_mode'       => array( 'label' => 'Preis', 'type' => 'radio', 'required' => true, 'options' => array( 'Ich habe eine Preisvorstellung', 'Ich wünsche eine Bewertung' ) ),
				'price'            => array( 'label' => 'Preisvorstellung (€)', 'type' => 'number', 'step' => '1000', 'depends' => 'price_mode=Ich habe eine Preisvorstellung' ),
				'message'          => array( 'label' => 'Nachricht', 'type' => 'textarea', 'rows' => 4, 'placeholder' => 'z. B. Baujahr, Zustand, vermietet oder frei, gewünschter Zeitrahmen …' ),
				'files'            => array( 'label' => 'Unterlagen (optional)', 'type' => 'file', 'help' => 'Grundriss, Fotos oder Exposé – bis zu 5 Dateien (PDF, JPG, PNG), je max. 10 MB.' ),
			),
		),
	);
}

/* --------------------------------------------------------------------------
 * Ausgabe
 * ----------------------------------------------------------------------- */

/**
 * Gibt ein Formular aus. $args: property_id (int), id (string).
 */
function imova_render_form( string $key, array $args = array() ): void {
	$forms = imova_forms();
	if ( ! isset( $forms[ $key ] ) ) {
		return;
	}
	$form    = array_merge( $forms[ $key ], array_intersect_key( $args, array_flip( array( 'submit', 'success' ) ) ) );
	$form_id = $args['id'] ?? 'formular';

	// Erfolg?
	if ( imova_filter_value( 'imova_ok' ) === $key ) {
		printf(
			'<div class="form-success" id="%s" role="status" tabindex="-1">%s<p>%s</p></div>',
			esc_attr( $form_id ),
			imova_icon( 'check', 28 ), // phpcs:ignore
			esc_html( $form['success'] )
		);
		return;
	}

	// Fehler aus vorheriger Absendung.
	$state  = array( 'errors' => array(), 'old' => array() );
	$token  = imova_filter_value( 'imova_t' );
	if ( $token && imova_filter_value( 'imova_form' ) === $key ) {
		$state = wp_parse_args( (array) get_transient( 'imova_form_' . $token ), $state );
	}
	$errors = $state['errors'];
	$old    = $state['old'];

	$has_file = (bool) array_filter( $form['fields'], fn( $f ) => 'file' === $f['type'] );
	?>
	<form class="form" id="<?php echo esc_attr( $form_id ); ?>" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" <?php echo $has_file ? 'enctype="multipart/form-data"' : ''; ?> novalidate data-imova-form>
		<input type="hidden" name="action" value="imova_form">
		<input type="hidden" name="imova_form" value="<?php echo esc_attr( $key ); ?>">
		<input type="hidden" name="imova_return" value="<?php echo esc_attr( imova_current_url() ); ?>">
		<input type="hidden" name="imova_anchor" value="<?php echo esc_attr( $form_id ); ?>">
		<input type="hidden" name="imova_ts" value="<?php echo esc_attr( imova_form_timestamp() ); ?>">
		<div class="form__hp" aria-hidden="true"><label>Website <input type="text" name="imova_website" tabindex="-1" autocomplete="off"></label></div>

		<?php if ( $errors ) : ?>
			<div class="form-errors" role="alert" tabindex="-1">
				<p><strong>Bitte prüfen Sie Ihre Angaben:</strong></p>
				<ul>
					<?php foreach ( $errors as $message ) : ?>
						<li><?php echo esc_html( $message ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<div class="form__grid">
			<?php
			foreach ( $form['fields'] as $name => $field ) {
				$value = $old[ $name ] ?? '';
				if ( 'property' === $name && ! empty( $args['property_id'] ) ) {
					$value = (string) (int) $args['property_id'];
				}
				imova_render_form_field( $key, $name, $field, (string) $value, $errors[ $name ] ?? '' );
			}
			?>

			<div class="field field--full field--consent<?php echo isset( $errors['consent'] ) ? ' has-error' : ''; ?>">
				<label class="check">
					<input type="checkbox" name="consent" value="1" required <?php checked( ! empty( $old['consent'] ) ); ?>>
					<span>Ich habe die <a href="<?php echo esc_url( (string) get_privacy_policy_url() ?: home_url( '/datenschutz/' ) ); ?>" target="_blank">Datenschutzerklärung</a> gelesen und bin mit der Verarbeitung meiner Angaben zur Bearbeitung meiner Anfrage einverstanden. *</span>
				</label>
			</div>
		</div>

		<div class="form__footer">
			<button type="submit" class="btn btn--dark"><?php echo esc_html( $form['submit'] ); ?> <?php echo imova_icon( 'arrow', 18 ); // phpcs:ignore ?></button>
			<p class="form__note"><?php echo imova_icon( 'lock', 14 ); // phpcs:ignore ?> Ihre Angaben werden vertraulich behandelt. * Pflichtfeld</p>
		</div>
	</form>
	<?php
}

function imova_render_form_field( string $form, string $name, array $field, string $value, string $error ): void {
	$id       = 'f-' . $form . '-' . $name;
	$required = ! empty( $field['required'] );
	$label    = esc_html( $field['label'] ) . ( $required ? ' *' : '' );
	$width    = $field['width'] ?? 'full';
	$attrs    = $required ? ' required' : '';
	$attrs   .= ! empty( $field['autocomplete'] ) ? ' autocomplete="' . esc_attr( $field['autocomplete'] ) . '"' : '';
	$attrs   .= ! empty( $field['placeholder'] ) ? ' placeholder="' . esc_attr( $field['placeholder'] ) . '"' : '';
	$attrs   .= $error ? ' aria-invalid="true" aria-describedby="' . esc_attr( $id ) . '-err"' : '';

	if ( 'hidden' === $field['type'] ) {
		printf( '<input type="hidden" name="%s" value="%s">', esc_attr( $name ), esc_attr( $value ) );
		return;
	}

	if ( ! empty( $field['section'] ) ) {
		printf( '<p class="form__section">%s</p>', esc_html( $field['section'] ) );
	}

	$depends = '';
	if ( ! empty( $field['depends'] ) ) {
		[ $dep_name, $dep_value ] = explode( '=', $field['depends'], 2 );
		$depends = sprintf( ' data-depends="%s" data-depends-value="%s"', esc_attr( $dep_name ), esc_attr( $dep_value ) );
	}

	printf( '<div class="field field--%s%s"%s>', esc_attr( $width ), $error ? ' has-error' : '', $depends ); // phpcs:ignore

	switch ( $field['type'] ) {
		case 'textarea':
			printf( '<label for="%s">%s</label><textarea id="%1$s" name="%s" rows="%d"%s>%s</textarea>', esc_attr( $id ), $label, esc_attr( $name ), (int) ( $field['rows'] ?? 5 ), $attrs, esc_textarea( $value ) ); // phpcs:ignore
			break;

		case 'select':
			printf( '<label for="%s">%s</label><div class="select"><select id="%1$s" name="%s"%s><option value="">Bitte wählen</option>', esc_attr( $id ), $label, esc_attr( $name ), $attrs ); // phpcs:ignore
			foreach ( $field['options'] as $option ) {
				printf( '<option %s>%s</option>', selected( $value, $option, false ), esc_html( $option ) );
			}
			echo '</select>' . imova_icon( 'chevron', 16 ) . '</div>'; // phpcs:ignore
			break;

		case 'radio':
			printf( '<fieldset><legend>%s</legend><div class="radio-group">', $label ); // phpcs:ignore
			foreach ( $field['options'] as $i => $option ) {
				printf(
					'<label class="radio-card"><input type="radio" name="%s" value="%s" %s%s><span>%s</span></label>',
					esc_attr( $name ),
					esc_attr( $option ),
					checked( $value, $option, false ),
					0 === $i && $required ? ' required' : '',
					esc_html( $option )
				);
			}
			echo '</div></fieldset>';
			break;

		case 'file':
			printf(
				'<span class="label">%s</span><label class="file-drop" for="%s">%s<span class="file-drop__text" data-file-label>Dateien auswählen oder hierher ziehen</span><input id="%2$s" type="file" name="%s[]" multiple accept=".pdf,.jpg,.jpeg,.png,.webp,.heic,application/pdf,image/*"></label>',
				$label, // phpcs:ignore
				esc_attr( $id ),
				imova_icon( 'upload', 22 ), // phpcs:ignore
				esc_attr( $name )
			);
			break;

		default:
			$type = in_array( $field['type'], array( 'email', 'tel', 'number' ), true ) ? $field['type'] : 'text';
			if ( 'number' === $type ) {
				$attrs .= ' min="0" step="' . esc_attr( $field['step'] ?? '1' ) . '" inputmode="decimal"';
			}
			printf( '<label for="%s">%s</label><input id="%1$s" type="%s" name="%s" value="%s"%s>', esc_attr( $id ), $label, $type, esc_attr( $name ), esc_attr( $value ), $attrs ); // phpcs:ignore
	}

	if ( ! empty( $field['help'] ) ) {
		printf( '<p class="field__help">%s</p>', esc_html( $field['help'] ) );
	}
	if ( $error ) {
		printf( '<p class="field__error" id="%s-err">%s</p>', esc_attr( $id ), esc_html( $error ) );
	}
	echo '</div>';
}

function imova_current_url(): string {
	$path = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';
	return remove_query_arg( array( 'imova_ok', 'imova_t', 'imova_form' ), home_url( $path ) );
}

function imova_form_timestamp(): string {
	$time = (string) time();
	return $time . '.' . substr( wp_hash( 'imova_form|' . $time ), 0, 16 );
}

/* --------------------------------------------------------------------------
 * Verarbeitung
 * ----------------------------------------------------------------------- */

add_action( 'admin_post_nopriv_imova_form', 'imova_handle_form' );
add_action( 'admin_post_imova_form', 'imova_handle_form' );

function imova_handle_form(): void {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- Schutz über signierten Zeitstempel & Honeypot (seitencache-tauglich).
	$key    = isset( $_POST['imova_form'] ) ? sanitize_key( $_POST['imova_form'] ) : '';
	$forms  = imova_forms();
	$return = isset( $_POST['imova_return'] ) ? wp_validate_redirect( esc_url_raw( wp_unslash( $_POST['imova_return'] ) ), home_url( '/' ) ) : home_url( '/' );
	$anchor = isset( $_POST['imova_anchor'] ) ? sanitize_html_class( wp_unslash( $_POST['imova_anchor'] ) ) : 'formular';

	if ( ! isset( $forms[ $key ] ) ) {
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}

	$is_bot = ! empty( $_POST['imova_website'] ) || ! imova_check_timestamp( isset( $_POST['imova_ts'] ) ? sanitize_text_field( wp_unslash( $_POST['imova_ts'] ) ) : '' );
	if ( $is_bot ) {
		// Bots bekommen scheinbar eine Erfolgsmeldung, damit sie nicht weiter probieren.
		wp_safe_redirect( add_query_arg( 'imova_ok', $key, $return ) . '#' . $anchor );
		exit;
	}

	$fields = $forms[ $key ]['fields'];
	$values = array();
	$errors = array();

	foreach ( $fields as $name => $field ) {
		if ( 'file' === $field['type'] ) {
			continue;
		}
		$raw = isset( $_POST[ $name ] ) ? wp_unslash( $_POST[ $name ] ) : '';
		$raw = is_string( $raw ) ? $raw : '';

		$value = match ( $field['type'] ) {
			'email'    => sanitize_email( $raw ),
			'textarea' => sanitize_textarea_field( $raw ),
			'number', 'hidden' => preg_replace( '/[^0-9.,]/', '', $raw ),
			default    => sanitize_text_field( $raw ),
		};
		if ( in_array( $field['type'], array( 'select', 'radio' ), true ) && '' !== $value && ! in_array( $value, $field['options'], true ) ) {
			$value = '';
		}
		$values[ $name ] = mb_substr( (string) $value, 0, 'textarea' === $field['type'] ? 5000 : 300 );

		if ( ! empty( $field['required'] ) && '' === $values[ $name ] ) {
			$errors[ $name ] = sprintf( 'Bitte füllen Sie „%s“ aus.', $field['label'] );
		} elseif ( 'email' === $field['type'] && '' !== $raw && ! is_email( $values[ $name ] ) ) {
			$errors[ $name ] = 'Bitte geben Sie eine gültige E-Mail-Adresse an.';
		}
	}

	if ( empty( $_POST['consent'] ) ) {
		$errors['consent'] = 'Bitte bestätigen Sie die Datenschutzhinweise.';
	}

	$files = array();
	if ( ! $errors ) {
		foreach ( $fields as $name => $field ) {
			if ( 'file' === $field['type'] && ! empty( $_FILES[ $name ]['name'][0] ) ) {
				$result = imova_store_uploads( $_FILES[ $name ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
				if ( is_wp_error( $result ) ) {
					$errors[ $name ] = $result->get_error_message();
				} else {
					$files = $result;
				}
			}
		}
	}

	if ( ! $errors && ! imova_rate_limit_ok() ) {
		$errors['_'] = 'Sie haben in kurzer Zeit sehr viele Anfragen gesendet. Bitte versuchen Sie es in einigen Minuten erneut oder rufen Sie uns an.';
	}

	if ( $errors ) {
		$token = wp_generate_password( 20, false );
		set_transient( 'imova_form_' . $token, array( 'errors' => $errors, 'old' => $values + array( 'consent' => ! empty( $_POST['consent'] ) ) ), 15 * MINUTE_IN_SECONDS );
		wp_safe_redirect( add_query_arg( array( 'imova_form' => $key, 'imova_t' => $token ), $return ) . '#' . $anchor );
		exit;
	}
	// phpcs:enable

	imova_save_submission( $key, $values, $files );

	wp_safe_redirect( add_query_arg( 'imova_ok', $key, $return ) . '#' . $anchor );
	exit;
}

function imova_check_timestamp( string $value ): bool {
	$parts = explode( '.', $value );
	if ( 2 !== count( $parts ) || ! ctype_digit( $parts[0] ) ) {
		return false;
	}
	if ( ! hash_equals( substr( wp_hash( 'imova_form|' . $parts[0] ), 0, 16 ), $parts[1] ) ) {
		return false;
	}
	return ( time() - (int) $parts[0] ) >= 3; // Menschen brauchen länger als 3 Sekunden.
}

function imova_rate_limit_ok(): bool {
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key = 'imova_rl_' . substr( wp_hash( $ip ), 0, 20 ); // IP wird nur gehasht und kurzzeitig verwendet.
	$count = (int) get_transient( $key );
	if ( $count >= 6 ) {
		return false;
	}
	set_transient( $key, $count + 1, 10 * MINUTE_IN_SECONDS );
	return true;
}

/**
 * Verzeichnis für vertrauliche Uploads (nicht öffentlich abrufbar).
 */
function imova_private_dir(): string {
	$dir = trailingslashit( wp_upload_dir()['basedir'] ) . 'imova-privat';
	if ( ! is_dir( $dir ) ) {
		wp_mkdir_p( $dir );
		file_put_contents( $dir . '/.htaccess', "Require all denied\nDeny from all\n" ); // phpcs:ignore
		file_put_contents( $dir . '/index.php', "<?php // Kein Zugriff.\n" ); // phpcs:ignore
	}
	return $dir;
}

/**
 * Speichert hochgeladene Dateien im privaten Verzeichnis.
 *
 * @return array|WP_Error Liste aus [ 'name' => Originalname, 'file' => gespeicherter Dateiname ].
 */
function imova_store_uploads( array $upload ) {
	$allowed = array(
		'pdf'  => 'application/pdf',
		'jpg'  => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'png'  => 'image/png',
		'webp' => 'image/webp',
		'heic' => 'image/heic',
	);
	$count = count( array_filter( (array) $upload['name'] ) );
	if ( $count > IMOVA_UPLOAD_MAX_FILES ) {
		return new WP_Error( 'too_many', sprintf( 'Bitte laden Sie höchstens %d Dateien hoch.', IMOVA_UPLOAD_MAX_FILES ) );
	}

	$stored = array();
	$dir    = imova_private_dir();
	foreach ( (array) $upload['name'] as $i => $original ) {
		if ( '' === $original ) {
			continue;
		}
		if ( UPLOAD_ERR_OK !== $upload['error'][ $i ] || $upload['size'][ $i ] > IMOVA_UPLOAD_MAX_BYTES ) {
			return new WP_Error( 'upload', sprintf( '„%s“ konnte nicht hochgeladen werden (max. 10 MB je Datei).', sanitize_file_name( $original ) ) );
		}
		$check = wp_check_filetype_and_ext( $upload['tmp_name'][ $i ], $original, $allowed );
		if ( ! $check['ext'] ) {
			return new WP_Error( 'type', sprintf( '„%s“ hat kein erlaubtes Format (PDF, JPG, PNG).', sanitize_file_name( $original ) ) );
		}
		$filename = wp_generate_password( 24, false ) . '.' . $check['ext'];
		if ( ! move_uploaded_file( $upload['tmp_name'][ $i ], $dir . '/' . $filename ) ) {
			return new WP_Error( 'move', 'Beim Hochladen ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.' );
		}
		$stored[] = array(
			'name' => sanitize_file_name( $original ),
			'file' => $filename,
		);
	}
	return $stored;
}

/**
 * Speichert eine Anfrage und benachrichtigt per E-Mail.
 */
function imova_save_submission( string $key, array $values, array $files ): int {
	$fields = imova_forms()[ $key ]['fields'];
	$data   = array();
	foreach ( $values as $name => $value ) {
		if ( '' === $value ) {
			continue;
		}
		if ( 'property' === $name ) {
			$value = get_the_title( (int) $value ) ?: $value;
		}
		$data[ $fields[ $name ]['label'] ?? $name ] = $value;
	}

	$subject_bits = array_filter( array( $values['name'] ?? '', $values['property_address'] ?? '' ) );
	if ( 'interesse' === $key && ! empty( $values['property'] ) ) {
		$subject_bits[] = get_the_title( (int) $values['property'] );
	}
	$title = imova_form_types()[ $key ] . ': ' . implode( ' – ', $subject_bits );

	$post_id = wp_insert_post(
		array(
			'post_type'   => 'imova_anfrage',
			'post_status' => 'publish',
			'post_title'  => $title,
		)
	);
	if ( ! $post_id || is_wp_error( $post_id ) ) {
		return 0;
	}

	update_post_meta( $post_id, '_imova_type', $key );
	update_post_meta( $post_id, '_imova_state', 'neu' );
	update_post_meta( $post_id, '_imova_data', $data );
	update_post_meta( $post_id, '_imova_email', $values['email'] ?? '' );
	update_post_meta( $post_id, '_imova_phone', $values['phone'] ?? '' );
	update_post_meta( $post_id, '_imova_name', $values['name'] ?? '' );
	if ( ! empty( $values['property'] ) ) {
		update_post_meta( $post_id, '_imova_property', (int) $values['property'] );
	}
	if ( $files ) {
		update_post_meta( $post_id, '_imova_files', $files );
	}

	imova_notify_submission( $post_id, $title, $data, $values['email'] ?? '', count( $files ) );

	return (int) $post_id;
}

function imova_notify_submission( int $post_id, string $title, array $data, string $reply_to, int $file_count ): void {
	$to = imova_opt( 'notify_email' ) ?: get_option( 'admin_email' );

	$lines = array( 'Neue Anfrage über die Website:', '' );
	foreach ( $data as $label => $value ) {
		$lines[] = $label . ': ' . $value;
	}
	if ( $file_count ) {
		$lines[] = '';
		$lines[] = sprintf( '%d Datei(en) angehängt – im Backend abrufbar.', $file_count );
	}
	$lines[] = '';
	$lines[] = 'Im Backend ansehen: ' . admin_url( 'post.php?post=' . $post_id . '&action=edit' );

	$headers = array();
	if ( is_email( $reply_to ) ) {
		$headers[] = 'Reply-To: ' . $reply_to;
	}
	wp_mail( $to, '[Website] ' . $title, implode( "\n", $lines ), $headers );
}
