<?php
/**
 * Template Name: Verkaufen
 *
 * @package imova
 */

get_header();
the_post();
?>

<?php
get_template_part(
	'template-parts/page-hero',
	null,
	array(
		'eyebrow' => '0 % Maklerprovision',
		'title'   => imova_field( 'hero_title' ),
		'text'    => imova_field( 'hero_text' ),
		'image'   => imova_field_img( 'hero_image', 'imova-card' ),
		'actions' => '<a class="btn btn--dark" href="#verkauf">Immobilie anbieten ' . imova_icon( 'arrow', 18 ) . '</a><a class="btn btn--outline" href="' . esc_attr( imova_tel_href( imova_opt( 'phone' ) ) ) . '">' . imova_icon( 'phone', 18 ) . ' Anrufen</a>',
	)
);
?>

<section class="section section--sand">
	<div class="container">
		<h2 class="h2" data-reveal><?php echo esc_html( imova_field( 'benefits_title' ) ); ?></h2>
		<div class="benefits" data-reveal>
			<?php foreach ( array( 1 => 'percent', 2 => 'clock', 3 => 'handshake', 4 => 'file' ) as $i => $icon ) : ?>
				<div class="benefit">
					<span class="benefit__icon"><?php echo imova_icon( $icon, 24 ); // phpcs:ignore ?></span>
					<h3 class="h4"><?php echo esc_html( imova_field( "benefit{$i}_title" ) ); ?></h3>
					<p><?php echo esc_html( imova_field( "benefit{$i}_text" ) ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<h2 class="h2" data-reveal><?php echo esc_html( imova_field( 'process_title' ) ); ?></h2>
		<ol class="steps steps--line" data-reveal>
			<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
				<li class="step">
					<span class="step__num">0<?php echo (int) $i; ?></span>
					<h3 class="step__title"><?php echo esc_html( imova_field( "step{$i}_title" ) ); ?></h3>
					<p><?php echo esc_html( imova_field( "step{$i}_text" ) ); ?></p>
				</li>
			<?php endfor; ?>
		</ol>
		<?php if ( get_the_content() ) : ?>
			<div class="prose prose--narrow"><?php the_content(); ?></div>
		<?php endif; ?>
	</div>
</section>

<section class="section section--sand" id="verkauf-bereich">
	<div class="container form-layout">
		<div class="form-layout__intro">
			<h2 class="h2"><?php echo esc_html( imova_field( 'form_title' ) ); ?></h2>
			<p class="lead"><?php echo esc_html( imova_field( 'form_text' ) ); ?></p>
			<ul class="checklist">
				<li><?php echo imova_icon( 'check', 18 ); // phpcs:ignore ?> Kostenlos &amp; unverbindlich</li>
				<li><?php echo imova_icon( 'check', 18 ); // phpcs:ignore ?> Persönliche Rückmeldung vom Geschäftsführer</li>
				<li><?php echo imova_icon( 'check', 18 ); // phpcs:ignore ?> Vertrauliche Behandlung Ihrer Daten</li>
			</ul>
		</div>
		<div class="card">
			<?php imova_render_form( 'verkauf', array( 'id' => 'verkauf' ) ); ?>
		</div>
	</div>
</section>

<?php
get_footer();
