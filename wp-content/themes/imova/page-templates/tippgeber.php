<?php
/**
 * Template Name: Tippgeber
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
		'eyebrow' => imova_opt( 'referral_reward' ),
		'title'   => imova_field( 'hero_title' ),
		'text'    => imova_field( 'hero_text' ),
		'image'   => imova_field_img( 'hero_image', 'imova-card' ),
		'actions' => '<a class="btn btn--dark" href="#tipp">Tipp abgeben ' . imova_icon( 'arrow', 18 ) . '</a>',
	)
);
?>

<section class="section section--sand steps-section">
	<div class="container">
		<h2 class="h2" data-reveal><?php echo esc_html( imova_field( 'steps_title' ) ); ?></h2>
		<ol class="steps" data-reveal>
			<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
				<li class="step">
					<span class="step__num">0<?php echo (int) $i; ?></span>
					<h3 class="step__title"><?php echo esc_html( imova_field( "step{$i}_title" ) ); ?></h3>
					<p><?php echo esc_html( imova_field( "step{$i}_text" ) ); ?></p>
				</li>
			<?php endfor; ?>
		</ol>
	</div>
</section>

<section class="section">
	<div class="container split">
		<div data-reveal>
			<h2 class="h2"><?php echo esc_html( imova_field( 'terms_title' ) ); ?></h2>
			<div class="terms prose"><?php echo imova_text( imova_field( 'terms_text' ) ); // phpcs:ignore ?></div>
			<?php if ( get_the_content() ) : ?>
				<div class="prose"><?php the_content(); ?></div>
			<?php endif; ?>
		</div>
		<ul class="trust-list" data-reveal>
			<?php foreach ( array( 1 => 'lock', 2 => 'shield', 3 => 'check' ) as $i => $icon ) : ?>
				<li>
					<span class="trust-list__icon"><?php echo imova_icon( $icon, 22 ); // phpcs:ignore ?></span>
					<div>
						<h3 class="h4"><?php echo esc_html( imova_field( "trust{$i}_title" ) ); ?></h3>
						<p><?php echo esc_html( imova_field( "trust{$i}_text" ) ); ?></p>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<section class="section section--sand" id="tipp-bereich">
	<div class="container form-layout">
		<div class="form-layout__intro">
			<h2 class="h2"><?php echo esc_html( imova_field( 'form_title' ) ); ?></h2>
			<p class="lead"><?php echo esc_html( imova_field( 'form_text' ) ); ?></p>
			<p class="muted">Lieber persönlich? Rufen Sie an: <a href="<?php echo esc_attr( imova_tel_href( imova_opt( 'phone' ) ) ); ?>"><?php echo esc_html( imova_opt( 'phone' ) ); ?></a></p>
		</div>
		<div class="card">
			<?php imova_render_form( 'tipp', array( 'id' => 'tipp' ) ); ?>
		</div>
	</div>
</section>

<?php
get_footer();
