<?php
/**
 * Template part for displaying content
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package finwave
 */

$meta_list = finwave_option( 'rt_single_meta', '', true );
$meta      = finwave_option( 'rt_blog_above_meta_visibility' );
$meta      = finwave_option( 'rt_single_above_meta_style' );
if ( finwave_option( 'rt_single_above_meta_visibility' ) ) {
	$category_index = array_search( 'category', $meta_list );
	unset( $meta_list[ $category_index ] );
}
?>
<article data-post-id="<?php the_ID(); ?>" <?php post_class( finwave_post_class() ); ?>>
	<div class="single-inner-wrapper">
		<div class="entry-wrapper">
			<div class="entry-content">
				<?php finwave_entry_content() ?>
			</div>
			<?php finwave_post_single_video(); ?>
			<?php finwave_entry_footer(); ?>
			<?php finwave_entry_profile(); ?>
		</div>
	</div>
</article>
