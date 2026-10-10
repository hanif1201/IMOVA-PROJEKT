<?php
/**
 * Tippgeber-Teaser (Startseite & Immobilienübersicht). Texte aus der Startseite.
 *
 * @package imova
 */

$front = (int) get_option( 'page_on_front' );
?>
<section class="section tipp-teaser">
	<div class="container tipp-teaser__inner">
		<div class="tipp-teaser__media" data-reveal>
			<img src="<?php echo esc_url( imova_field_img( 'tipp_image', 'imova-card', $front ) ); ?>" alt="" loading="lazy">
		</div>
		<div class="tipp-teaser__text" data-reveal>
			<p class="eyebrow"><?php echo esc_html( imova_field( 'tipp_eyebrow', $front ) ); ?></p>
			<h2 class="h2"><?php echo esc_html( imova_field( 'tipp_title', $front ) ); ?></h2>
			<p class="lead"><?php echo esc_html( imova_field( 'tipp_text', $front ) ); ?></p>
			<ol class="mini-steps">
				<li><span>01</span>Tipp abgeben</li>
				<li><span>02</span>Wir prüfen</li>
				<li><span>03</span>Ankauf</li>
				<li><span>04</span>Provision</li>
			</ol>
			<a class="btn btn--dark" href="<?php echo esc_url( imova_page_url( 'page-templates/tippgeber.php', 'tippgeber' ) ); ?>">Mehr zum Tippgeber-Programm <?php echo imova_icon( 'arrow', 18 ); // phpcs:ignore ?></a>
		</div>
	</div>
</section>
