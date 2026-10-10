<?php
/**
 * Abschluss-Band mit persönlichem Kontakt. $args: title, text.
 *
 * @package imova
 */

$front = (int) get_option( 'page_on_front' );
$about = get_posts( array( 'post_type' => 'page', 'meta_key' => '_wp_page_template', 'meta_value' => 'page-templates/ueber-uns.php', 'posts_per_page' => 1, 'fields' => 'ids' ) );
$photo = $about ? imova_img_url( get_post_meta( $about[0], '_imova_pf_founder_image', true ), 'imova-portrait' ) : '';
?>
<section class="contact-cta">
	<div class="container contact-cta__inner" data-reveal>
		<div class="contact-cta__person">
			<div class="avatar">
				<?php if ( $photo ) : ?>
					<img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( imova_opt( 'director' ) ); ?>" loading="lazy">
				<?php else : ?>
					<span><?php echo esc_html( implode( '', array_map( fn( $w ) => mb_substr( $w, 0, 1 ), explode( ' ', imova_opt( 'director' ) ) ) ) ); ?></span>
				<?php endif; ?>
			</div>
			<div>
				<p class="contact-cta__name"><?php echo esc_html( imova_opt( 'director' ) ); ?></p>
				<p class="contact-cta__role">Geschäftsführer</p>
			</div>
		</div>
		<div class="contact-cta__text">
			<h2 class="h2"><?php echo esc_html( $args['title'] ?? imova_field( 'cta_title', $front ) ); ?></h2>
			<p><?php echo esc_html( $args['text'] ?? imova_field( 'cta_text', $front ) ); ?></p>
		</div>
		<div class="contact-cta__actions">
			<a class="btn btn--light" href="<?php echo esc_attr( imova_tel_href( imova_opt( 'phone' ) ) ); ?>"><?php echo imova_icon( 'phone', 18 ); // phpcs:ignore ?> <?php echo esc_html( imova_opt( 'phone' ) ); ?></a>
			<a class="btn btn--ghost-light" href="<?php echo esc_url( imova_page_url( 'page-templates/kontakt.php', 'kontakt' ) ); ?>">Nachricht schreiben <?php echo imova_icon( 'arrow', 18 ); // phpcs:ignore ?></a>
		</div>
	</div>
</section>
