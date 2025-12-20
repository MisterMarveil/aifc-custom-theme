<?php
/**
 * Template part for displaying service
 * @link https://codex.wordpress.org/Template_Hierarchy
 * @package finwave
 */

$id = get_the_ID();
$rt_service_icon= get_post_meta( $id, 'rt_service_icon', true );
$icon_class 			= '' ;
if ( !empty( $rt_service_icon ) ) {
	$icon_class 		= 'service-item-icon';
} else {
	$icon_class 		= 'service-item-image';
}

$service_icon_bg    = get_post_meta( $id, 'rt_service_color', true );
$service_bg = "";
if( !empty( $service_icon_bg ) ) {
	$service_bg = 'style="color: ' . $service_icon_bg . '"';
}

$content = get_the_content();
$content = apply_filters( 'the_content', $content );
$content = wp_trim_words( get_the_excerpt(), finwave_option( 'rt_service_excerpt_limit' ), '' );

?>
<article id="post-<?php the_ID(); ?>">
	<div class="service-item <?php echo esc_attr( $icon_class ); ?>">
		<div class="service-content">
			<div class="service-info">
				<?php if (!empty( $rt_service_icon )  ) { ?>
					<div class="service-icon" <?php echo wp_specialchars_decode( esc_attr( $service_bg ), ENT_COMPAT ); ?>><i class="<?php finwave_html( $rt_service_icon , false );?>"></i></div>
				<?php } ?>
				<h2 class="service-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h2>
			</div>
			<?php if ( finwave_option( 'rt_service_ar_excerpt' ) ) { ?>
				<p><?php finwave_html( $content , false ); ?></p>
			<?php } ?>
			<?php if ( finwave_option( 'rt_service_read_more' ) ) { ?>
				<div class="rt-button">
					<a class="btn button-3" href="<?php the_permalink();?>">
						<?php esc_html_e('See Details' , 'finwave' ); ?><i class="icon-rt-right-arrow"></i>
					</a>
				</div>
			<?php } ?>

			<div class="rt-shape">
				<svg width="133" height="68" viewBox="0 0 133 68" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M55.8635 28.8563L5.07903 72.9805C2.28574 75.4075 4.00216 80 7.70251 80H146C148.209 80 150 78.2091 150 76V7.92083C150 4.4673 145.918 2.63688 143.339 4.93419L97.1412 46.0924C95.8612 47.2327 93.9969 47.4307 92.506 46.5845L60.4614 28.397C58.9864 27.5599 57.1438 27.7439 55.8635 28.8563Z" stroke="currentColor" stroke-width="6"></path>
				</svg>
			</div>

		</div>

	</div>
</article>
