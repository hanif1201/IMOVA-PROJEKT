<?php
/**
 * IMOVA Projekt – Theme-Funktionen.
 *
 * @package imova
 */

defined( 'ABSPATH' ) || exit;

define( 'IMOVA_VERSION', '1.3.1' );
define( 'IMOVA_DIR', get_template_directory() );
define( 'IMOVA_URI', get_template_directory_uri() );

require IMOVA_DIR . '/inc/helpers.php';
require IMOVA_DIR . '/inc/setup.php';
require IMOVA_DIR . '/inc/settings.php';
require IMOVA_DIR . '/inc/post-types.php';
require IMOVA_DIR . '/inc/property-meta.php';
require IMOVA_DIR . '/inc/property-query.php';
require IMOVA_DIR . '/inc/page-fields.php';
require IMOVA_DIR . '/inc/forms.php';
require IMOVA_DIR . '/inc/submissions-admin.php';
require IMOVA_DIR . '/inc/demo-content.php';
