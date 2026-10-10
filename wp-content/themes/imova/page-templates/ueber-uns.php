<?php
/**
 * Template Name: Über uns
 *
 * @package imova
 */

get_header();
the_post();

$photo = imova_field_img( 'founder_image', 'imova-portrait' );
$name  = imova_field( 'founder_name' );
?>

<?php get_template_part( 'template-parts/page-hero', null, array( 'title' => imova_field( 'hero_title' ), 'text' => imova_field( 'hero_text' ) ) ); ?>

<section class="section section--flush-top founder">
	<div class="container founder__inner">
		<figure class="founder__photo" data-reveal>
			<?php if ( $photo ) : ?>
				<img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $name ); ?>">
			<?php else : ?>
				<div class="placeholder-media placeholder-media--portrait">
					<?php echo imova_icon( 'user', 48 ); // phpcs:ignore ?>
					<span class="placeholder-media__note">Foto folgt</span>
				</div>
			<?php endif; ?>
			<figcaption>
				<strong><?php echo esc_html( $name ); ?></strong>
				<span><?php echo esc_html( imova_field( 'founder_role' ) ); ?></span>
			</figcaption>
		</figure>

		<div class="founder__story" data-reveal>
			<blockquote class="quote">
				<p><?php echo esc_html( imova_field( 'founder_quote' ) ); ?></p>
			</blockquote>
			<div class="prose">
				<?php the_content(); ?>
			</div>
		</div>
	</div>
</section>

<section class="section section--sand dogs">
	<div class="container">
		<div class="section-head section-head--center" data-reveal>
			<div>
				<h2 class="h2"><?php echo esc_html( imova_field( 'dogs_title' ) ); ?></h2>
				<p class="lead"><?php echo esc_html( imova_field( 'dogs_text' ) ); ?></p>
			</div>
		</div>
		<div class="dogs__grid">
			<?php foreach ( array( 1, 2 ) as $i ) : ?>
				<article class="dog-card" data-reveal>
					<div class="dog-card__photo">
						<img src="<?php echo esc_url( imova_field_img( "dog{$i}_image", 'imova-card' ) ); ?>" alt="<?php echo esc_attr( imova_field( "dog{$i}_name" ) ); ?>" loading="lazy">
					</div>
					<div class="dog-card__body">
						<p class="dog-card__role"><?php echo esc_html( imova_field( "dog{$i}_role" ) ); ?></p>
						<h3 class="h3"><?php echo esc_html( imova_field( "dog{$i}_name" ) ); ?></h3>
						<p><?php echo esc_html( imova_field( "dog{$i}_text" ) ); ?></p>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section values">
	<div class="container">
		<h2 class="h2" data-reveal><?php echo esc_html( imova_field( 'values_title' ) ); ?></h2>
		<div class="values__grid">
			<?php foreach ( array( 1 => 'leaf', 2 => 'shield', 3 => 'handshake' ) as $i => $icon ) : ?>
				<div class="value" data-reveal>
					<span class="value__num">0<?php echo (int) $i; ?></span>
					<h3 class="h3"><?php echo esc_html( imova_field( "value{$i}_title" ) ); ?></h3>
					<p><?php echo esc_html( imova_field( "value{$i}_text" ) ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php get_template_part( 'template-parts/contact-cta' ); ?>

<?php
get_footer();
