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
$rt_project_title 		= get_post_meta( $id, 'rt_project_title', true);
$rt_project_text 		= get_post_meta( $id, 'rt_project_text', true);
$rt_project_client 		= get_post_meta( $id, 'rt_project_client', true);
$rt_project_start 		= get_post_meta( $id, 'rt_project_start', true);
$rt_project_end 		= get_post_meta( $id, 'rt_project_end', true);
$rt_project_weblink 	= get_post_meta( $id, 'rt_project_weblink', true);
$rt_project_weblink 	= get_post_meta( $id, 'rt_project_weblink', true);

$ratting	 	= get_post_meta( $id, 'rt_project_rating', true );
$rt_project_rating = 5- intval( $ratting );

?>
<div id="post-<?php the_ID();?>" <?php post_class( 'project-single' );?>>
	<div class="project-single-item">
		<div class="project-item-wrap">
			<div class="project-content-info sidebar-sticky">
				<?php 
				// Remplacer tout le contenu existant par :
				echo do_shortcode('[widget_info_formation compact="yes"]');
				
				// Vous pouvez ajouter d'autres widgets si nécessaire
				// echo do_shortcode('[formulaire_brochure]');
				// echo do_shortcode('[formulaire_conseiller]');
				?>
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
