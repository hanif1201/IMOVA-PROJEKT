<?php
/**
 * Kopfbereich.
 *
 * @package imova
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#inhalt">Zum Inhalt springen</a>

<header class="site-header" data-header>
	<div class="site-header__inner container">
		<div class="site-header__brand">
			<?php imova_logo(); ?>
		</div>

		<nav class="site-nav" id="site-nav" aria-label="Hauptnavigation" data-nav>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'nav-list',
					'fallback_cb'    => 'imova_fallback_menu',
					'depth'          => 1,
				)
			);
			?>
			<div class="site-nav__mobile-extra">
				<a class="btn btn--dark btn--block" href="<?php echo esc_url( imova_page_url( 'page-templates/verkaufen.php', 'verkaufen' ) ); ?>">Immobilie verkaufen</a>
				<a class="site-nav__contact" href="<?php echo esc_attr( imova_tel_href( imova_opt( 'phone' ) ) ); ?>"><?php echo imova_icon( 'phone', 18 ); // phpcs:ignore ?> <?php echo esc_html( imova_opt( 'phone' ) ); ?></a>
				<a class="site-nav__contact" href="mailto:<?php echo esc_attr( imova_opt( 'email' ) ); ?>"><?php echo imova_icon( 'mail', 18 ); // phpcs:ignore ?> <?php echo esc_html( imova_opt( 'email' ) ); ?></a>
			</div>
		</nav>

		<div class="site-header__actions">
			<a class="site-header__phone" href="<?php echo esc_attr( imova_tel_href( imova_opt( 'phone' ) ) ); ?>" aria-label="Anrufen: <?php echo esc_attr( imova_opt( 'phone' ) ); ?>">
				<?php echo imova_icon( 'phone', 18 ); // phpcs:ignore ?><span><?php echo esc_html( imova_opt( 'phone' ) ); ?></span>
			</a>
			<a class="btn btn--dark btn--sm site-header__cta" href="<?php echo esc_url( imova_page_url( 'page-templates/verkaufen.php', 'verkaufen' ) ); ?>">Immobilie verkaufen</a>
			<button class="nav-toggle" type="button" aria-controls="site-nav" aria-expanded="false" data-nav-toggle>
				<span class="screen-reader-text">Menü</span>
				<span class="nav-toggle__bars" aria-hidden="true"></span>
			</button>
		</div>
	</div>
</header>

<main id="inhalt" class="site-main">
