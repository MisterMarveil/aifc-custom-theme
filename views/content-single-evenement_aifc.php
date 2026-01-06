<?php
/**
 * Template part: Single Événement AIFC amélioré
 */

$event = get_post();
$date_debut = get_field('date_debut');
$date_fin = get_field('date_fin');
?>

<div class="aifc-event-layout">

    <!-- SIDEBAR GAUCHE -->
    <aside class="aifc-event-sidebar">
        <?= do_shortcode('[aifc_event_cta title="Participez à l\'événement"]'); ?>
        
        <?php if ($date_debut && $date_fin): ?>
        <div class="aifc-event-info-card">
            <h4><span class="aifc-icon">📋</span> Informations pratiques</h4>
            <ul class="aifc-info-list">
                <li>
                    <span class="aifc-info-label">Date :</span>
                    <span class="aifc-info-value"><?= esc_html(get_field('periode_evenement')); ?></span>
                </li>
                <li>
                    <span class="aifc-info-label">Lieu :</span>
                    <span class="aifc-info-value"><?= esc_html(get_field('lieu_evenement')); ?></span>
                </li>
                <li>
                    <span class="aifc-info-label">Type :</span>
                    <span class="aifc-info-value"><?= esc_html(get_field('type_evenement')); ?></span>
                </li>
                <li>
                    <span class="aifc-info-label">Public :</span>
                    <span class="aifc-info-value"><?= esc_html(get_field('public_cible')); ?></span>
                </li>
            </ul>
        </div>
        <?php endif; ?>
        
        <div class="aifc-event-share">
            <h4><span class="aifc-icon">🔗</span> Partager</h4>
            <div class="aifc-share-buttons">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(get_permalink()); ?>" 
                   target="_blank" class="aifc-share-btn facebook">
                    Facebook
                </a>
                <a href="https://twitter.com/intent/tweet?url=<?= urlencode(get_permalink()); ?>&text=<?= urlencode(get_the_title()); ?>" 
                   target="_blank" class="aifc-share-btn twitter">
                    Twitter
                </a>
                <a href="https://wa.me/?text=<?= urlencode(get_the_title() . ' - ' . get_permalink()); ?>" 
                   target="_blank" class="aifc-share-btn whatsapp">
                    WhatsApp
                </a>
            </div>
        </div>
    </aside>

    <!-- CONTENU PRINCIPAL -->
    <main class="aifc-event-main">
        <!-- SLIDER -->
        <?= do_shortcode('[aifc_event_slider autoplay="5000" navigation="true"]'); ?>
        
        <!-- CONTENU -->
        <?= do_shortcode('[aifc_event_content show_title="true" show_countdown="true"]'); ?>
        
        <!-- PROGRAMME -->
        <?php if (have_rows('programme_evenement')): ?>
        <div class="aifc-event-programme">
            <h2><span class="aifc-icon">📅</span> Programme détaillé</h2>
            <div class="aifc-programme-timeline">
                <?php while (have_rows('programme_evenement')): the_row(); ?>
                <div class="aifc-programme-item">
                    <div class="aifc-programme-time">
                        <?= esc_html(get_sub_field('heure')); ?>
                    </div>
                    <div class="aifc-programme-content">
                        <h4><?= esc_html(get_sub_field('titre')); ?></h4>
                        <p><?= esc_html(get_sub_field('description')); ?></p>
                        <?php if (get_sub_field('intervenant')): ?>
                        <div class="aifc-programme-speaker">
                            <strong>Intervenant :</strong> <?= esc_html(get_sub_field('intervenant')); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- INTERVENANTS -->
        <?php if (have_rows('intervenants')): ?>
        <div class="aifc-event-speakers">
            <h2><span class="aifc-icon">🎤</span> Nos intervenants</h2>
            <div class="aifc-speakers-grid">
                <?php while (have_rows('intervenants')): the_row(); ?>
                <div class="aifc-speaker-card">
                    <?php if ($photo = get_sub_field('photo')): ?>
                    <img src="<?= esc_url($photo['url']); ?>" 
                         alt="<?= esc_attr($photo['alt']); ?>"
                         class="aifc-speaker-photo">
                    <?php endif; ?>
                    <h4><?= esc_html(get_sub_field('nom')); ?></h4>
                    <p class="aifc-speaker-title"><?= esc_html(get_sub_field('titre')); ?></p>
                    <p class="aifc-speaker-bio"><?= esc_html(get_sub_field('bio')); ?></p>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- FORMULAIRE DE CONTACT -->
        <div id="contact" class="aifc-event-contact">
            <h2><span class="aifc-icon">📧</span> Demande d'information</h2>
            <?= do_shortcode('[formulaire_brochure]'); ?>
        </div>
    </main>
</div>