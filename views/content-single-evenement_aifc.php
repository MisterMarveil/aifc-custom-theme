<?php
/**
 * Template part: Single Événement AIFC
 */

$event = get_post();
?>

<div class="aifc-event-layout">

  <!-- SIDEBAR GAUCHE -->
  <aside class="aifc-event-sidebar">
    <?= do_shortcode('[aifc_event_cta]'); ?>
  </aside>

  <!-- CONTENU PRINCIPAL -->
  <main class="aifc-event-main">

    <!-- SLIDER -->
    <?= do_shortcode('[aifc_event_slider]'); ?>

    <!-- CONTENU -->
    <article class="aifc-event-content">

      <h1 class="aifc-event-title"><?php the_title(); ?></h1>

      <div class="aifc-event-meta">
        <p><strong>Période :</strong> <?= esc_html(get_field('periode_evenement')); ?></p>
        <p><strong>Lieu :</strong> <?= esc_html(get_field('lieu_evenement')); ?></p>
      </div>

      <?php if ($theme = get_field('theme_evenement')) : ?>
        <h3>Thème</h3>
        <p><?= esc_html($theme); ?></p>
      <?php endif; ?>

      <?php if ($resume = get_field('resume_evenement')) : ?>
        <div class="aifc-event-resume">
          <?= wp_kses_post($resume); ?>
        </div>
      <?php endif; ?>

      <div class="aifc-event-description">
        <?php the_content(); ?>
      </div>

    </article>

  </main>

</div>
