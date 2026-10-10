<?php
/**
 * Seite nicht gefunden.
 *
 * @package imova
 */

get_header();
?>
<section class="section not-found">
	<div class="container container--narrow">
		<p class="eyebrow">Fehler 404</p>
		<h1 class="page-title">Diese Seite ist leider umgezogen.</h1>
		<p class="lead">Vielleicht finden Sie, was Sie suchen, in unseren aktuellen Immobilien – oder Sie schreiben uns direkt.</p>
		<div class="btn-row">
			<a class="btn btn--dark" href="<?php echo esc_url( get_post_type_archive_link( 'immobilie' ) ); ?>">Zu den Immobilien</a>
			<a class="btn btn--outline" href="<?php echo esc_url( home_url( '/' ) ); ?>">Zur Startseite</a>
		</div>
	</div>
</section>
<?php
get_footer();
