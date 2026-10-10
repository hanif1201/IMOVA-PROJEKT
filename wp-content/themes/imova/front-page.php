<?php
/**
 * Startseite.
 *
 * @package imova
 */

get_header();

$rate      = imova_opt( 'commission_rate' );
$rate_num  = (float) str_replace( ',', '.', $rate );
$listings  = new WP_Query(
	array(
		'post_type'      => 'immobilie',
		'posts_per_page' => 3,
		'no_found_rows'  => true,
		'meta_query'     => array(
			array(
				'key'     => '_imova_status',
				'value'   => 'verkauft',
				'compare' => '!=',
			),
		),
	)
);
?>

<section class="hero">
	<div class="hero__media">
		<img src="<?php echo esc_url( imova_field_img( 'hero_image' ) ); ?>" alt="" fetchpriority="high">
	</div>
	<div class="container hero__inner">
		<div class="hero__content">
			<p class="eyebrow eyebrow--light"><?php echo esc_html( imova_field( 'hero_eyebrow' ) ); ?></p>
			<h1 class="hero__title"><?php echo imova_field_lines( 'hero_title' ); // phpcs:ignore ?></h1>
			<p class="hero__lead"><?php echo esc_html( imova_field( 'hero_text' ) ); ?></p>
			<div class="btn-row">
				<a class="btn btn--light" href="<?php echo esc_url( get_post_type_archive_link( 'immobilie' ) ); ?>">Immobilien ansehen <?php echo imova_icon( 'arrow', 18 ); // phpcs:ignore ?></a>
				<a class="btn btn--ghost-light" href="<?php echo esc_url( imova_page_url( 'page-templates/verkaufen.php', 'verkaufen' ) ); ?>">Immobilie verkaufen</a>
			</div>
		</div>
	</div>
</section>

<section class="trust-bar" aria-label="Auf einen Blick">
	<div class="container trust-bar__inner">
		<?php foreach ( array( 1 => 'pin', 2 => 'handshake', 3 => 'percent', 4 => 'user' ) as $i => $icon ) : ?>
			<div class="trust-item">
				<?php echo imova_icon( $icon, 22 ); // phpcs:ignore ?>
				<div><strong><?php echo esc_html( imova_field( "trust{$i}_title" ) ); ?></strong><span><?php echo esc_html( imova_field( "trust{$i}_text" ) ); ?></span></div>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<section class="section philosophy">
	<div class="container philosophy__inner">
		<div class="philosophy__media" data-reveal>
			<img src="<?php echo esc_url( imova_field_img( 'phil_image', 'imova-portrait' ) ); ?>" alt="" loading="lazy">
		</div>
		<div class="philosophy__text" data-reveal>
			<p class="eyebrow"><?php echo esc_html( imova_field( 'phil_eyebrow' ) ); ?></p>
			<h2 class="h2"><?php echo esc_html( imova_field( 'phil_title' ) ); ?></h2>
			<div class="prose prose--lead"><?php echo imova_text( imova_field( 'phil_text' ) ); // phpcs:ignore ?></div>
			<ul class="pillars">
				<?php foreach ( array( 1 => 'layers', 2 => 'hammer', 3 => 'leaf' ) as $i => $icon ) : ?>
					<li class="pillar">
						<span class="pillar__icon"><?php echo imova_icon( $icon, 22 ); // phpcs:ignore ?></span>
						<div>
							<h3 class="pillar__title"><?php echo esc_html( imova_field( "pillar{$i}_title" ) ); ?></h3>
							<p><?php echo esc_html( imova_field( "pillar{$i}_text" ) ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>

<?php if ( $listings->have_posts() ) : ?>
	<section class="section section--sand listings-preview">
		<div class="container">
			<div class="section-head" data-reveal>
				<div>
					<h2 class="h2"><?php echo esc_html( imova_field( 'list_title' ) ); ?></h2>
					<p class="lead"><?php echo esc_html( imova_field( 'list_text' ) ); ?></p>
				</div>
				<a class="link-arrow" href="<?php echo esc_url( get_post_type_archive_link( 'immobilie' ) ); ?>">Alle Immobilien <?php echo imova_icon( 'arrow', 18 ); // phpcs:ignore ?></a>
			</div>
			<div class="property-grid" data-reveal>
				<?php
				while ( $listings->have_posts() ) {
					$listings->the_post();
					get_template_part( 'template-parts/property-card' );
				}
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>
<?php endif; ?>

<section class="section section--dark savings">
	<div class="container savings__inner">
		<div class="savings__main" data-reveal>
			<p class="eyebrow eyebrow--light"><?php echo esc_html( imova_field( 'savings_eyebrow' ) ); ?></p>
			<h2 class="h2"><?php echo esc_html( imova_field( 'savings_title' ) ); ?></h2>
			<p class="lead"><?php echo esc_html( imova_field( 'savings_text' ) ); ?></p>

			<div class="calc" data-calc data-rate="<?php echo esc_attr( (string) $rate_num ); ?>">
				<label class="calc__label" for="calc-price">Kaufpreis</label>
				<output class="calc__price" data-calc-price for="calc-price">400.000 €</output>
				<input class="calc__range" id="calc-price" type="range" min="150000" max="2000000" step="10000" value="400000" data-calc-input>
				<div class="calc__result">
					<span>Ihre Ersparnis gegenüber <?php echo esc_html( $rate ); ?> % Provision</span>
					<strong data-calc-result><?php echo esc_html( imova_format_price( round( 400000 * $rate_num / 100 ) ) ); ?></strong>
				</div>
			</div>

			<div class="btn-row">
				<a class="btn btn--light" href="<?php echo esc_url( get_post_type_archive_link( 'immobilie' ) ); ?>">Provisionsfrei kaufen</a>
				<a class="btn btn--ghost-light" href="<?php echo esc_url( imova_page_url( 'page-templates/verkaufen.php', 'verkaufen' ) ); ?>">Provisionsfrei verkaufen</a>
			</div>
		</div>

		<aside class="finance-card" data-reveal>
			<span class="finance-card__icon"><?php echo imova_icon( 'handshake', 28 ); // phpcs:ignore ?></span>
			<h3 class="h3"><?php echo esc_html( imova_field( 'finance_title' ) ); ?></h3>
			<p><?php echo esc_html( imova_field( 'finance_text' ) ); ?></p>
			<ul class="checklist checklist--light">
				<?php foreach ( array_filter( array_map( fn( $i ) => imova_field( "finance_point{$i}" ), range( 1, 4 ) ) ) as $point ) : ?>
					<li><?php echo imova_icon( 'check', 18 ); // phpcs:ignore ?> <?php echo esc_html( $point ); ?></li>
				<?php endforeach; ?>
			</ul>
		</aside>
	</div>
</section>

<?php get_template_part( 'template-parts/tippgeber-teaser' ); ?>

<?php get_template_part( 'template-parts/contact-cta' ); ?>

<?php
get_footer();
