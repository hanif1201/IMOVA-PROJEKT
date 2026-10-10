<?php
/**
 * Immobilienübersicht mit Filtern.
 *
 * @package imova
 */

get_header();

global $wp_query;
$locations = get_terms( array( 'taxonomy' => 'standort', 'hide_empty' => true ) );
$action    = get_post_type_archive_link( 'immobilie' );
$total     = (int) $wp_query->found_posts;

/**
 * Select-Feld für die Filterleiste.
 */
$select = function ( string $name, string $label, array $options ) {
	$current = imova_filter_value( $name );
	printf( '<div class="filter"><label class="screen-reader-text" for="filter-%1$s">%2$s</label><div class="select select--filter"><select id="filter-%1$s" name="%1$s" data-autosubmit>', esc_attr( $name ), esc_html( $label ) );
	printf( '<option value="">%s</option>', esc_html( $label ) );
	foreach ( $options as $value => $text ) {
		printf( '<option value="%s" %s>%s</option>', esc_attr( (string) $value ), selected( $current, (string) $value, false ), esc_html( $text ) );
	}
	echo '</select>' . imova_icon( 'chevron', 16 ) . '</div></div>'; // phpcs:ignore
};
?>

<section class="listing-head">
	<div class="container">
		<h1 class="page-title">Immobilien</h1>
		<p class="lead">Wohnungen und Häuser direkt vom Eigentümer – ohne Maklerprovision.</p>
	</div>
</section>

<div class="filter-bar" data-filter-bar>
	<form class="container filter-bar__inner" action="<?php echo esc_url( $action ); ?>" method="get" role="search" aria-label="Immobilien filtern">
		<?php
		if ( $locations && ! is_wp_error( $locations ) ) {
			$select( 'ort', 'Standort', wp_list_pluck( $locations, 'name', 'slug' ) );
		}
		$select( 'zimmer', 'Zimmer', imova_room_options() );
		$select( 'preis', 'Preis', imova_price_options() );
		$select( 'status', 'Status', imova_property_statuses() );
		?>
		<div class="filter-bar__end">
			<?php $select( 'sortierung', 'Neueste zuerst', array_slice( imova_sort_options(), 1, null, true ) ); ?>
			<noscript><button class="btn btn--dark btn--sm" type="submit">Filtern</button></noscript>
		</div>
	</form>
</div>

<section class="section section--tight">
	<div class="container">
		<div class="listing-meta">
			<p><?php echo esc_html( sprintf( 1 === $total ? '%d Immobilie' : '%d Immobilien', $total ) ); ?></p>
			<?php if ( imova_filters_active() ) : ?>
				<a class="link-reset" href="<?php echo esc_url( $action ); ?>"><?php echo imova_icon( 'close', 14 ); // phpcs:ignore ?> Filter zurücksetzen</a>
			<?php endif; ?>
		</div>

		<?php if ( have_posts() ) : ?>
			<div class="property-grid">
				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/property-card' );
				}
				?>
			</div>
			<?php
			the_posts_pagination(
				array(
					'mid_size'           => 1,
					'prev_text'          => 'Zurück',
					'next_text'          => 'Weiter',
					'screen_reader_text' => 'Seitennavigation',
				)
			);
			?>
		<?php else : ?>
			<div class="empty-state">
				<?php echo imova_icon( 'home', 36 ); // phpcs:ignore ?>
				<h2 class="h3">Keine passenden Immobilien gefunden</h2>
				<p>Zu Ihrer Auswahl haben wir derzeit kein Angebot. Schreiben Sie uns, wonach Sie suchen – wir melden uns, sobald etwas Passendes frei wird.</p>
				<div class="btn-row btn-row--center">
					<a class="btn btn--outline" href="<?php echo esc_url( $action ); ?>">Alle Immobilien</a>
					<a class="btn btn--dark" href="<?php echo esc_url( imova_page_url( 'page-templates/kontakt.php', 'kontakt' ) ); ?>">Suchauftrag senden</a>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
get_template_part(
	'template-parts/contact-cta',
	null,
	array(
		'title' => 'Nichts Passendes dabei?',
		'text'  => 'Viele unserer Objekte vergeben wir, bevor sie online gehen. Sagen Sie uns, wonach Sie suchen – wir melden uns persönlich.',
	)
);

get_footer();
