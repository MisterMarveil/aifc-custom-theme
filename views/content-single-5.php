<?php
/**
 * Template part for displaying content
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package finwave
 */

?>
<article data-post-id="<?php the_ID(); ?>" <?php post_class( finwave_post_class() ); ?>>
	<div class="single-inner-wrapper">
		<?php finwave_single_entry_header(); ?>
		<?php finwave_post_single_thumbnail(); ?>
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
