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

if ( !empty( has_post_thumbnail() ) ) {
	$image_empty = 'is-image';
} else {
	$image_empty = 'no-image';
}

$wow = finwave_option('rt_animation') == 1 ? 'wow' : 'animation-off';
$effect = finwave_option('rt_animation_effect');
$delay = finwave_option('delay');
$duration = finwave_option('duration');

?>

<article data-post-id="<?php the_ID(); ?>" <?php post_class( finwave_post_class() ); ?>>
	<div class="article-inner-wrapper <?php echo esc_attr($image_empty .' '. $wow . ' ' . $effect); ?>" data-wow-delay="<?php echo esc_attr($delay); ?>ms" data-wow-duration="<?php echo esc_attr($duration); ?>ms">
		<div class="post-thumbnail-wrap">
			<?php $swiper_data=array(
				'slidesPerView' 	=>1,
				'centeredSlides'	=>false,
				'loop'				=>true,
				'spaceBetween'		=>20,
				'slideToClickedSlide' =>true,
				'slidesPerGroup' => 1,
				'autoplay'				=>array(
					'delay'  => 1,
				),
				'speed'      =>500,
				'breakpoints' =>array(
					'0'    =>array('slidesPerView' =>1),
					'425'    =>array('slidesPerView' =>1),
					'576'    =>array('slidesPerView' =>1),
					'768'    =>array('slidesPerView' =>1),
					'992'    =>array('slidesPerView' =>1),
					'1200'    =>array('slidesPerView' =>1),
					'1600'    =>array('slidesPerView' =>1)
				),
				'auto'   =>false
			);

			$swiper_data = json_encode( $swiper_data );
			$rt_post_gallerys_raw = get_post_meta( get_the_ID(), 'rt_post_gallery', true );
			$rt_post_gallerys = explode( "," , $rt_post_gallerys_raw );
			if ( !empty( $rt_post_gallerys_raw ) && 'gallery' == get_post_format( get_the_ID() ) ) { ?>
				<div class="rt-swiper-slider single-post-slider rt-swiper-nav" data-xld = '<?php echo esc_attr( $swiper_data ); ?>'>
					<div class="swiper-wrapper">
						<?php foreach( $rt_post_gallerys as $rt_posts_gallery ) { ?>
							<div class="swiper-slide">
								<?php echo wp_get_attachment_image( $rt_posts_gallery, 'finwave-size3', "", array( "class" => "img-responsive" ) );
								?>
							</div>
						<?php } ?>
					</div>
					<div class="swiper-navigation">
						<div class="swiper-button swiper-button-prev"><i class="icon-rt-left-arrow"></i></div>
						<div class="swiper-button swiper-button-next"><i class="icon-rt-right-arrow"></i></div>
					</div>
				</div>
			<?php } else { ?>
				<figure class="post-thumbnail">
					<a class="post-thumb-link alignwide" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1"><?php the_post_thumbnail( 'finwave-size3' ); ?></a>
				</figure><!-- .post-thumbnail -->

				<?php $rt_youtube_link = get_post_meta( get_the_ID(), 'rt_youtube_link', true );
				if ( finwave_option( 'rt_video_visibility' ) == 1 && ( 'video' == get_post_format( get_the_ID() ) ) && !empty( $rt_youtube_link ) ) { ?>
					<div class="rt-video"><a class="popup-youtube video-popup-icon" href="<?php echo esc_url( $rt_youtube_link );?>"><i class="icon-rt-play"></i></a></div>
				<?php } ?>
			<?php } ?>
			<?php finwave_separate_meta( 'title-above-meta' ); ?>
		</div>
		<div class="entry-wrapper">
			<header class="entry-header">
				<?php if ( ! is_single() ) {
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
