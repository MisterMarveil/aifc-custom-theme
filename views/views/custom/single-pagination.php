<?php
/**
 * Template part for single thumbn pagination
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package finwave
 */

$previous = get_previous_post();
$next     = get_next_post();
$cols     = ( $previous && $next ) ? 'two-cols' : 'one-cols';
?>
<?php if ( finwave_option( 'rt_single_navigation_visibility' ) ) { ?>
<div class="single-post-pagination <?php echo esc_attr( $cols ) ?>">
	<?php if ( $previous ):
		$prev_image = get_the_post_thumbnail_url( $previous, 'thumbnail' ); ?>

		<div class="post-navigation prev">
			<a href="<?php echo esc_url( get_permalink( $previous ) ); ?>" class="nav-title">
				<?php echo finwave_get_svg( 'arrow-right', '180' ); ?>
				<?php esc_html_e( 'Previous Post: ', 'finwave' ) ?>
			</a>

			<a href="<?php echo esc_url( get_permalink( $previous ) ); ?>" class="link pg-prev">
				<?php if ( $prev_image ) : ?>
					<div class="post-thumb" style="background-image:url(<?php echo esc_url( $prev_image ) ?>)"></div>
				<?php endif; ?>
				<p class="item-title"><?php echo get_the_title( $previous ); ?></p>
			</a>
		</div>
	<?php endif; ?>

	<?php if ( $next ):
		$next_image = get_the_post_thumbnail_url( $next, 'thumbnail' ); ?>

		<div class="post-navigation next text-right">
			<a href="<?php echo esc_url( get_permalink( $next ) ); ?>" class="nav-title">
				<?php esc_html_e( 'Next Post: ', 'finwave' ) ?>
				<?php echo finwave_get_svg( 'arrow-right' ); ?>
			</a>
			<a href="<?php echo esc_url( get_permalink( $next ) ); ?>" class="link pg-next">
				<p class="item-title"><?php echo get_the_title( $next ); ?></p>
				<?php if ( $next_image ) : ?>
					<div class="post-thumb" style="background-image:url(<?php echo esc_url( $next_image ) ?>)"></div>
				<?php endif; ?>
			</a>
		</div>
	<?php endif; ?>
</div>
<?php } ?>
