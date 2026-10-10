<?php
/**
 * Kopfbereich für Unterseiten. $args: title, text, image (URL, optional), eyebrow (optional).
 *
 * @package imova
 */

$image = $args['image'] ?? '';
?>
<section class="page-hero<?php echo $image ? ' page-hero--image' : ''; ?>">
	<div class="container page-hero__inner">
		<div class="page-hero__text" data-reveal>
			<?php if ( ! empty( $args['eyebrow'] ) ) : ?>
				<p class="eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></p>
			<?php endif; ?>
			<h1 class="page-hero__title"><?php echo esc_html( $args['title'] ?? get_the_title() ); ?></h1>
			<?php if ( ! empty( $args['text'] ) ) : ?>
				<p class="page-hero__lead"><?php echo esc_html( $args['text'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $args['actions'] ) ) : ?>
				<div class="btn-row"><?php echo $args['actions']; // phpcs:ignore ?></div>
			<?php endif; ?>
		</div>
		<?php if ( $image ) : ?>
			<div class="page-hero__media" data-reveal>
				<img src="<?php echo esc_url( $image ); ?>" alt="" loading="eager" fetchpriority="high">
			</div>
		<?php endif; ?>
	</div>
</section>
