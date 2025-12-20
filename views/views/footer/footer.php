<?php
/**
 * Template part for displaying footer
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package finwave
 */

$footer_width = 'container'. finwave_option('rt_footer_width');
$copyright_center = finwave_option('rt_social_footer') ? 'justify-content-between' : 'justify-content-center';
?>

<?php if ( is_active_sidebar( 'rt-footer-sidebar' ) ) : ?>
	<div class="footer-widgets-wrapper">
		<div class="footer-container <?php echo esc_attr($footer_width) ?>">
			<div class="footer-widgets row">
				<?php dynamic_sidebar( 'rt-footer-sidebar' ); ?>
			</div>
		</div>
	</div><!-- .site-info -->
<?php endif; ?>

<?php if ( ! empty( finwave_option( 'rt_footer_copyright' ) ) ) : ?>
	<div class="footer-copyright-wrapper text-center">
		<div class="footer-container <?php echo esc_attr( $footer_width ) ?>">
			<div class="d-flex align-items-center <?php echo esc_attr($copyright_center); ?>">
				<div class="copyright-text">
					<?php finwave_html( str_replace( '[y]', date( 'Y' ), finwave_option( 'rt_footer_copyright' ) ) ); ?>
				</div>
				<?php if( finwave_option('rt_social_footer') ) { ?>
				<div class="social-icon d-flex gap-20 align-items-center">
					<div class="social-icon d-flex column-gap-10">
						<?php if( finwave_option( 'rt_follow_us_label' ) ) { ?><label><?php finwave_html( finwave_option( 'rt_follow_us_label' ), 'allow_title' ) ?></label><?php } ?>
						<?php finwave_get_social_html( '#555' ); ?>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
<?php endif; ?>
