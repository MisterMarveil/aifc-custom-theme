<?php
/**
 * Template part for displaying content
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package finwave
 */

$meta_list = finwave_option( 'rt_blog_meta', '', true );
if ( finwave_option( 'rt_blog_above_meta_visibility' ) ) {
	$meta_index = array_search( 'category', $meta_list );
	unset( $meta_list[ $meta_index ] );
}

$wow = finwave_option('rt_animation') == 1 ? 'wow' : 'animation-off';
$effect = finwave_option('rt_animation_effect');
$delay = finwave_option('delay');
$duration = finwave_option('duration');

?>
<article data-post-id="<?php the_ID(); ?>" <?php post_class( finwave_post_class() ); ?>>
	<div class="article-inner-wrapper <?php echo esc_attr($wow . ' ' . $effect); ?>" data-wow-delay="<?php echo esc_attr($delay); ?>ms" data-wow-duration="<?php echo esc_attr($duration); ?>ms">
		<?php finwave_post_thumbnail('finwave-size3'); ?>
		<div class="entry-wrapper">
			<header class="entry-header">
				<?php finwave_separate_meta( 'title-above-meta' );
				if ( ! is_single() ) {
					the_title( sprintf( '<h2 class="entry-title default-max-width"><a href="%s">', esc_url( get_permalink() ) ), '</a></h2>' );
				} else {
					the_title( '<h2 class="entry-title default-max-width">', '</h2>' );
				}

				if ( ! empty( $meta_list ) && finwave_option( 'rt_meta_visibility' ) ) {
					echo finwave_post_meta( [
						'with_list'     => true,
						'with_icon'     => true,
						'include'       => $meta_list,
						'author_prefix' => finwave_option( 'rt_author_prefix' ),
					] );
				}
				?>
			</header>
			<?php if ( finwave_option( 'rt_blog_content_visibility' ) ) : ?>
				<div class="entry-content">
					<?php finwave_entry_content() ?>
				</div>
			<?php endif; ?>
			<?php finwave_entry_footer(); ?>
		</div>
	</div>
</article>
