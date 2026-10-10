<?php
/**
 * Objektkarte für Übersichten (im Loop).
 *
 * @package imova
 */

$status   = imova_prop_status();
$rooms    = imova_format_number( imova_prop( 'rooms' ) );
$area     = imova_format_number( imova_prop( 'area' ) );
$location = imova_prop_location();
?>
<article class="property-card property-card--<?php echo esc_attr( $status ); ?>">
	<a class="property-card__link" href="<?php the_permalink(); ?>">
		<div class="property-card__media">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'imova-card', array( 'loading' => 'lazy', 'alt' => '' ) ); ?>
			<?php else : ?>
				<div class="placeholder-media">
					<?php echo imova_mark(); // phpcs:ignore ?>
					<span class="placeholder-media__note"><?php echo 'demnaechst' === $status ? 'Bilder folgen in Kürze' : 'Bilder folgen'; ?></span>
				</div>
			<?php endif; ?>
			<span class="badge badge--<?php echo esc_attr( $status ); ?>"><?php echo esc_html( imova_prop_status_label() ); ?></span>
		</div>
		<div class="property-card__body">
			<?php if ( $location ) : ?>
				<p class="property-card__location"><?php echo esc_html( $location ); ?></p>
			<?php endif; ?>
			<h3 class="property-card__title"><?php the_title(); ?></h3>
			<dl class="facts">
				<?php if ( $rooms ) : ?>
					<div><dt>Zimmer</dt><dd><?php echo esc_html( $rooms ); ?></dd></div>
				<?php endif; ?>
				<?php if ( $area ) : ?>
					<div><dt>Wohnfläche</dt><dd><?php echo esc_html( $area ); ?> m²</dd></div>
				<?php endif; ?>
				<div><dt>Kaufpreis</dt><dd><?php echo esc_html( imova_prop_price_label() ); ?></dd></div>
			</dl>
		</div>
	</a>
</article>
