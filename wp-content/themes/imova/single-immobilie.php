<?php
/**
 * Einzelansicht einer Immobilie – mit eigener Darstellung für „Demnächst“.
 *
 * @package imova
 */

get_header();

while ( have_posts() ) :
	the_post();

	$status   = imova_prop_status();
	$soon     = 'demnaechst' === $status;
	$sold     = 'verkauft' === $status;
	$location = imova_prop_location();
	$address  = trim( imova_prop( 'street' ) . ', ' . trim( imova_prop( 'zip' ) . ' ' . imova_prop( 'city' ) ), ', ' );
	$public   = '1' === imova_prop( 'show_address' ) && imova_prop( 'street' );
	$images   = imova_prop_gallery_ids();
	$expose   = (int) imova_prop( 'expose' );
	$rooms    = imova_format_number( imova_prop( 'rooms' ) );
	$area     = imova_format_number( imova_prop( 'area' ) );
	$features = array_filter( array_map( 'trim', explode( "\n", imova_prop( 'features' ) ) ) );

	$details = array_filter(
		array(
			'Objektart'      => imova_prop( 'property_type' ),
			'Zimmer'         => $rooms,
			'Wohnfläche'     => $area ? $area . ' m²' : '',
			'Etage'          => imova_prop( 'floor' ),
			'Baujahr'        => imova_prop( 'year_built' ),
			'Modernisierung' => imova_prop( 'modernized' ),
			'Zustand'        => imova_prop( 'condition' ),
			'Heizung'        => imova_prop( 'heating' ),
			'Verfügbar ab'   => imova_prop( 'available_from' ),
		)
	);
	$energy = array_filter(
		array(
			'Energieausweis'          => imova_prop( 'energy_cert' ),
			'Endenergie'              => imova_prop( 'energy_value' ) ? imova_format_number( imova_prop( 'energy_value' ) ) . ' kWh/(m²·a)' : '',
			'Effizienzklasse'         => imova_prop( 'energy_class' ),
			'Wesentlicher Energieträger' => imova_prop( 'energy_carrier' ),
			'Baujahr laut Ausweis'    => imova_prop( 'energy_cert' ) && imova_prop( 'year_built' ) ? imova_prop( 'year_built' ) : '',
		)
	);
	?>

	<nav class="breadcrumb container" aria-label="Brotkrumen">
		<a href="<?php echo esc_url( get_post_type_archive_link( 'immobilie' ) ); ?>">Immobilien</a>
		<span aria-hidden="true">/</span>
		<span aria-current="page"><?php the_title(); ?></span>
	</nav>

	<?php if ( $soon ) : ?>
		<?php /* ---------- Demnächst ---------- */ ?>
		<section class="soon">
			<div class="container soon__inner">
				<div class="soon__visual">
					<?php if ( $images ) : ?>
						<?php echo wp_get_attachment_image( $images[0], 'imova-hero', false, array( 'class' => 'soon__img', 'alt' => '' ) ); ?>
					<?php endif; ?>
					<div class="soon__overlay">
						<span class="badge badge--demnaechst">Demnächst</span>
						<?php echo imova_mark( 'soon__mark' ); // phpcs:ignore ?>
						<p>Fotos, Grundriss und alle Details<br>folgen in Kürze.</p>
					</div>
				</div>

				<div class="soon__content">
					<?php if ( $location ) : ?>
						<p class="eyebrow"><?php echo esc_html( $location ); ?></p>
					<?php endif; ?>
					<h1 class="h1"><?php the_title(); ?></h1>
					<?php if ( $public ) : ?>
						<p class="soon__address"><?php echo imova_icon( 'pin', 18 ); // phpcs:ignore ?> <?php echo esc_html( $address ); ?></p>
					<?php endif; ?>

					<dl class="facts facts--large">
						<?php if ( $rooms ) : ?>
							<div><dt>Zimmer</dt><dd><?php echo esc_html( $rooms ); ?></dd></div>
						<?php endif; ?>
						<div><dt>Wohnfläche</dt><dd><?php echo $area ? esc_html( $area ) . ' m²' : 'folgt'; ?></dd></div>
						<div><dt>Kaufpreis</dt><dd><?php echo esc_html( imova_prop_price_label() ); ?></dd></div>
					</dl>

					<?php $teaser = array_filter( array_map( 'trim', explode( "\n", imova_prop( 'teaser' ) ) ) ); ?>
					<?php if ( $teaser ) : ?>
						<ul class="checklist">
							<?php foreach ( $teaser as $line ) : ?>
								<li><?php echo imova_icon( 'check', 18 ); // phpcs:ignore ?> <?php echo esc_html( $line ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<div class="soon__form card">
						<h2 class="h3">Unverbindlich vormerken lassen</h2>
						<p class="muted">Sie erfahren als Erste(r), sobald Bilder, Grundriss und Preis feststehen.</p>
						<?php imova_render_form( 'interesse', array( 'property_id' => get_the_ID(), 'id' => 'vormerken' ) ); ?>
					</div>
				</div>
			</div>
		</section>

		<?php if ( get_the_content() || $public ) : ?>
			<section class="section section--sand">
				<div class="container property-body property-body--soon">
					<?php if ( get_the_content() ) : ?>
						<div class="prose"><h2 class="h3">Über das Projekt</h2><?php the_content(); ?></div>
					<?php endif; ?>
					<?php if ( $public ) : ?>
						<div><h2 class="h3">Lage</h2><?php get_template_part( 'template-parts/map', null, array( 'address' => $address ) ); ?></div>
					<?php endif; ?>
				</div>
			</section>
		<?php endif; ?>

	<?php else : ?>
		<?php /* ---------- Reguläres Objekt ---------- */ ?>
		<section class="gallery container" data-gallery>
			<?php if ( $images ) : ?>
				<div class="gallery__grid gallery__grid--<?php echo esc_attr( (string) min( count( $images ), 3 ) ); ?>">
					<?php foreach ( array_slice( $images, 0, 3 ) as $i => $id ) : ?>
						<button type="button" class="gallery__item" data-gallery-open="<?php echo esc_attr( (string) $i ); ?>" aria-label="Bild <?php echo esc_attr( (string) ( $i + 1 ) ); ?> vergrößern">
							<?php echo wp_get_attachment_image( $id, 0 === $i ? 'imova-hero' : 'imova-card', false, array( 'alt' => '', 'loading' => 0 === $i ? 'eager' : 'lazy' ) ); ?>
						</button>
					<?php endforeach; ?>
				</div>
				<span class="badge badge--<?php echo esc_attr( $status ); ?> gallery__badge"><?php echo esc_html( imova_prop_status_label() ); ?></span>
				<?php if ( count( $images ) > 1 ) : ?>
					<button type="button" class="btn btn--light btn--sm gallery__all" data-gallery-open="0"><?php echo imova_icon( 'expand', 16 ); // phpcs:ignore ?> Alle <?php echo count( $images ); ?> Bilder</button>
				<?php endif; ?>
				<template data-gallery-items>
					<?php foreach ( $images as $id ) : ?>
						<img src="<?php echo esc_url( (string) wp_get_attachment_image_url( $id, 'imova-hero' ) ); ?>" alt="<?php echo esc_attr( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) ); ?>">
					<?php endforeach; ?>
				</template>
			<?php else : ?>
				<div class="placeholder-media placeholder-media--hero"><?php echo imova_mark(); // phpcs:ignore ?><span class="placeholder-media__note">Bilder folgen</span></div>
			<?php endif; ?>
		</section>

		<div class="container property-layout">
			<div class="property-main">
				<header class="property-header">
					<?php if ( $location ) : ?>
						<p class="eyebrow"><?php echo esc_html( $location ); ?></p>
					<?php endif; ?>
					<h1 class="h1"><?php the_title(); ?></h1>
					<?php if ( $public ) : ?>
						<p class="property-header__address"><?php echo imova_icon( 'pin', 18 ); // phpcs:ignore ?> <?php echo esc_html( $address ); ?></p>
					<?php endif; ?>
				</header>

				<dl class="facts facts--large facts--ruled">
					<?php if ( $rooms ) : ?>
						<div><dt>Zimmer</dt><dd><?php echo esc_html( $rooms ); ?></dd></div>
					<?php endif; ?>
					<?php if ( $area ) : ?>
						<div><dt>Wohnfläche</dt><dd><?php echo esc_html( $area ); ?> m²</dd></div>
					<?php endif; ?>
					<?php if ( imova_prop( 'floor' ) ) : ?>
						<div><dt>Etage</dt><dd><?php echo esc_html( imova_prop( 'floor' ) ); ?></dd></div>
					<?php endif; ?>
					<?php if ( imova_prop( 'year_built' ) ) : ?>
						<div><dt>Baujahr</dt><dd><?php echo esc_html( imova_prop( 'year_built' ) ); ?></dd></div>
					<?php endif; ?>
				</dl>

				<?php if ( get_the_content() ) : ?>
					<section class="property-section">
						<h2 class="h3">Beschreibung</h2>
						<div class="prose"><?php the_content(); ?></div>
					</section>
				<?php endif; ?>

				<?php if ( $features ) : ?>
					<section class="property-section">
						<h2 class="h3">Ausstattung</h2>
						<ul class="checklist checklist--columns">
							<?php foreach ( $features as $feature ) : ?>
								<li><?php echo imova_icon( 'check', 18 ); // phpcs:ignore ?> <?php echo esc_html( $feature ); ?></li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endif; ?>

				<?php if ( $details ) : ?>
					<section class="property-section">
						<h2 class="h3">Objektdaten</h2>
						<dl class="data-table">
							<?php foreach ( $details as $label => $value ) : ?>
								<div><dt><?php echo esc_html( $label ); ?></dt><dd><?php echo esc_html( $value ); ?></dd></div>
							<?php endforeach; ?>
						</dl>
					</section>
				<?php endif; ?>

				<?php if ( $energy ) : ?>
					<section class="property-section">
						<h2 class="h3">Energie</h2>
						<?php if ( imova_prop( 'energy_class' ) ) : ?>
							<?php $classes = array( 'A+', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H' ); ?>
							<div class="energy-scale" role="img" aria-label="Energieeffizienzklasse <?php echo esc_attr( imova_prop( 'energy_class' ) ); ?>">
								<?php foreach ( $classes as $i => $class ) : ?>
									<span class="energy-scale__step<?php echo $class === imova_prop( 'energy_class' ) ? ' is-active' : ''; ?>" style="--i:<?php echo (int) $i; ?>"><?php echo esc_html( $class ); ?></span>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						<dl class="data-table">
							<?php foreach ( $energy as $label => $value ) : ?>
								<div><dt><?php echo esc_html( $label ); ?></dt><dd><?php echo esc_html( $value ); ?></dd></div>
							<?php endforeach; ?>
						</dl>
					</section>
				<?php endif; ?>

				<?php if ( $public ) : ?>
					<section class="property-section">
						<h2 class="h3">Lage</h2>
						<?php get_template_part( 'template-parts/map', null, array( 'address' => $address ) ); ?>
					</section>
				<?php endif; ?>
			</div>

			<aside class="property-aside">
				<div class="price-card">
					<p class="price-card__label"><?php echo $sold ? 'Status' : 'Kaufpreis'; ?></p>
					<p class="price-card__price"><?php echo esc_html( imova_prop_price_label() ); ?></p>
					<?php if ( imova_prop( 'price_note' ) && ! $sold ) : ?>
						<p class="price-card__note"><?php echo esc_html( imova_prop( 'price_note' ) ); ?></p>
					<?php endif; ?>
					<p class="price-card__commission"><?php echo imova_icon( 'percent', 16 ); // phpcs:ignore ?> Provisionsfrei – direkt vom Eigentümer</p>

					<?php if ( ! $sold ) : ?>
						<a class="btn btn--dark btn--block" href="#anfrage">Anfrage senden</a>
					<?php else : ?>
						<a class="btn btn--dark btn--block" href="<?php echo esc_url( get_post_type_archive_link( 'immobilie' ) ); ?>">Verfügbare Immobilien</a>
					<?php endif; ?>
					<a class="btn btn--outline btn--block" href="<?php echo esc_attr( imova_tel_href( imova_opt( 'phone' ) ) ); ?>"><?php echo imova_icon( 'phone', 18 ); // phpcs:ignore ?> <?php echo esc_html( imova_opt( 'phone' ) ); ?></a>
					<?php if ( $expose && ! $sold ) : ?>
						<a class="btn btn--ghost btn--block" href="<?php echo esc_url( (string) wp_get_attachment_url( $expose ) ); ?>" download><?php echo imova_icon( 'file', 18 ); // phpcs:ignore ?> Exposé herunterladen (PDF)</a>
					<?php endif; ?>
				</div>
				<div class="finance-note">
					<?php echo imova_icon( 'handshake', 22 ); // phpcs:ignore ?>
					<p><strong>Finanzierung ohne Papierkram:</strong> Unsere Finanzierungspartner vermitteln Ihnen günstige Konditionen. Alle benötigten Objektunterlagen senden wir direkt an Ihren Finanzberater – Sie müssen nichts anfordern oder weiterleiten.</p>
				</div>
			</aside>
		</div>

		<?php if ( ! $sold ) : ?>
			<section class="section section--sand" id="anfrage-bereich">
				<div class="container form-layout">
					<div class="form-layout__intro">
						<h2 class="h2">Interesse an dieser Immobilie?</h2>
						<p class="lead">Schreiben Sie uns – wir senden Ihnen gern weitere Unterlagen und vereinbaren einen Besichtigungstermin.</p>
						<ul class="contact-list">
							<li><?php echo imova_icon( 'phone', 18 ); // phpcs:ignore ?><a href="<?php echo esc_attr( imova_tel_href( imova_opt( 'phone' ) ) ); ?>"><?php echo esc_html( imova_opt( 'phone' ) ); ?></a></li>
							<li><?php echo imova_icon( 'mail', 18 ); // phpcs:ignore ?><a href="mailto:<?php echo esc_attr( imova_opt( 'email' ) ); ?>"><?php echo esc_html( imova_opt( 'email' ) ); ?></a></li>
						</ul>
					</div>
					<div class="card">
						<?php
						imova_render_form(
							'interesse',
							array(
								'property_id' => get_the_ID(),
								'id'          => 'anfrage',
								'submit'      => 'Anfrage senden',
								'success'     => 'Vielen Dank für Ihre Anfrage! Wir melden uns in Kürze mit weiteren Informationen zu dieser Immobilie.',
							)
						);
						?>
					</div>
				</div>
			</section>
		<?php endif; ?>
	<?php endif; ?>

	<?php
	$related = new WP_Query(
		array(
			'post_type'      => 'immobilie',
			'posts_per_page' => 3,
			'post__not_in'   => array( get_the_ID() ),
			'no_found_rows'  => true,
		)
	);
	if ( $related->have_posts() ) :
		?>
		<section class="section">
			<div class="container">
				<div class="section-head">
					<h2 class="h2">Weitere Immobilien</h2>
					<a class="link-arrow" href="<?php echo esc_url( get_post_type_archive_link( 'immobilie' ) ); ?>">Alle ansehen <?php echo imova_icon( 'arrow', 18 ); // phpcs:ignore ?></a>
				</div>
				<div class="property-grid">
					<?php
					while ( $related->have_posts() ) {
						$related->the_post();
						get_template_part( 'template-parts/property-card' );
					}
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
	<?php endif; ?>

<?php endwhile; ?>

<?php
get_footer();
