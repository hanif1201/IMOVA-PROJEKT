<?php
/**
 * Inhaltstypen: Immobilien (öffentlich) und Anfragen (nur im Backend).
 *
 * @package imova
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		register_post_type(
			'immobilie',
			array(
				'labels'        => array(
					'name'               => 'Immobilien',
					'singular_name'      => 'Immobilie',
					'menu_name'          => 'Immobilien',
					'add_new'            => 'Neue Immobilie',
					'add_new_item'       => 'Neue Immobilie anlegen',
					'edit_item'          => 'Immobilie bearbeiten',
					'new_item'           => 'Neue Immobilie',
					'view_item'          => 'Immobilie ansehen',
					'view_items'         => 'Immobilien ansehen',
					'search_items'       => 'Immobilien durchsuchen',
					'not_found'          => 'Keine Immobilien gefunden',
					'not_found_in_trash' => 'Keine Immobilien im Papierkorb',
					'all_items'          => 'Alle Immobilien',
					'featured_image'     => 'Titelbild',
					'set_featured_image' => 'Titelbild festlegen',
				),
				'public'        => true,
				'has_archive'   => 'immobilien',
				'rewrite'       => array(
					'slug'       => 'immobilien',
					'with_front' => false,
				),
				'menu_icon'     => 'dashicons-building',
				'menu_position' => 5,
				'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
				'show_in_rest'  => true,
			)
		);

		register_taxonomy(
			'standort',
			'immobilie',
			array(
				'labels'            => array(
					'name'          => 'Standorte',
					'singular_name' => 'Standort',
					'menu_name'     => 'Standorte',
					'add_new_item'  => 'Neuen Standort hinzufügen',
					'edit_item'     => 'Standort bearbeiten',
					'search_items'  => 'Standorte durchsuchen',
					'all_items'     => 'Alle Standorte',
				),
				'hierarchical'      => true,
				'public'            => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array(
					'slug'       => 'standort',
					'with_front' => false,
				),
			)
		);

		register_post_type(
			'imova_anfrage',
			array(
				'labels'          => array(
					'name'          => 'Anfragen',
					'singular_name' => 'Anfrage',
					'menu_name'     => 'Anfragen',
					'all_items'     => 'Alle Anfragen',
					'edit_item'     => 'Anfrage ansehen',
					'search_items'  => 'Anfragen durchsuchen',
					'not_found'     => 'Noch keine Anfragen eingegangen',
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'menu_icon'       => 'dashicons-email-alt',
				'menu_position'   => 4,
				'supports'        => array( 'title' ),
				'capability_type' => 'post',
				'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'    => true,
			)
		);
	}
);
