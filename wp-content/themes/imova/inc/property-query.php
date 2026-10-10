<?php
/**
 * Filter & Sortierung für die Immobilienübersicht (GET-Parameter, ohne JavaScript nutzbar).
 *
 * @package imova
 */

defined( 'ABSPATH' ) || exit;

function imova_filter_value( string $key ): string {
	return isset( $_GET[ $key ] ) ? sanitize_text_field( wp_unslash( $_GET[ $key ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
}

function imova_price_options(): array {
	return array(
		'250000'  => 'bis 250.000 €',
		'400000'  => 'bis 400.000 €',
		'600000'  => 'bis 600.000 €',
		'800000'  => 'bis 800.000 €',
		'1200000' => 'bis 1,2 Mio. €',
	);
}

function imova_room_options(): array {
	return array(
		'1' => 'ab 1 Zimmer',
		'2' => 'ab 2 Zimmer',
		'3' => 'ab 3 Zimmer',
		'4' => 'ab 4 Zimmer',
		'5' => 'ab 5 Zimmer',
	);
}

function imova_sort_options(): array {
	return array(
		''          => 'Neueste zuerst',
		'preis-auf' => 'Preis aufsteigend',
		'preis-ab'  => 'Preis absteigend',
		'flaeche'   => 'Größte Fläche',
	);
}

add_action(
	'pre_get_posts',
	function ( WP_Query $query ) {
		if ( is_admin() || ! $query->is_main_query() || ! ( $query->is_post_type_archive( 'immobilie' ) || $query->is_tax( 'standort' ) ) ) {
			return;
		}

		$query->set( 'posts_per_page', 12 );

		$meta_query = array( 'relation' => 'AND' );
		$tax_query  = array();

		$ort = imova_filter_value( 'ort' );
		if ( $ort ) {
			$tax_query[] = array(
				'taxonomy' => 'standort',
				'field'    => 'slug',
				'terms'    => $ort,
			);
		}

		$status = imova_filter_value( 'status' );
		if ( $status && isset( imova_property_statuses()[ $status ] ) ) {
			$meta_query[] = array(
				'key'   => '_imova_status',
				'value' => $status,
			);
		}

		$rooms = imova_filter_value( 'zimmer' );
		if ( is_numeric( $rooms ) ) {
			$meta_query[] = array(
				'key'     => '_imova_rooms',
				'value'   => (float) $rooms,
				'compare' => '>=',
				'type'    => 'DECIMAL(5,1)',
			);
		}

		$price = imova_filter_value( 'preis' );
		if ( is_numeric( $price ) ) {
			$meta_query[] = array(
				'key'     => '_imova_price',
				'value'   => (int) $price,
				'compare' => '<=',
				'type'    => 'NUMERIC',
			);
		}

		$sort = imova_filter_value( 'sortierung' );
		if ( in_array( $sort, array( 'preis-auf', 'preis-ab', 'flaeche' ), true ) ) {
			$key                    = 'flaeche' === $sort ? '_imova_area' : '_imova_price';
			$meta_query['sort_key'] = array(
				'relation'   => 'OR',
				'sort_value' => array(
					'key'     => $key,
					'compare' => 'EXISTS',
					'type'    => 'DECIMAL(12,1)',
				),
				array(
					'key'     => $key,
					'compare' => 'NOT EXISTS',
				),
			);
			$query->set(
				'orderby',
				array(
					'sort_value' => 'preis-auf' === $sort ? 'ASC' : 'DESC',
					'date'       => 'DESC',
				)
			);
		}

		if ( count( $meta_query ) > 1 ) {
			$query->set( 'meta_query', $meta_query );
		}
		if ( $tax_query ) {
			$query->set( 'tax_query', $tax_query );
		}
	}
);

/**
 * Sind gerade Filter aktiv?
 */
function imova_filters_active(): bool {
	foreach ( array( 'ort', 'zimmer', 'preis', 'status' ) as $key ) {
		if ( imova_filter_value( $key ) ) {
			return true;
		}
	}
	return false;
}
