<?php
/**
 * Template part for displaying content
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package finwave
 */

use RT\Finwave\Options\Opt;

?>
<article data-post-id="<?php the_ID(); ?>" <?php post_class( finwave_post_class() ); ?>>
	<div class="single-inner-wrapper">
		<?php if ( ! in_array( Opt::$single_style, [ '2', '3', '4', '5' ] ) ) : ?>
			<?php finwave_post_single_thumbnail(); ?>
		<?php endif; ?>
		<div class="entry-wrapper">
			<?php finwave_single_entry_header(); ?>
				<div class="entry-content">
					<?php finwave_entry_content() ?>
				</div>
			<?php finwave_post_single_video(); ?>
			<?php finwave_entry_footer(); ?>
			<?php finwave_entry_profile(); ?>
		</div>
	</div>
</article>
