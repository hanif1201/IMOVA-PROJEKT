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
				<?php if ( imova_whatsapp_url() ) : ?>
					<li class="contact-list__wa"><?php echo imova_whatsapp_icon( 20 ); // phpcs:ignore ?><div><span class="label">WhatsApp – am schnellsten</span><a href="<?php echo esc_url( imova_whatsapp_url( imova_whatsapp_context_text() ) ); ?>" target="_blank" rel="noopener">Nachricht schreiben</a></div></li>
				<?php endif; ?>
				<li><?php echo imova_icon( 'phone', 20 ); // phpcs:ignore ?><div><span class="label">Telefon</span><a href="<?php echo esc_attr( imova_tel_href( imova_opt( 'phone' ) ) ); ?>"><?php echo esc_html( imova_opt( 'phone' ) ); ?></a></div></li>
				<li><?php echo imova_icon( 'mail', 20 ); // phpcs:ignore ?><div><span class="label">E-Mail</span><a href="mailto:<?php echo esc_attr( imova_opt( 'email' ) ); ?>"><?php echo esc_html( imova_opt( 'email' ) ); ?></a></div></li>
				<li><?php echo imova_icon( 'pin', 20 ); // phpcs:ignore ?><div><span class="label">Region</span><span class="contact-list__value">München &amp; Augsburg</span></div></li>
				<?php if ( imova_opt( 'instagram' ) ) : ?>
					<li><?php echo imova_icon( 'instagram', 20 ); // phpcs:ignore ?><div><span class="label">Instagram</span><a href="<?php echo esc_url( imova_opt( 'instagram' ) ); ?>" target="_blank" rel="noopener">@imovaprojekt</a></div></li>
				<?php endif; ?>
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
