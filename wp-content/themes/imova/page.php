<?php
/**
 * Standardseite (z. B. Impressum, Datenschutz).
 *
 * @package imova
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article class="section text-page">
		<div class="container container--narrow">
			<h1 class="page-title"><?php the_title(); ?></h1>
			<div class="prose">
				<?php the_content(); ?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
