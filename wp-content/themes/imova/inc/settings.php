<?php
/**
 * Globale Firmendaten (Einstellungen → IMOVA). Werden in Header, Footer,
 * Kontaktseite, Impressum und E-Mail-Benachrichtigungen verwendet.
 *
 * @package imova
 */

defined( 'ABSPATH' ) || exit;

function imova_settings_fields(): array {
	return array(
		'Logo' => array(
			'logo_dark'  => 'Logo für helle Hintergründe (Header) – PNG mit transparentem Hintergrund, ca. 600 px breit',
			'logo_light' => 'Logo für dunkle Hintergründe (Footer, Platzhalterbilder) – helle/weiße Version',
		),
		'Unternehmen'  => array(
			'company'         => 'Firmenname',
			'director'        => 'Geschäftsführer',
			'street'          => 'Straße & Hausnummer',
			'zip_city'        => 'PLZ & Ort',
			'register_court'  => 'Registergericht',
			'register_number' => 'Handelsregisternummer',
			'vat_id'          => 'USt-IdNr. (optional)',
		),
		'Kontakt'      => array(
			'phone'        => 'Telefon',
			'email'        => 'E-Mail (öffentlich)',
			'notify_email' => 'E-Mail für Formular-Benachrichtigungen',
		),
		'Texte & Zahlen' => array(
			'commission_rate' => 'Übliche Käuferprovision in % (z. B. 3,57)',
			'referral_reward' => 'Tippgeber-Provision (Kurztext)',
		),
	);
}

add_action(
	'admin_menu',
	function () {
		$hook = add_options_page( 'IMOVA Einstellungen', 'IMOVA', 'manage_options', 'imova-settings', 'imova_render_settings_page' );
		add_action(
			'admin_print_scripts-' . $hook,
			function () {
				wp_enqueue_media();
				wp_enqueue_script( 'imova-admin', IMOVA_URI . '/assets/js/admin.js', array( 'jquery', 'jquery-ui-sortable' ), IMOVA_VERSION . '.' . filemtime( IMOVA_DIR . '/assets/js/admin.js' ), true );
			}
		);
	}
);

add_action(
	'admin_init',
	function () {
		register_setting(
			'imova_settings',
			'imova_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => function ( $input ) {
					$clean = array();
					foreach ( array_keys( imova_default_settings() ) as $key ) {
						$value         = isset( $input[ $key ] ) ? (string) $input[ $key ] : '';
						if ( str_starts_with( $key, 'logo_' ) ) {
							$clean[ $key ] = absint( $value ) ? (string) absint( $value ) : '';
						} else {
							$clean[ $key ] = str_contains( $key, 'email' ) ? sanitize_email( $value ) : sanitize_text_field( $value );
						}
					}
					return $clean;
				},
			)
		);
	}
);

function imova_render_settings_page(): void {
	?>
	<div class="wrap">
		<h1>IMOVA – Firmendaten & Einstellungen</h1>
		<p>Diese Angaben erscheinen auf der gesamten Website (Header, Footer, Kontakt, Impressum) und steuern, wohin Formular-Benachrichtigungen gesendet werden.</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'imova_settings' ); ?>
			<?php foreach ( imova_settings_fields() as $section => $fields ) : ?>
				<h2><?php echo esc_html( $section ); ?></h2>
				<table class="form-table" role="presentation">
					<?php foreach ( $fields as $key => $label ) : ?>
						<?php if ( str_starts_with( $key, 'logo_' ) ) : ?>
							<tr>
								<th scope="row"><?php echo esc_html( 'logo_dark' === $key ? 'Logo (dunkel)' : 'Logo (hell)' ); ?></th>
								<td class="imova-logo-cell<?php echo 'logo_light' === $key ? ' is-dark' : ''; ?>"><?php imova_admin_field( 'imova_settings[' . $key . ']', array( 'label' => $label, 'type' => 'image' ), imova_opt( $key ) ); ?></td>
							</tr>
							<?php continue; ?>
						<?php endif; ?>
						<tr>
							<th scope="row"><label for="imova-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
							<td><input type="<?php echo str_contains( $key, 'email' ) ? 'email' : 'text'; ?>" class="regular-text" id="imova-<?php echo esc_attr( $key ); ?>" name="imova_settings[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( imova_opt( $key ) ); ?>"></td>
						</tr>
					<?php endforeach; ?>
				</table>
			<?php endforeach; ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
