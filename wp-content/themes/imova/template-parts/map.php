<?php
/**
 * Karte (Google Maps) – wird erst nach Einwilligung geladen.
 * Erwartet $args['address'].
 *
 * @package imova
 */

$address = trim( (string) ( $args['address'] ?? '' ) );
if ( ! $address ) {
	return;
}
$src = 'https://maps.google.com/maps?q=' . rawurlencode( $address ) . '&z=15&output=embed&hl=de';
?>
<div class="map" data-map data-src="<?php echo esc_url( $src ); ?>">
	<div class="map__placeholder">
		<?php echo imova_icon( 'pin', 28 ); // phpcs:ignore ?>
		<p class="map__address"><?php echo esc_html( $address ); ?></p>
		<p class="map__note">Mit dem Laden der Karte werden Daten an Google übertragen.</p>
		<div class="map__actions">
			<button type="button" class="btn btn--dark btn--sm" data-map-load>Karte laden</button>
			<a class="btn btn--outline btn--sm" href="https://www.google.com/maps/search/?api=1&amp;query=<?php echo rawurlencode( $address ); ?>" target="_blank" rel="noopener">In Google Maps öffnen</a>
		</div>
		<label class="check check--small"><input type="checkbox" data-map-remember> <span>Karten künftig immer laden</span></label>
	</div>
</div>
