<?php
/**
 * Template Name: Kontakt
 *
 * @package imova
 */

get_header();
the_post();

?>

<?php get_template_part( 'template-parts/page-hero', null, array( 'title' => imova_field( 'hero_title' ), 'text' => imova_field( 'hero_text' ) ) ); ?>

<section class="section section--flush-top">
	<div class="container form-layout form-layout--contact">
		<div class="contact-info">
			<ul class="contact-list contact-list--large">
				<li><?php echo imova_icon( 'phone', 20 ); // phpcs:ignore ?><div><span class="label">Telefon</span><a href="<?php echo esc_attr( imova_tel_href( imova_opt( 'phone' ) ) ); ?>"><?php echo esc_html( imova_opt( 'phone' ) ); ?></a></div></li>
				<li><?php echo imova_icon( 'mail', 20 ); // phpcs:ignore ?><div><span class="label">E-Mail</span><a href="mailto:<?php echo esc_attr( imova_opt( 'email' ) ); ?>"><?php echo esc_html( imova_opt( 'email' ) ); ?></a></div></li>
				<li><?php echo imova_icon( 'pin', 20 ); // phpcs:ignore ?><div><span class="label">Anschrift</span><address><?php echo esc_html( imova_opt( 'company' ) ); ?><br><?php echo esc_html( imova_opt( 'street' ) ); ?><br><?php echo esc_html( imova_opt( 'zip_city' ) ); ?></address></div></li>
			</ul>
			<p class="muted"><?php echo esc_html( imova_field( 'office_text' ) ); ?></p>
			<?php if ( get_the_content() ) : ?>
				<div class="prose"><?php the_content(); ?></div>
			<?php endif; ?>
		</div>
		<div class="card">
			<h2 class="h3">Schreiben Sie uns</h2>
			<?php imova_render_form( 'kontakt', array( 'id' => 'kontaktformular' ) ); ?>
		</div>
	</div>
</section>

<?php
get_footer();
