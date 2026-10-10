<?php
/**
 * Fallback-Template.
 *
 * @package imova
 */

get_header();
?>
<section class="section text-page">
	<div class="container container--narrow">
		<h1 class="page-title"><?php echo is_search() ? 'Suche' : esc_html( wp_strip_all_tags( get_the_archive_title() ?: get_bloginfo( 'name' ) ) ); ?></h1>
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article class="list-entry">
					<h2 class="h3"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>
				</article>
			<?php endwhile; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p>Hier gibt es derzeit keine Inhalte.</p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
