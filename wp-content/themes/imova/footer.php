<?php
/**
 * Fußbereich mit Firmendaten, Navigation und Cookie-Hinweis.
 *
 * @package imova
 */

?>
</main>

<footer class="site-footer">
	<div class="container">
		<div class="site-footer__top">
			<div class="site-footer__brand">
				<?php imova_logo( 'light' ); ?>
				<p class="site-footer__claim">Bestehendes bewahren.<br>Werte für morgen schaffen.</p>
				<?php if ( imova_opt( 'instagram' ) ) : ?>
					<a class="social-link" href="<?php echo esc_url( imova_opt( 'instagram' ) ); ?>" target="_blank" rel="noopener"><?php echo imova_icon( 'instagram', 20 ); // phpcs:ignore ?> Instagram</a>
				<?php endif; ?>
			</div>

			<div class="site-footer__col">
				<p class="site-footer__heading">Kontakt</p>
				<address>
					<?php echo esc_html( imova_opt( 'company' ) ); ?><br>
					<?php echo esc_html( imova_opt( 'street' ) ); ?><br>
					<?php echo esc_html( imova_opt( 'zip_city' ) ); ?>
				</address>
				<p>
					<a href="<?php echo esc_attr( imova_tel_href( imova_opt( 'phone' ) ) ); ?>"><?php echo esc_html( imova_opt( 'phone' ) ); ?></a><br>
					<a href="mailto:<?php echo esc_attr( imova_opt( 'email' ) ); ?>"><?php echo esc_html( imova_opt( 'email' ) ); ?></a>
				</p>
			</div>

			<div class="site-footer__col">
				<p class="site-footer__heading">Navigation</p>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'footer-list',
						'fallback_cb'    => 'imova_fallback_menu',
						'depth'          => 1,
					)
				);
				?>
			</div>

			<div class="site-footer__col">
				<p class="site-footer__heading">Persönlich für Sie da</p>
				<p><?php echo esc_html( imova_opt( 'director' ) ); ?><br><span class="muted">Geschäftsführer</span></p>
				<a class="btn btn--light btn--sm" href="<?php echo esc_url( imova_page_url( 'page-templates/kontakt.php', 'kontakt' ) ); ?>">Kontakt aufnehmen</a>
			</div>
		</div>

		<div class="site-footer__bottom">
			<p>
				© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( imova_opt( 'company' ) ); ?>
				<span class="sep">·</span> <?php echo esc_html( imova_opt( 'register_court' ) ); ?> <?php echo esc_html( imova_opt( 'register_number' ) ); ?>
				<span class="sep">·</span> Geschäftsführer: <?php echo esc_html( imova_opt( 'director' ) ); ?>
			</p>
			<nav aria-label="Rechtliches">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'legal',
						'container'      => false,
						'menu_class'     => 'legal-list',
						'fallback_cb'    => false,
						'depth'          => 1,
					)
				);
				?>
				<button type="button" class="linklike" data-consent-open>Cookie-Einstellungen</button>
			</nav>
		</div>
	</div>
</footer>

<?php if ( imova_whatsapp_url() ) : ?>
	<a class="wa-float" href="<?php echo esc_url( imova_whatsapp_url( imova_whatsapp_context_text() ) ); ?>" target="_blank" rel="noopener" aria-label="Per WhatsApp schreiben">
		<span class="wa-float__label">Schreiben Sie uns auf WhatsApp</span>
		<span class="wa-float__icon"><?php echo imova_whatsapp_icon( 30 ); // phpcs:ignore ?></span>
	</a>
<?php endif; ?>

<?php get_template_part( 'template-parts/consent' ); ?>
<?php wp_footer(); ?>
</body>
</html>
