<?php
/**
 * The template for displaying all single project
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package finwave
 */

use RT\Finwave\Options\Opt;

global $post;
$id = get_the_ID();
$rt_project_title 		= get_post_meta( $id, 'rt_project_title', true );
$rt_project_text 		= get_post_meta( $id, 'rt_project_text', true );
$rt_project_client 		= get_post_meta( $id, 'rt_project_client', true );
$rt_project_start 		= get_post_meta( $id, 'rt_project_start', true );
$rt_project_end 		= get_post_meta( $id, 'rt_project_end', true );
$rt_project_weblink 	= get_post_meta( $id, 'rt_project_weblink', true );

$ratting	 	= get_post_meta( $id, 'rt_project_rating', true );
$rt_project_rating = 5- intval( $ratting );

?>
<div id="post-<?php the_ID();?>" <?php post_class( 'project-single' );?>>
	<div class="project-single-item">
		<div class="project-item-wrap">
			<div class="project-content-info sidebar-sticky">
				<div class="project-information">
					<?php if ( !empty( $rt_project_title ) && finwave_option( 'rt_project_title' )) { ?>
						<div class="rt-section-title style3 has-animation">
							<h2 class="info-title">BRIDGE GOOD<?php echo esc_html( $rt_project_title );?><span class="line"></span></h2>
						</div>
					<?php } if ( !empty( $rt_project_text ) && finwave_option( 'rt_project_text' ) ) { ?>
						<p><?php echo esc_html( $rt_project_text );?></p>
					<?php } ?>
					<ul class="info-list">
						<?php if ( finwave_option( 'rt_project_cat' ) ) { ?>
							<li><label><?php esc_html_e( 'Category', 'finwave' );?>: </label>
								<span class="project-cat"><?php
									$i = 1;
									$term_lists = get_the_terms( get_the_ID(), 'rt-project-category' );
									if( $term_lists ) { foreach ( $term_lists as $term_list ){
											$link = get_term_link( $term_list->term_id, 'rt-project-category' ); ?>
											<?php if ( $i > 1 ){ echo esc_html( ', ' ); } ?><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $term_list->name ); ?></a><?php $i++; } } ?></span>
							</li>
						<?php } ?>
						<?php if ( !empty( $rt_project_client ) && finwave_option( 'rt_project_client' ) ) { ?>
							<li><label><?php esc_html_e( 'Client', 'finwave' );?>: </label><?php echo esc_html( $rt_project_client );?></li>
						<?php } if ( !empty( $rt_project_start ) && finwave_option( 'rt_project_start' ) ) { ?>
							<li><label><?php esc_html_e( 'Starts On', 'finwave' );?>: </label><?php echo esc_html( $rt_project_start );?></li>
						<?php } if ( !empty( $rt_project_end ) && finwave_option( 'rt_project_end' ) ) { ?>
							<li><label><?php esc_html_e( 'Ends On', 'finwave' );?>: </label><?php echo esc_html( $rt_project_end );?></li>
						<?php } if ( !empty( $rt_project_weblink ) && finwave_option( 'rt_project_weblink' ) ) { ?>
							<li><label><?php esc_html_e( 'Web Link', 'finwave' );?>: </label><?php echo esc_html( $rt_project_weblink );?></li>
						<?php } ?>

						<?php if( finwave_option( 'rt_project_rating' ) ) { ?>
							<?php if( $ratting != -1) { ?>
								<li><label><?php esc_html_e( 'Rating', 'finwave' );?>: </label>
									<ul class="rating">
										<?php for ($i=0; $i < $ratting; $i++) { ?>
											<li class="star-rate"><i class="icon-rt-star" aria-hidden="true"></i></li>
										<?php } ?>
										<?php for ($i=0; $i < $rt_project_rating; $i++) { ?>
											<li><i class="icon-rt-star" aria-hidden="true"></i></li>
										<?php } ?>
									</ul>
								</li>
							<?php } } ?>
					</ul>
				</div>
			</div>
			<div class="project-item-content">
				<?php if ( has_post_thumbnail() ) { ?>
					<div class="post-thumbnail-wrap single-post-thumbnail">
						<figure class="post-thumbnail">
							<?php the_post_thumbnail( 'full' ); ?>
						</figure><!-- .post-thumbnail -->
					</div>
				<?php } ?>
				<div class="project-content">
					<?php if( ! Opt::$breadcrumb_title == 1 ) { ?>
						<h1 class="entry-title"><?php the_title(); ?></h1>
					<?php } ?>
					<?php the_content();?>
				</div>
			</div>
		</div>
	</div>
</div>
