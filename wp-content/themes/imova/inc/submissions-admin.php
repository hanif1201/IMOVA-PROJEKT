<?php
/**
 * Backend für Anfragen: Spalten, Filter nach Art & Bearbeitungsstand, Detailansicht, Datei-Download.
 *
 * @package imova
 */

defined( 'ABSPATH' ) || exit;

function imova_submission_states(): array {
	return array(
		'neu'        => 'Neu',
		'bearbeitung' => 'In Bearbeitung',
		'erledigt'   => 'Erledigt',
	);
}

/* Spalten ------------------------------------------------------------------ */

add_filter(
	'manage_imova_anfrage_posts_columns',
	fn() => array(
		'cb'           => '<input type="checkbox">',
		'title'        => 'Anfrage',
		'imova_type'   => 'Art',
		'imova_contact' => 'Kontakt',
		'imova_state'  => 'Stand',
		'date'         => 'Eingang',
	)
);

add_action(
	'manage_imova_anfrage_posts_custom_column',
	function ( string $column, int $post_id ) {
		switch ( $column ) {
			case 'imova_type':
				$type = get_post_meta( $post_id, '_imova_type', true );
				printf( '<span class="imova-badge imova-badge--type-%s">%s</span>', esc_attr( $type ), esc_html( imova_form_types()[ $type ] ?? $type ) );
				break;
			case 'imova_contact':
				$email = get_post_meta( $post_id, '_imova_email', true );
				$phone = get_post_meta( $post_id, '_imova_phone', true );
				if ( $email ) {
					printf( '<a href="mailto:%1$s">%1$s</a><br>', esc_attr( $email ) );
				}
				if ( $phone ) {
					printf( '<a href="%s">%s</a>', esc_attr( imova_tel_href( $phone ) ), esc_html( $phone ) );
				}
				break;
			case 'imova_state':
				$state = get_post_meta( $post_id, '_imova_state', true ) ?: 'neu';
				printf( '<span class="imova-badge imova-badge--state-%s">%s</span>', esc_attr( $state ), esc_html( imova_submission_states()[ $state ] ?? $state ) );
				break;
		}
	},
	10,
	2
);

/* Filter ------------------------------------------------------------------- */

add_action(
	'restrict_manage_posts',
	function ( string $post_type ) {
		if ( 'imova_anfrage' !== $post_type ) {
			return;
		}
		$current_type  = isset( $_GET['imova_type'] ) ? sanitize_key( $_GET['imova_type'] ) : ''; // phpcs:ignore
		$current_state = isset( $_GET['imova_state'] ) ? sanitize_key( $_GET['imova_state'] ) : ''; // phpcs:ignore

		echo '<select name="imova_type"><option value="">Alle Arten</option>';
		foreach ( imova_form_types() as $key => $label ) {
			printf( '<option value="%s" %s>%s</option>', esc_attr( $key ), selected( $current_type, $key, false ), esc_html( $label ) );
		}
		echo '</select><select name="imova_state"><option value="">Jeder Stand</option>';
		foreach ( imova_submission_states() as $key => $label ) {
			printf( '<option value="%s" %s>%s</option>', esc_attr( $key ), selected( $current_state, $key, false ), esc_html( $label ) );
		}
		echo '</select>';
	}
);

add_action(
	'pre_get_posts',
	function ( WP_Query $query ) {
		if ( ! is_admin() || ! $query->is_main_query() || 'imova_anfrage' !== $query->get( 'post_type' ) ) {
			return;
		}
		$meta = array();
		foreach ( array( 'imova_type' => '_imova_type', 'imova_state' => '_imova_state' ) as $param => $key ) {
			$value = isset( $_GET[ $param ] ) ? sanitize_key( $_GET[ $param ] ) : ''; // phpcs:ignore
			if ( $value ) {
				$meta[] = array(
					'key'   => $key,
					'value' => $value,
				);
			}
		}
		if ( $meta ) {
			$query->set( 'meta_query', $meta );
		}
	}
);

/* Schnellfilter oberhalb der Liste („Tippgeber (3) | Verkaufsangebot (1) …“) */
add_filter(
	'views_edit-imova_anfrage',
	function ( array $views ) {
		global $wpdb;
		$counts = $wpdb->get_results( "SELECT meta_value AS type, COUNT(*) AS n FROM {$wpdb->postmeta} pm JOIN {$wpdb->posts} p ON p.ID = pm.post_id WHERE pm.meta_key = '_imova_type' AND p.post_status = 'publish' AND p.post_type = 'imova_anfrage' GROUP BY meta_value", OBJECT_K ); // phpcs:ignore
		$current = isset( $_GET['imova_type'] ) ? sanitize_key( $_GET['imova_type'] ) : ''; // phpcs:ignore
		foreach ( imova_form_types() as $key => $label ) {
			$views[ 'imova_' . $key ] = sprintf(
				'<a href="%s"%s>%s <span class="count">(%d)</span></a>',
				esc_url( admin_url( 'edit.php?post_type=imova_anfrage&imova_type=' . $key ) ),
				$current === $key ? ' class="current" aria-current="page"' : '',
				esc_html( $label ),
				isset( $counts[ $key ] ) ? (int) $counts[ $key ]->n : 0
			);
		}
		return $views;
	}
);

/* Detailansicht ------------------------------------------------------------ */

add_action(
	'add_meta_boxes_imova_anfrage',
	function () {
		remove_meta_box( 'submitdiv', 'imova_anfrage', 'side' );
		remove_meta_box( 'slugdiv', 'imova_anfrage', 'normal' );
		add_meta_box( 'imova-submission', 'Angaben', 'imova_render_submission_box', 'imova_anfrage', 'normal', 'high' );
		add_meta_box( 'imova-submission-state', 'Bearbeitungsstand', 'imova_render_submission_state_box', 'imova_anfrage', 'side', 'high' );
	}
);

// Titel ist nur zur Anzeige – kein Bearbeitungsfeld.
add_action(
	'admin_head-post.php',
	function () {
		if ( 'imova_anfrage' === get_current_screen()->post_type ) {
			echo '<style>#titlediv #title{pointer-events:none;background:transparent;border-color:transparent;box-shadow:none;padding-left:0}</style>';
		}
	}
);

function imova_render_submission_box( WP_Post $post ): void {
	$data  = (array) get_post_meta( $post->ID, '_imova_data', true );
	$files = (array) get_post_meta( $post->ID, '_imova_files', true );
	$email = get_post_meta( $post->ID, '_imova_email', true );
	$phone = get_post_meta( $post->ID, '_imova_phone', true );
	$prop  = (int) get_post_meta( $post->ID, '_imova_property', true );

	echo '<table class="imova-submission widefat striped"><tbody>';
	printf( '<tr><th>Eingegangen</th><td>%s Uhr</td></tr>', esc_html( get_the_date( 'd.m.Y, H:i', $post ) ) );
	foreach ( $data as $label => $value ) {
		printf( '<tr><th>%s</th><td>%s</td></tr>', esc_html( $label ), nl2br( esc_html( (string) $value ) ) );
	}
	if ( $prop ) {
		printf( '<tr><th>Objekt</th><td><a href="%s">%s</a></td></tr>', esc_url( (string) get_edit_post_link( $prop ) ), esc_html( get_the_title( $prop ) ) );
	}
	foreach ( array_filter( $files ) as $i => $file ) {
		printf(
			'<tr><th>%s</th><td><a class="button button-small" href="%s">%s %s</a></td></tr>',
			0 === $i ? 'Unterlagen' : '',
			esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=imova_file&post=' . $post->ID . '&i=' . $i ), 'imova_file_' . $post->ID ) ),
			'<span class="dashicons dashicons-download" style="vertical-align:text-bottom"></span>',
			esc_html( $file['name'] )
		);
	}
	echo '</tbody></table>';

	echo '<p class="imova-submission__actions">';
	if ( $email ) {
		printf( '<a class="button button-primary" href="mailto:%s?subject=%s">Per E-Mail antworten</a> ', esc_attr( $email ), rawurlencode( 'Ihre Anfrage bei ' . imova_opt( 'company' ) ) );
	}
	if ( $phone ) {
		printf( '<a class="button" href="%s">Anrufen: %s</a>', esc_attr( imova_tel_href( $phone ) ), esc_html( $phone ) );
	}
	echo '</p>';
}

function imova_render_submission_state_box( WP_Post $post ): void {
	wp_nonce_field( 'imova_state_save', 'imova_state_nonce' );
	$state = get_post_meta( $post->ID, '_imova_state', true ) ?: 'neu';
	$note  = get_post_meta( $post->ID, '_imova_note', true );
	echo '<p><select name="imova_state" style="width:100%">';
	foreach ( imova_submission_states() as $key => $label ) {
		printf( '<option value="%s" %s>%s</option>', esc_attr( $key ), selected( $state, $key, false ), esc_html( $label ) );
	}
	echo '</select></p>';
	printf( '<p><label for="imova-note"><strong>Interne Notiz</strong></label><textarea id="imova-note" name="imova_note" rows="5" style="width:100%%">%s</textarea></p>', esc_textarea( (string) $note ) );
	echo '<p style="display:flex;justify-content:space-between;align-items:center">';
	submit_button( 'Speichern', 'primary', 'save', false );
	printf( '<a class="submitdelete" style="color:#b32d2e" href="%s">In den Papierkorb</a>', esc_url( (string) get_delete_post_link( $post->ID ) ) );
	echo '</p>';
	// Nötig, damit WordPress die Seite als normales Speichern behandelt.
	echo '<input type="hidden" name="post_status" value="publish">';
}

add_action(
	'save_post_imova_anfrage',
	function ( int $post_id ) {
		if ( ! isset( $_POST['imova_state_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['imova_state_nonce'] ), 'imova_state_save' ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$state = isset( $_POST['imova_state'] ) ? sanitize_key( $_POST['imova_state'] ) : 'neu';
		if ( isset( imova_submission_states()[ $state ] ) ) {
			update_post_meta( $post_id, '_imova_state', $state );
		}
		update_post_meta( $post_id, '_imova_note', isset( $_POST['imova_note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['imova_note'] ) ) : '' );
	}
);

/* Beim Öffnen einer neuen Anfrage automatisch „In Bearbeitung“ setzen. */
add_action(
	'load-post.php',
	function () {
		$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // phpcs:ignore
		if ( $post_id && 'imova_anfrage' === get_post_type( $post_id ) && 'neu' === get_post_meta( $post_id, '_imova_state', true ) && empty( $_POST ) ) {
			update_post_meta( $post_id, '_imova_state', 'bearbeitung' );
		}
	}
);

/* Datei-Download (nur für angemeldete Redakteure). */
add_action(
	'admin_post_imova_file',
	function () {
		$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
		$index   = isset( $_GET['i'] ) ? absint( $_GET['i'] ) : 0;
		check_admin_referer( 'imova_file_' . $post_id );
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( 'Keine Berechtigung.' );
		}
		$files = (array) get_post_meta( $post_id, '_imova_files', true );
		$file  = $files[ $index ] ?? null;
		$path  = $file ? imova_private_dir() . '/' . basename( $file['file'] ) : '';
		if ( ! $path || ! is_readable( $path ) ) {
			wp_die( 'Datei nicht gefunden.' );
		}
		nocache_headers();
		header( 'Content-Type: ' . ( wp_check_filetype( $path )['type'] ?: 'application/octet-stream' ) );
		header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( $file['name'] ) . '"' );
		header( 'Content-Length: ' . filesize( $path ) );
		readfile( $path ); // phpcs:ignore
		exit;
	}
);

/* Beim endgültigen Löschen einer Anfrage auch die Dateien entfernen. */
add_action(
	'before_delete_post',
	function ( int $post_id ) {
		if ( 'imova_anfrage' !== get_post_type( $post_id ) ) {
			return;
		}
		foreach ( (array) get_post_meta( $post_id, '_imova_files', true ) as $file ) {
			if ( ! empty( $file['file'] ) ) {
				wp_delete_file( imova_private_dir() . '/' . basename( $file['file'] ) );
			}
		}
	}
);

/* Zähler neuer Anfragen am Menüpunkt. */
add_action(
	'admin_menu',
	function () {
		global $menu;
		$new = new WP_Query(
			array(
				'post_type'      => 'imova_anfrage',
				'post_status'    => 'publish',
				'meta_key'       => '_imova_state',
				'meta_value'     => 'neu',
				'fields'         => 'ids',
				'posts_per_page' => 1,
				'no_found_rows'  => false,
			)
		);
		if ( ! $new->found_posts ) {
			return;
		}
		foreach ( $menu as $i => $item ) {
			if ( 'edit.php?post_type=imova_anfrage' === $item[2] ) {
				$menu[ $i ][0] .= sprintf( ' <span class="awaiting-mod"><span class="pending-count">%d</span></span>', $new->found_posts ); // phpcs:ignore
			}
		}
	},
	99
);

/* Dashboard-Widget mit den neuesten Anfragen. */
add_action(
	'wp_dashboard_setup',
	function () {
		wp_add_dashboard_widget(
			'imova_latest',
			'Neueste Anfragen',
			function () {
				$posts = get_posts( array( 'post_type' => 'imova_anfrage', 'posts_per_page' => 6 ) );
				if ( ! $posts ) {
					echo '<p>Noch keine Anfragen eingegangen.</p>';
					return;
				}
				echo '<ul class="imova-dash">';
				foreach ( $posts as $p ) {
					$state = get_post_meta( $p->ID, '_imova_state', true ) ?: 'neu';
					printf(
						'<li><a href="%s">%s</a> <span class="imova-badge imova-badge--state-%s">%s</span><br><small>%s</small></li>',
						esc_url( (string) get_edit_post_link( $p->ID ) ),
						esc_html( get_the_title( $p ) ),
						esc_attr( $state ),
						esc_html( imova_submission_states()[ $state ] ?? '' ),
						esc_html( get_the_date( 'd.m.Y, H:i', $p ) )
					);
				}
				echo '</ul>';
				printf( '<p><a class="button" href="%s">Alle Anfragen</a></p>', esc_url( admin_url( 'edit.php?post_type=imova_anfrage' ) ) );
			}
		);
	}
);
