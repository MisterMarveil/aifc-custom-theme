<?php
/**
 * Template part for single project related
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package finwave
 */

namespace RT\Finwave\Modules;

class ProjectRelated {
	public static function rt_project_related() {
		$post_id = get_the_id();
		$current_post = array( $post_id );
		$title_length = finwave_option( 'rt_project_related_title_limit' ) ? finwave_option( 'rt_project_related_title_limit' ) : '';
		$related_item_number = finwave_option( 'rt_project_related_limit' );

		$content = get_the_content();
		$content = apply_filters( 'the_content', $content );
		$content = wp_trim_words( get_the_excerpt(), finwave_option( 'rt_project_excerpt_limit' ), '' );

		# Making ready to the Query ...
		$query_type = finwave_option( 'rt_project_related_query' );

		$args = array(
			'post_type'				 => 'rt-project',
			'post__not_in'           => $current_post,
			'posts_per_page'         => $related_item_number,
			'no_found_rows'          => true,
			'post_status'            => 'publish',
			'ignore_sticky_posts'    => true,
			'update_post_term_cache' => false,
		);

		# Checking Related Posts Order ----------
		if( finwave_option( 'rt_project_related_sort' ) ){

			$post_order = finwave_option( 'rt_project_related_sort' );

			if( $post_order == 'rand' ){
				$args['orderby'] = 'rand';
			}
			elseif( $post_order == 'views' ){
				$args['orderby']  = 'meta_value_num';
			}
			elseif( $post_order == 'popular' ){
				$args['orderby'] = 'comment_count';
			}
			elseif( $post_order == 'modified' ){
				$args['orderby'] = 'modified';
				$args['order']   = 'ASC';
			}
			elseif( $post_order == 'recent' ){
				$args['orderby'] = '';
				$args['order']   = '';
			}
		}

		# Get related posts by author ----------
		if( $query_type == 'author' ){
			$args['author'] = get_the_author_meta( 'ID' );
		}

		# Get related posts by tags ----------
		elseif( $query_type == 'tag' ){
			$tags_ids  = array();
			$post_tags = get_the_terms( $post_id, 'post_tag' );

			if( ! empty( $post_tags ) ){
				foreach( $post_tags as $individual_tag ){
					$tags_ids[] = $individual_tag->term_id;
				}
				$args['tag__in'] = $tags_ids;
			}
		}

		# Get related posts by categories ----------
		else{
			$terms = get_the_terms( $post_id, 'rt-project-category' );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$port_cat_links = array();
				foreach ( $terms as $term ) {
					$port_cat_links[] = $term->term_id;
				}
				$args['tax_query'] = array (
					array (
						'taxonomy' => 'rt-project-category',
						'field'    => 'ID',
						'terms'    => $port_cat_links,
					)
				);
			}
		}

		# Get the posts ----------
		$related_query = new \WP_Query( $args );
		if( $related_query->have_posts() && finwave_option( 'rt_project_related' ) ) { ?>
			<div class="rt-project-default rt-project-multi-layout-default rt-related-project">
				<div class="container">
					<h2 class="related-title"><?php finwave_html( finwave_option( 'rt_project_related_title' ) , 'allow_title' );?></h2>
					<div class="row g-4">
						<?php while ( $related_query->have_posts() ) {
						$related_query->the_post();
						$trimmed_title = wp_trim_words( get_the_title(), $title_length, '' );
						?>
						<div class="col-sm-6 col-xl-4 col-lg-4" >
							<article id="post-<?php the_ID(); ?>">
								<div class="project-item">
									<div class="project-thumbs">
										<?php finwave_post_thumbnail('finwave-size6'); ?>
									</div>
									<div class="project-content d-flex justify-content-between">
										<div class="project-info">
											<h3 class="project-title"><a href="<?php the_permalink();?>"><?php echo esc_html( $trimmed_title ); ?></a></h3>
											<?php if ( finwave_option( 'rt_project_ar_cat' ) ) { ?>
												<span class="project-cat"><?php
													$i = 1;
													$term_lists = get_the_terms( get_the_ID(), 'rt-project-category' );
													if( $term_lists ) {
														foreach ( $term_lists as $term_list ){
															$link = get_term_link( $term_list->term_id, 'rt-project-category' ); ?>
															<?php if ( $i > 1 ){ echo esc_html( ', ' ); } ?><a href="<?php echo esc_url( $link ); ?>">
															<?php echo esc_html( $term_list->name ); ?></a><?php $i++; } } ?></span>
											<?php } if ( finwave_option( 'rt_project_ar_excerpt' ) ) { ?>
												<div class="project-excerpt"><?php finwave_html( $content , false ); ?></div>
											<?php } ?>
										</div>
										<?php if( finwave_option( 'rt_project_ar_button' ) ) { ?>
											<div class="rt-button">
												<a class="btn button-4" href="<?php the_permalink();?>"><i class="icon-rt-arrow-right-1"></i></a>
											</div>
										<?php } ?>
									</div>
								</div>
							</article>
						</div>
					<?php } ?>
					</div>
				</div>
			</div>
		<?php }
		wp_reset_postdata();
	}
}
?>
