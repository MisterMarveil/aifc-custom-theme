<?php
get_header();

// Boucle standard
if (have_posts()) :
  while (have_posts()) : the_post();

    // On charge le template project
    get_template_part('content', 'single-project');

  endwhile;
endif;

get_footer();
