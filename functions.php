<?php
add_action( 'wp_enqueue_scripts', 'finwave_child_styles', 18 );
function finwave_child_styles() {
	wp_enqueue_style( 'child-style', get_stylesheet_uri() );
}

// Renommer le type de post "rt-project" en "Formations"
add_action('init', 'rt_rename_project_to_formation', 999);
function rt_rename_project_to_formation() {
    global $wp_post_types;
    
    if (isset($wp_post_types['rt-project'])) {
        // Renommer les labels
        $labels = &$wp_post_types['rt-project']->labels;
        $labels->name = 'Formations';
        $labels->singular_name = 'Formation';
        $labels->add_new = 'Ajouter une formation';
        $labels->add_new_item = 'Ajouter une nouvelle formation';
        $labels->edit_item = 'Modifier la formation';
        $labels->new_item = 'Nouvelle formation';
        $labels->view_item = 'Voir la formation';
        $labels->search_items = 'Rechercher des formations';
        $labels->not_found = 'Aucune formation trouvée';
        $labels->not_found_in_trash = 'Aucune formation dans la corbeille';
        $labels->all_items = 'Toutes les formations';
        $labels->menu_name = 'Formations';
        $labels->name_admin_bar = 'Formation';
        
        // Renommer la taxonomie
        register_taxonomy('rt-project-category', 'rt-project', array(
            'labels' => array(
                'name' => 'Catégories de formation',
                'singular_name' => 'Catégorie',
                'search_items' => 'Rechercher des catégories',
                'all_items' => 'Toutes les catégories',
                'parent_item' => 'Catégorie parente',
                'parent_item_colon' => 'Catégorie parente :',
                'edit_item' => 'Modifier la catégorie',
                'update_item' => 'Mettre à jour la catégorie',
                'add_new_item' => 'Ajouter une nouvelle catégorie',
                'new_item_name' => 'Nom de la nouvelle catégorie',
                'menu_name' => 'Catégories',
            ),
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'categorie-formation'),
        ));
    }
}

// Solution générique qui filtre tout le contenu HTML
add_filter('the_content', 'rt_replace_breadcrumb_texts', 1);
add_filter('wp_nav_menu_items', 'rt_replace_breadcrumb_texts', 10, 2);
add_filter('wp_title', 'rt_replace_breadcrumb_texts');
function rt_replace_breadcrumb_texts($content) {
    // Remplacer tous les textes liés à "Project"
    $replacements = array(
        'Projects' => 'Formations',
        'projects' => 'formations',
        'Project' => 'Formation',
        'project' => 'formation',
        'rt-project' => 'formation',
        'rt-projects' => 'formations',
    );
    
    foreach ($replacements as $search => $replace) {
        // Dans le contenu HTML
        $content = str_replace($search, $replace, $content);
        // Dans les attributs
        $content = str_replace(
            htmlspecialchars($search), 
            htmlspecialchars($replace), 
            $content
        );
    }
    
    return $content;
}

// Solution plus ciblée pour les breadcrumbs
add_action('wp_footer', 'rt_replace_breadcrumb_js');
function rt_replace_breadcrumb_js() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cibler les breadcrumbs spécifiquement
        const breadcrumbContainers = [
            '.breadcrumb-area'           
        ];
        
        breadcrumbContainers.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            elements.forEach(element => {
                // Remplacer les textes
                element.innerHTML = element.innerHTML
                    .replace(/Projects/g, 'Formations')
                    .replace(/projects/g, 'formations')
                    .replace(/Project/g, 'Formation')
                    .replace(/project/g, 'formation')
                    .replace(/rt-project/g, 'formation')
                    .replace(/rt-projects/g, 'formations');
                
                // Remplacer dans les title attributes
                if (element.title) {
                    element.title = element.title
                        .replace(/Projects/g, 'Formations')
                        .replace(/Project/g, 'Formation');
                }
            });
        });
        
        // Remplacer aussi dans les aria-current
        const ariaElements = document.querySelectorAll('[aria-current="page"]');
        ariaElements.forEach(element => {
            if (element.textContent.includes('Project') || element.textContent.includes('project')) {
                element.textContent = element.textContent
                    .replace(/Project/g, 'Formation')
                    .replace(/project/g, 'formation');
            }
        });
    });
    </script>
    <?php
}

// Ajouter la méta box détaillée pour les formations
add_action('add_meta_boxes', 'rt_formation_detailed_meta_boxes');
function rt_formation_detailed_meta_boxes() {
    add_meta_box(
        'rt_formation_details',
        '📋 Détails de la Formation',
        'rt_formation_meta_box_callback',
        'rt-project',
        'normal',
        'high'
    );
}

function rt_formation_meta_box_callback($post) {
    wp_nonce_field('rt_formation_save_meta', 'rt_formation_meta_nonce');
    
    // Récupérer les valeurs existantes
   /* $formation_categorie = get_post_meta($post->ID, '_rt_formation_categorie', true);*/
    $formation_duree = get_post_meta($post->ID, '_rt_formation_duree', true);
    $formation_niveau = get_post_meta($post->ID, '_rt_formation_niveau', true);
    $formation_public = get_post_meta($post->ID, '_rt_formation_public', true);
    $formation_prix = get_post_meta($post->ID, '_rt_formation_prix', true);
    $formation_paiement = get_post_meta($post->ID, '_rt_formation_paiement', true);
    $formation_mode = get_post_meta($post->ID, '_rt_formation_mode', true);
    $formation_certification = get_post_meta($post->ID, '_rt_formation_certification', true);
    $formation_prochaine_rentree = get_post_meta($post->ID, '_rt_formation_prochaine_rentree', true);
    $formation_lien_preinscription = get_post_meta($post->ID, '_rt_formation_lien_preinscription', true);
    $formation_description_courte = get_post_meta($post->ID, '_rt_formation_description_courte', true);
    $formation_conseiller_phone = get_post_meta($post->ID, '_rt_formation_conseiller_phone', true); 
    ?>
    
    <style>
        .formation-meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        .formation-field {
            margin-bottom: 15px;
        }
        .formation-field label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #23282d;
        }
        .formation-field input[type="text"],
        .formation-field input[type="url"],
        .formation-field input[type="number"],
        .formation-field select,
        .formation-field textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .formation-field-full {
            grid-column: 1 / -1;
        }
        .formation-note {
            font-style: italic;
            color: #666;
            font-size: 12px;
            margin-top: 3px;
        }
    </style>
    
    <div class="formation-meta-grid">
        <!-- Description courte -->
        <div class="formation-field formation-field-full">
            <label for="rt_formation_description_courte">📌 Description courte de la formation</label>
            <textarea id="rt_formation_description_courte" name="rt_formation_description_courte" rows="3"><?php echo esc_textarea($formation_description_courte); ?></textarea>
            <p class="formation-note">Texte d'accroche qui apparaît en début de fiche formation</p>
        </div>
        
        <!-- Catégorie détaillée -->
        <!--div class="formation-field">
            <label for="rt_formation_categorie">📂 Catégorie détaillée</label>
            <input type="text" id="rt_formation_categorie" name="rt_formation_categorie" 
                   value="<?php /*echo esc_attr($formation_categorie);*/ ?>" placeholder="Ex: Formations Standards — Islamic Finance">
        </div-->
        
        <!-- Durée -->
        <div class="formation-field">
            <label for="rt_formation_duree">⏳ Durée</label>
            <input type="text" id="rt_formation_duree" name="rt_formation_duree" 
                   value="<?php echo esc_attr($formation_duree); ?>" placeholder="Ex: 6 mois (E-learning)">
        </div>
        
        <!-- Niveau requis -->
        <div class="formation-field">
            <label for="rt_formation_niveau">🎓 Niveau requis</label>
            <textarea id="rt_formation_niveau" name="rt_formation_niveau" rows="2"><?php echo esc_textarea($formation_niveau); ?></textarea>
            <p class="formation-note">Ex: Baccalauréat ou équivalent + 3 ans d'expérience</p>
        </div>
        
        <!-- Public cible -->
        <div class="formation-field">
            <label for="rt_formation_public">💼 Public cible</label>
            <textarea id="rt_formation_public" name="rt_formation_public" rows="3"><?php echo esc_textarea($formation_public); ?></textarea>
        </div>
        
        <!-- Prix -->
        <div class="formation-field">
            <label for="rt_formation_prix">💳 Prix de la formation</label>
            <input type="text" id="rt_formation_prix" name="rt_formation_prix" 
                   value="<?php echo esc_attr($formation_prix); ?>" placeholder="Ex: 350,000 FCFA">
        </div>
        
        <!-- Modalités de paiement -->
        <div class="formation-field">
            <label for="rt_formation_paiement">💰 Modalités de paiement</label>
            <input type="text" id="rt_formation_paiement" name="rt_formation_paiement" 
                   value="<?php echo esc_attr($formation_paiement); ?>" placeholder="Ex: En 03 versements — 50% à l'inscription">
        </div>
        
        <!-- Méthode pédagogique -->
        <div class="formation-field">
            <label for="rt_formation_mode">📥 Méthode pédagogique</label>
            <textarea id="rt_formation_mode" name="rt_formation_mode" rows="2"><?php echo esc_textarea($formation_mode); ?></textarea>
            <p class="formation-note">Ex: Formation 100% en ligne, Cours + études de cas</p>
        </div>
        
        <!-- Certification -->
        <div class="formation-field">
            <label for="rt_formation_certification">🏅 Certification délivrée</label>
            <input type="text" id="rt_formation_certification" name="rt_formation_certification" 
                   value="<?php echo esc_attr($formation_certification); ?>" placeholder="Ex: Certificat de formation professionnelle AIFC">
        </div>
        
        <!-- Prochaine rentrée -->
        <div class="formation-field">
            <label for="rt_formation_prochaine_rentree">📅 Prochaine rentrée</label>
            <input type="text" id="rt_formation_prochaine_rentree" name="rt_formation_prochaine_rentree" 
                   value="<?php echo esc_attr($formation_prochaine_rentree); ?>" placeholder="Ex: Dates programmables selon session">
        </div>
        
        <!-- Lien de préinscription -->
        <div class="formation-field formation-field-full">
            <label for="rt_formation_lien_preinscription">🔗 Lien de préinscription</label>
            <input type="url" id="rt_formation_lien_preinscription" name="rt_formation_lien_preinscription" 
                   value="<?php echo esc_url($formation_lien_preinscription); ?>" placeholder="https://votresite.com/preinscription">
            <p class="formation-note">Lien vers le formulaire de préinscription ou de réservation</p>
        </div>

        <!-- Numero conseiller -->
        <div class="formation-field">
            <label for="rt_formation_conseiller_phone">📞 Numéro du conseiller (WhatsApp)</label>
            <input type="tel" id="rt_formation_conseiller_phone" name="rt_formation_conseiller_phone" 
                value="<?php echo esc_attr($formation_conseiller_phone); ?>" placeholder="Ex: +237691919116">
            <p class="formation-note">Numéro au format international (avec indicatif pays)</p>
        </div>
    </div>
    <?php
}

// Sauvegarder tous les champs
add_action('save_post', 'rt_formation_save_meta_box_data');
function rt_formation_save_meta_box_data($post_id) {
    if (!isset($_POST['rt_formation_meta_nonce']) || 
        !wp_verify_nonce($_POST['rt_formation_meta_nonce'], 'rt_formation_save_meta')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    $fields = array(
        'rt_formation_description_courte',
        //'rt_formation_categorie',
        'rt_formation_duree',
        'rt_formation_niveau',
        'rt_formation_public',
        'rt_formation_prix',
        'rt_formation_paiement',
        'rt_formation_mode',
        'rt_formation_certification',
        'rt_formation_prochaine_rentree',
        'rt_formation_lien_preinscription',
        'rt_formation_conseiller_phone', 
    );
    
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            if ($field === 'rt_formation_modules') {
                update_post_meta($post_id, '_' . $field, wp_kses_post($_POST[$field]));
            } elseif ($field === 'rt_formation_lien_preinscription') {
                update_post_meta($post_id, '_' . $field, esc_url_raw($_POST[$field]));
            } else {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }
    }
}

// Ajouter des colonnes personnalisées dans la liste des formations
add_filter('manage_rt-project_posts_columns', 'rt_formation_admin_columns');
function rt_formation_admin_columns($columns) {
    $new_columns = array();
    
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = 'Titre de la formation';
    /*$new_columns['formation_categorie'] = '📂 Catégorie';*/
    $new_columns['formation_prix'] = '💳 Prix';
    $new_columns['formation_duree'] = '⏳ Durée';
    $new_columns['formation_prochaine'] = '📅 Prochaine rentrée';
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
}

// Remplir les colonnes avec les données
add_action('manage_rt-project_posts_custom_column', 'rt_formation_admin_column_data', 10, 2);
function rt_formation_admin_column_data($column, $post_id) {
    switch($column) {
        /*case 'formation_categorie':
            $categorie = get_post_meta($post_id, '_rt_formation_categorie', true);
            echo $categorie ? esc_html($categorie) : '—';
            break;*/
            
        case 'formation_prix':
            $prix = get_post_meta($post_id, '_rt_formation_prix', true);
            echo $prix ? '<strong>' . esc_html($prix) . '</strong>' : '—';
            break;
            
        case 'formation_duree':
            $duree = get_post_meta($post_id, '_rt_formation_duree', true);
            echo $duree ? esc_html($duree) : '—';
            break;
            
        case 'formation_prochaine':
            $prochaine = get_post_meta($post_id, '_rt_formation_prochaine_rentree', true);
            echo $prochaine ? '<span style="color: #d63638; font-weight: bold;">' . esc_html($prochaine) . '</span>' : '—';
            break;
    }
}

function aifc_register_formation_mode_taxonomy() {
    register_taxonomy(
        'formation_mode',
        'rt-project',
        array(
            'label'             => 'Mode de formation',
            'public'            => true,
            'hierarchical'      => false,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rewrite'           => false,
        )
    );

}
add_action('init', 'aifc_register_formation_mode_taxonomy');


// Rendre certaines colonnes triables
add_filter('manage_edit-rt-project_sortable_columns', 'rt_formation_sortable_columns');
function rt_formation_sortable_columns($columns) {
    $columns['formation_prix'] = 'formation_prix';
    return $columns;
}


// Shortcode pour afficher une fiche formation complète
add_shortcode('fiche_formation', 'rt_fiche_formation_shortcode');
function rt_fiche_formation_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => get_the_ID(),
    ), $atts);
    
    $post_id = $atts['id'];
    $formation_data = rt_get_formation_data($post_id);
    
    if (empty($formation_data)) {
        return '<p>Aucune donnée de formation disponible.</p>';
    }
    
    ob_start();
    ?>
    <div class="fiche-formation-detailed">
        <!-- Description courte -->
        <?php if (!empty($formation_data['description_courte'])): ?>
        <div class="formation-section formation-description">
            <h3><span class="formation-icon">📌</span> Informations sur la Formation</h3>
            <p class="formation-desc"><?php echo esc_html($formation_data['description_courte']); ?></p>
        </div>
        <?php endif; ?>
        
        <div class="formation-grid-details">
            <!-- Catégorie -->
            <?php if (!empty($formation_data['categorie'])): ?>
            <div class="formation-detail-box">
                <div class="formation-detail-icon">📂</div>
                <div class="formation-detail-content">
                    <h4>Catégorie</h4>
                    <p><?php echo esc_html($formation_data['categorie']); ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Durée -->
            <?php if (!empty($formation_data['duree'])): ?>
            <div class="formation-detail-box">
                <div class="formation-detail-icon">⏳</div>
                <div class="formation-detail-content">
                    <h4>Durée</h4>
                    <p><?php echo esc_html($formation_data['duree']); ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Niveau -->
            <?php if (!empty($formation_data['niveau'])): ?>
            <div class="formation-detail-box">
                <div class="formation-detail-icon">🎓</div>
                <div class="formation-detail-content">
                    <h4>Niveau requis</h4>
                    <p><?php echo nl2br(esc_html($formation_data['niveau'])); ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Prix -->
            <?php if (!empty($formation_data['prix'])): ?>
            <div class="formation-detail-box formation-price-box">
                <div class="formation-detail-icon">💳</div>
                <div class="formation-detail-content">
                    <h4>Inscription</h4>
                    <p class="formation-price"><?php echo esc_html($formation_data['prix']); ?></p>
                    <?php if (!empty($formation_data['paiement'])): ?>
                    <p class="formation-paiement"><?php echo esc_html($formation_data['paiement']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Modules -->
        <?php if (!empty($formation_data['modules'])): ?>
        <div class="formation-section formation-modules">
            <h3><span class="formation-icon">📚</span> Modules de la formation</h3>
            <div class="modules-list">
                <?php 
                $modules = explode("\n", $formation_data['modules']);
                foreach ($modules as $module):
                    if (trim($module)): ?>
                    <div class="module-item">
                        <span class="module-bullet">•</span>
                        <span class="module-text"><?php echo esc_html(trim($module)); ?></span>
                    </div>
                    <?php endif;
                endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Public cible -->
        <?php if (!empty($formation_data['public'])): ?>
        <div class="formation-section formation-public">
            <h3><span class="formation-icon">💼</span> Public cible</h3>
            <p><?php echo nl2br(esc_html($formation_data['public'])); ?></p>
        </div>
        <?php endif; ?>
        
        <!-- Méthode pédagogique -->
        <?php if (!empty($formation_data['mode'])): ?>
        <div class="formation-section formation-methode">
            <h3><span class="formation-icon">📥</span> Méthode pédagogique</h3>
            <p><?php echo nl2br(esc_html($formation_data['mode'])); ?></p>
        </div>
        <?php endif; ?>
        
        <!-- Certification -->
        <?php if (!empty($formation_data['certification'])): ?>
        <div class="formation-section formation-certification">
            <h3><span class="formation-icon">🏅</span> Certification</h3>
            <p><?php echo esc_html($formation_data['certification']); ?></p>
        </div>
        <?php endif; ?>
        
        <!-- Prochaine rentrée -->
        <?php if (!empty($formation_data['prochaine_rentree'])): ?>
        <div class="formation-section formation-rentree">
            <h3><span class="formation-icon">📅</span> Prochaine rentrée</h3>
            <p><?php echo esc_html($formation_data['prochaine_rentree']); ?></p>
        </div>
        <?php endif; ?>
        
        <!-- Bouton de préinscription -->
        <?php $formation_data['lien_preinscription'] = !empty($formation_data['lien_preinscription']) ? $formation_data['lien_preinscription'] : "/preinscription-aux-formations-aifc/?_form=$post_id"  ?>
        <div class="formation-section formation-preinscription">
            <a href="<?php echo esc_url($formation_data['lien_preinscription']); ?>" 
               class="btn-formation-preinscription" 
               target="_blank">
               🔗 Préinscription - Réserver une place
            </a>
        </div>
    </div>
    <?php
    
    return ob_get_clean();
}

// Fonction helper pour récupérer toutes les données de formation
function rt_get_formation_data($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    return array(
        'description_courte' => get_post_meta($post_id, '_rt_formation_description_courte', true),
        /*'categorie' => get_post_meta($post_id, '_rt_formation_categorie', true),*/
        'duree' => get_post_meta($post_id, '_rt_formation_duree', true),
        'modules' => get_post_meta($post_id, '_rt_formation_modules', true),
        'niveau' => get_post_meta($post_id, '_rt_formation_niveau', true),
        'public' => get_post_meta($post_id, '_rt_formation_public', true),
        'prix' => get_post_meta($post_id, '_rt_formation_prix', true),
        'paiement' => get_post_meta($post_id, '_rt_formation_paiement', true),
        'mode' => get_post_meta($post_id, '_rt_formation_mode', true),
        'certification' => get_post_meta($post_id, '_rt_formation_certification', true),
        'prochaine_rentree' => get_post_meta($post_id, '_rt_formation_prochaine_rentree', true),
        'lien_preinscription' => get_post_meta($post_id, '_rt_formation_lien_preinscription', true),
    );
}

/**
 * Widget d'information de formation avec shortcode
 */
add_shortcode('widget_info_formation', 'rt_widget_info_formation_shortcode');
function rt_widget_info_formation_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => get_the_ID(),
        'show_title' => 'yes',
        'show_actions' => 'yes',
        'class' => '',
        'compact' => 'yes'
    ), $atts);
    
    $post_id = $atts['id'];
    $formation_data = rt_get_formation_data($post_id);
    $isCompact = $atts['compact'] === 'yes';
    
    // Si ce n'est pas une formation, on retourne rien
    if (get_post_type($post_id) !== 'rt-project') {
        return '';
    }
    
    ob_start();
    ?>
    <div class="formation-info-widget <?php echo esc_attr($atts['class']); ?>">
        <!-- Titre de la formation -->
        <?php 
            if ($atts['show_title'] === 'yes'): 
            $modes = get_the_terms($post->ID, 'formation_mode');

            ?>
        <div class="formation-widget-header">
            <?php if (!empty($modes) && !is_wp_error($modes)): ?>
                <div class="aifc-formation-modes">
                    <?php foreach ($modes as $mode): ?>
                        <span class="aifc-mode-tab">
                            <?php echo esc_html($mode->name); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <h3 class="formation-widget-title">
                <i class="icon-rt-book"></i>
                <?php echo get_the_title($post_id); ?>
            </h3>
        </div>
        <?php endif; ?>
        
        <div class="formation-widget-content">
            <!-- Description courte -->
            <?php if (!empty($formation_data['description_courte'])): ?>
            <div class="formation-widget-desc">
                <p><?php echo esc_html(wp_trim_words($formation_data['description_courte'], 20, '...')); ?></p>
                  <?php 
                    $methode_pedagogique = get_post_meta(get_the_ID(), '_rt_formation_mode', true);
                    if (!empty($methode_pedagogique)) : ?>
                        <p class="no-margin">  
                            <span class="compact-info-icon">📥</span>                  
                            <span class="info-tag info-methode">
                                Contenu: 
                                <strong><small><?php echo esc_html($methode_pedagogique); ?></small></strong>
                            </span>
                        </p>
                
                 <?php endif; ?>
                <?php if ($isCompact): ?>
                    <?php if (!empty($formation_data['certification'])): ?>
                        <p class="no-margin">  
                            <span class="compact-info-icon">🏅</span>                  
                            <span class="info-tag info-certif">
                                Certification: 
                                <strong><?php echo esc_html($formation_data['certification']); ?></strong>
                            </span>
                        </p>
                    <?php endif; ?>
                    <?php if (!empty($formation_data['niveau'])): ?>
                        <p class="no-margin">  
                            <span class="compact-info-icon">🎓</span>
                            <span class="info-tag info-niveau">
                                Niveau Requis: 
                                <strong><?php echo esc_html($formation_data['niveau']); ?></strong>
                            </span>
                        </p>
                    <?php endif; ?>
                    <?php if (!empty($formation_data['duree'])): ?>
                        <p class="no-margin">  
                            <span class="compact-info-icon">⏳</span>
                            <span class="info-tag info-duree">
                                Durée: 
                                <strong><?php echo esc_html($formation_data['duree']); ?></strong>
                            </span>
                        </p>
                    <?php endif; ?>
                    <?php if (!empty($formation_data['prix'])): ?>
                        <p class="no-margin">  
                            <span class="compact-info-icon">💳</span>
                            <span class="info-tag info-prix">
                                Coût: 
                                <strong><?php echo esc_html($formation_data['prix']); ?></strong>                                
                            </span>
                        </p>
                    <?php endif; ?>                    
                    <?php if (!empty($formation_data['paiement'])): ?>
                        <p class="no-margin">  
                            <span class="compact-info-icon">💳</span>
                            <span class="info-tag info-paiement">
                                Modalités: 
                                <strong><?php echo esc_html($formation_data['paiement']); ?></strong>                                
                            </span>
                        </p>
                    <?php endif; ?>                    
                    <?php if (!empty($formation_data['prochaine_rentree'])): ?>
                        <p class="no-margin">  
                            <span class="compact-info-icon">📅</span>
                            <span class="info-tag info-prochaine-rentree">
                                Prochaine Rentrée: 
                                <strong><?php echo esc_html($formation_data['prochaine_rentree']); ?></strong>  
                            </span>
                        </p>
                    <?php endif; ?>                    
                </p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Informations principales -->
            <div class="formation-widget-infos">
                <!-- Catégorie -->
                <?php if (!empty($formation_data['categorie']) && !$isCompact): ?>
                <div class="formation-widget-info-item">
                    <span class="info-icon">📂</span>
                    <div class="info-content">
                        <span class="info-label">Catégorie</span>
                        <span class="info-value"><?php echo esc_html($formation_data['categorie']); ?></span>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Durée -->
                <?php if (!empty($formation_data['duree']) && !$isCompact): ?>
                <div class="formation-widget-info-item">
                    <span class="info-icon">⏳</span>
                    <div class="info-content">
                        <span class="info-label">Durée</span>
                        <span class="info-value"><?php echo esc_html($formation_data['duree']); ?></span>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Niveau -->
                <?php if (!empty($formation_data['niveau']) && !$isCompact): ?>
                <div class="formation-widget-info-item">
                    <span class="info-icon">🎓</span>
                    <div class="info-content">
                        <span class="info-label">Niveau requis</span>
                        <span class="info-value"><?php echo esc_html(wp_trim_words($formation_data['niveau'], 5, '...')); ?></span>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Prochaine rentrée -->
                <?php if (!empty($formation_data['prochaine_rentree']) && !$isCompact): ?>
                <div class="formation-widget-info-item">
                    <span class="info-icon">📅</span>
                    <div class="info-content">
                        <span class="info-label">Prochaine rentrée</span>
                        <span class="info-value"><?php echo esc_html($formation_data['prochaine_rentree']); ?></span>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Prix -->
                <?php if (!empty($formation_data['prix']) && !$isCompact): ?>
                <div class="formation-widget-info-item formation-price">
                    <span class="info-icon">💳</span>
                    <div class="info-content">
                        <span class="info-label">Coût de la formation</span>
                        <span class="info-value"><?php echo esc_html($formation_data['prix']); ?></span>
                        <?php if (!empty($formation_data['paiement'])): ?>
                        <span class="info-subtext"><?php echo esc_html($formation_data['paiement']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Certification -->
                <?php if (!empty($formation_data['certification']) && !$isCompact): ?>
                <div class="formation-widget-info-item">
                    <span class="info-icon">🏅</span>
                    <div class="info-content">
                        <span class="info-label">Certification</span>
                        <span class="info-value"><?php echo esc_html($formation_data['certification']); ?></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Appels à l'action -->
            <?php if ($atts['show_actions'] === 'yes'): ?>
            <div class="formation-widget-actions">
                <!--div class="formation-actions-header">
                    <h4>Vous souhaitez rejoindre cette formation ?</h4>
                </div-->
                
                <div class="formation-actions-list">
                    <!-- Préinscription -->
                    <?php $formation_data['lien_preinscription'] = !empty($formation_data['lien_preinscription']) ? $formation_data['lien_preinscription'] : "/preinscription-aux-formations-aifc/?_form=$post_id"  ?>
                    <a href="<?php echo esc_url($formation_data['lien_preinscription']); ?>" 
                       class="formation-action-btn btn-preinscription" 
                       target="_blank">
                        <span class="action-icon">📝</span>
                        <span class="action-text">
                            <strong>Remplir le formulaire de préinscription</strong>
                            <small>Réservez votre place dès maintenant</small>
                        </span>
                        <span class="action-arrow">→</span>
                    </a>
                    
                    
                    <!-- Brochure détaillée -->
                   <a href="#" id="open-brochure-modal" class="formation-action-btn btn-brochure">
                        <span class="action-icon">📄</span>
                        <span class="action-text">
                            <strong>Recevez la brochure détaillée</strong>
                            <small>Programme complet et modalités</small>
                        </span>
                        <span class="action-arrow">→</span>
                    </a>

                    
                    <!-- Échange avec conseiller -->
                    <a id="contact-form-conseiller" href="#" 
                       class="formation-action-btn btn-conseiller">
                       <span class="action-icon">👨‍💼</span>
                        <span class="action-text">
                            <strong>Échangez avec un conseiller AIFC</strong>
                            <small>Orientation personnalisée</small>
                        </span>
                        <span class="action-arrow">→</span>
                    </a>
                </div>
                
                <!-- Note importante -->
                <div class="formation-action-note">
                    <p><strong>⚠️ Places limitées</strong> - Les inscriptions sont ouvertes jusqu'à épuisement des places disponibles.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('contact-form-conseiller');

        if (!btn) return;

        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const phone = '<?php echo esc_js(get_post_meta(get_the_ID(), '_rt_formation_conseiller_phone', true)) ?: "237654160386"; ?>';
            const formationTitre = '<?php echo esc_js(get_the_title()); ?>';
            const message = encodeURIComponent(
                `Bonjour AIFC, je souhaite échanger avec un conseiller concernant la formation: ${formationTitre}`
            );

            const whatsappUrl = `https://wa.me/${phone}?text=${message}`;
            window.open(whatsappUrl, '_blank');
        });
    });
    </script>
    <?php
    
    return ob_get_clean();
}

/**
 * Formulaire de contact pour brochure (à ajouter dans la page)
 */
add_shortcode('formulaire_brochure', 'rt_formulaire_brochure_shortcode');
function rt_formulaire_brochure_shortcode() {
    ob_start();
    ?>
    <div id="contact-form-brochure" class="formation-contact-form">
        <!--h3>Demander la brochure détaillée</h3-->
        
        <p>Recevez le programme complet, les modalités d'inscription et toutes les informations sur cette formation (un conseiller pourrait aussi prendre contact pour vous apportez des éclairages complémentaires).</p>
        
        <?php
        if (!shortcode_exists('gravityform')) {
            echo "Gravity Form needs to be setted -- (functions.php in finwave child)";
        }else{
            $id = get_the_ID();
            echo do_shortcode('[gravityform id="2" title="false" field_values="formation=$id" ]');
        }
       /* } else {
            // Fallback HTML
            ?>
            <form action="#" method="post" class="formation-brochure-form">
                <div class="form-group">
                    <label class="label-name">Nom:</label>
                    <input type="text" name="nom" placeholder="Votre nom complet" required>
                </div>
                <div class="form-group">
                    <label class="label-mail">Email:</label>
                    <input type="email" namNome="email" placeholder="Votre adresse email" required>
                </div>
                <div class="form-group">
                    <label class="label-phone">Téléphone (whatsapp recommandée):</label>
                    <input type="tel" name="telephone" placeholder="Votre numéro de téléphone">
                </div>
                <div class="form-group">
                    <label class="label-name">Message:</label>
                    <textarea name="message" placeholder="Votre message ou questions" rows="4"></textarea>
                </div>
                <input type="hidden" name="formation" value="<?php echo get_the_title(); ?>">
                <button id="ask_brochure_btn" type="submit" class="btn-submit">Envoyer la demande</button>
            </form>
            <?php
        }*/
        ?>
    </div>
    <?php
    return ob_get_clean();
}


add_filter( 'gform_field_value_formation', 'populate_formation_field' );
function populate_formation_field() {
    // Retrieve the value generated by your custom shortcode
    return get_the_ID();
}

/**
 * CPT : Événements AIFC
 */
function aifc_register_event_cpt() {
    register_post_type('evenement_aifc', [
        'label' => 'Événements AIFC',
        'public' => true,
        'menu_icon' => 'dashicons-calendar-alt',
        'supports' => ['title', 'editor', 'thumbnail'],
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'evenements-aifc'],
    ]);
}
add_action('init', 'aifc_register_event_cpt');

function aifc_get_next_event() {
    $query = new WP_Query([
        'post_type' => 'evenement_aifc',
        'meta_key' => 'evenement_actif',
        'meta_value' => '1',
        'posts_per_page' => 1
    ]);

    return $query->have_posts() ? $query->posts[0] : null;
}

/**
 * Résout l'événement AIFC à utiliser
 * Priorité :
 * 1. post_id passé au shortcode
 * 2. contexte single-evenement_aifc
 * 3. événement actif
 */
function aifc_resolve_event($atts = []) {

    // 1️⃣ post_id explicite
    if (!empty($atts['post_id'])) {
        $post = get_post((int) $atts['post_id']);
        if ($post && $post->post_type === 'evenement_aifc') {
            return $post;
        }
    }

    // 2️⃣ Contexte single
    if (is_singular('evenement_aifc')) {
        return get_post();
    }

    // 3️⃣ Événement actif
    return aifc_get_next_event();
}


/**
 * Détermine si le contexte courant est un événement AIFC
 */
function aifc_is_event_context() {
    if (is_singular('evenement_aifc')) {
        return true;
    }

    // Cas page "prochaine édition" (par slug ou ID)
    if (is_page() && get_post_field('post_name', get_the_ID()) === 'prochaine-edition') {
        return true;
    }

    return false;
}

/**
 * Shortcode Slider amélioré
 */
add_shortcode('aifc_event_slider', function ($atts) {
    $atts = shortcode_atts([
        'post_id' => null,
        'autoplay' => '5000',
        'navigation' => 'true',
        'pagination' => 'true',
        'effect' => 'slide',
    ], $atts);

    $event = aifc_resolve_event($atts);
    if (!$event) return '<p class="aifc-no-slider">Aucun événement à afficher.</p>';

    $gallery = [];
    for ($i = 1; $i <= 5; $i++) {
        $img = get_field("image_evenement_$i", $event->ID);
        if ($img) $gallery[] = $img;
    }

    if (empty($gallery)) {
        // Image par défaut
        return '<div class="aifc-event-slider-container">
            <div class="aifc-event-slider swiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <img src="' . get_template_directory_uri() . '/assets/default-event.jpg" alt="Événement AIFC">
                        <div class="aifc-slider-overlay">
                            <h3>' . esc_html($event->post_title) . '</h3>
                            <p>Prochain événement AIFC</p>
                        </div>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>';
    }

    ob_start(); ?>
    
    <div class="aifc-event-slider-container">
        <div class="aifc-event-slider swiper"
             data-autoplay="<?php echo esc_attr($atts['autoplay']); ?>"
             data-effect="<?php echo esc_attr($atts['effect']); ?>">
            
            <div class="swiper-wrapper">
                <?php foreach ($gallery as $index => $img): ?>
                    <div class="swiper-slide">
                        <img src="<?= esc_url($img['url']); ?>" 
                             alt="<?= esc_attr($img['alt'] ?: 'Image événement AIFC'); ?>">
                        <div class="aifc-slider-overlay">
                            <h3><?= esc_html($event->post_title); ?></h3>
                            <p>Image <?= $index + 1; ?> - <?= esc_html($img['caption'] ?: 'Événement AIFC'); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($atts['pagination'] === 'true'): ?>
                <div class="swiper-pagination"></div>
            <?php endif; ?>
            
            <?php if ($atts['navigation'] === 'true'): ?>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php
    return ob_get_clean();
});

/**
 * Shortcode CTA amélioré
 */
add_shortcode('aifc_event_cta', function ($atts) {
    $atts = shortcode_atts([
        'post_id' => null,
        'title' => 'Réservez votre place',
        'show_note' => 'true',
    ], $atts);

    $event = aifc_resolve_event($atts);
    if (!$event) return '';

    $preinscription = get_field('cta_preinscription', $event->ID);
    $reservation = get_field('cta_reservation', $event->ID);
    $brochure = get_field('cta_brochure', $event->ID);
    $conseiller = get_field('cta_conseiller', $event->ID);
    
    // Liens personnalisables
    $link_preinscription = get_field('lien_preinscription', $event->ID) ?: '#preinscription';
    $link_reservation = get_field('lien_reservation', $event->ID) ?: '#reservation';
    $link_brochure = get_field('lien_brochure', $event->ID) ?: '#brochure';
    $link_conseiller = get_field('lien_conseiller', $event->ID) ?: '#contact-form-conseiller';
    
    $whatsapp_number = get_field('whatsapp_conseiller', $event->ID) ?: '237654160386';
    
    ob_start(); ?>
    
    <div class="aifc-event-cta">
        <h3><?= esc_html($atts['title']); ?></h3>
        
        <div class="aifc-cta-buttons">
            <?php if ($preinscription): ?>
                <a href="<?= esc_url($link_preinscription); ?>" 
                   class="aifc-cta-btn aifc-cta-btn-primary"
                   target="_blank">
                    <span class="aifc-cta-icon">📝</span>
                    <span>Préinscription en ligne</span>
                </a>
            <?php endif; ?>
            
            <?php if ($reservation): ?>
                <a href="<?= esc_url($link_reservation); ?>" 
                   class="aifc-cta-btn aifc-cta-btn-secondary"
                   target="_blank">
                    <span class="aifc-cta-icon">🎟️</span>
                    <span>Réserver une place</span>
                </a>
            <?php endif; ?>
            
            <?php if ($brochure): ?>
                <a href="<?= esc_url($link_brochure); ?>" 
                   class="aifc-cta-btn aifc-cta-btn-outline"
                   target="_blank">
                    <span class="aifc-cta-icon">📄</span>
                    <span>Télécharger la brochure</span>
                </a>
            <?php endif; ?>
            
            <?php if ($conseiller): ?>
                <a href="https://wa.me/<?= esc_attr($whatsapp_number); ?>?text=<?= urlencode('Bonjour AIFC, je souhaite échanger avec un conseiller concernant votre événement : ' . get_the_title($event->ID)); ?>" 
                   class="aifc-cta-btn aifc-cta-btn-whatsapp"
                   target="_blank">
                    <span class="aifc-cta-icon">💬</span>
                    <span>Échanger avec un conseiller</span>
                </a>
            <?php endif; ?>
        </div>
        
        <?php if ($atts['show_note'] === 'true'): ?>
            <div class="aifc-cta-note">
                <p><strong>⚠️ Places limitées</strong><br>
                Les inscriptions sont traitées par ordre d'arrivée.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <?php
    return ob_get_clean();
});

/**
 * Shortcode Contenu amélioré
 */
add_shortcode('aifc_event_content', function ($atts) {
    $atts = shortcode_atts([
        'post_id' => null,
        'show_title' => 'true',
        'show_meta' => 'true',
        'show_theme' => 'true',
        'show_resume' => 'true',
        'show_countdown' => 'true',
    ], $atts);

    $event = aifc_resolve_event($atts);
    if (!$event) return '';

    setup_postdata($event);
    
    $periode = get_field('periode_evenement', $event->ID);
    $lieu = get_field('lieu_evenement', $event->ID);
    $theme = get_field('theme_evenement', $event->ID);
    $resume = get_field('resume_evenement', $event->ID);
    $date_debut = get_field('date_debut', $event->ID);
    $date_fin = get_field('date_fin', $event->ID);
    
    ob_start(); ?>
    
    <div class="aifc-event-content">
        <?php if ($atts['show_title'] === 'true'): ?>
        <div class="aifc-event-header">
            <h1 class="aifc-event-title"><?= esc_html($event->post_title); ?></h1>
            <?php if ($theme && $atts['show_theme'] === 'true'): ?>
                <p class="aifc-event-subtitle">Thème : <?= esc_html($theme); ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($atts['show_meta'] === 'true' && ($periode || $lieu)): ?>
        <div class="aifc-event-meta-grid">
            <?php if ($periode): ?>
            <div class="aifc-meta-card">
                <span class="aifc-meta-icon">📅</span>
                <span class="aifc-meta-label">Période</span>
                <span class="aifc-meta-value"><?= esc_html($periode); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ($lieu): ?>
            <div class="aifc-meta-card">
                <span class="aifc-meta-icon">📍</span>
                <span class="aifc-meta-label">Lieu</span>
                <span class="aifc-meta-value"><?= esc_html($lieu); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ($date_debut && $date_fin && $atts['show_countdown'] === 'true'): ?>
            <div class="aifc-meta-card">
                <span class="aifc-meta-icon">⏳</span>
                <span class="aifc-meta-label">Début dans</span>
                <span class="aifc-meta-value aifc-countdown-trigger" 
                      data-date="<?= esc_attr($date_debut); ?>">
                    Calcul en cours...
                </span>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($theme && $atts['show_theme'] === 'true'): ?>
        <div class="aifc-event-theme">
            <h3>Thème de l'événement</h3>
            <p><?= esc_html($theme); ?></p>
        </div>
        <?php endif; ?>
        
        <?php if ($resume && $atts['show_resume'] === 'true'): ?>
        <div class="aifc-event-resume">
            <?= wp_kses_post($resume); ?>
        </div>
        <?php endif; ?>
        
        <div class="aifc-event-description">
            <?= apply_filters('the_content', $event->post_content); ?>
        </div>
        
        <?php if ($date_debut && $date_fin && $atts['show_countdown'] === 'true'): ?>
        <div class="aifc-countdown">
            <h3 class="aifc-countdown-title">🚀 L'événement commence dans :</h3>
            <div class="aifc-countdown-timer" 
                 data-date="<?= esc_attr($date_debut); ?>">
                <div class="aifc-countdown-item">
                    <span class="aifc-countdown-number" data-days>00</span>
                    <span class="aifc-countdown-label">Jours</span>
                </div>
                <div class="aifc-countdown-item">
                    <span class="aifc-countdown-number" data-hours>00</span>
                    <span class="aifc-countdown-label">Heures</span>
                </div>
                <div class="aifc-countdown-item">
                    <span class="aifc-countdown-number" data-minutes>00</span>
                    <span class="aifc-countdown-label">Minutes</span>
                </div>
                <div class="aifc-countdown-item">
                    <span class="aifc-countdown-number" data-seconds>00</span>
                    <span class="aifc-countdown-label">Secondes</span>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="aifc-event-actions">
            <a href="#preinscription" class="aifc-cta-btn aifc-cta-btn-primary">
                S'inscrire maintenant
            </a>
            <a href="#brochure" class="aifc-cta-btn aifc-cta-btn-outline">
                Télécharger le programme
            </a>
            <a href="#contact" class="aifc-cta-btn aifc-cta-btn-secondary">
                Nous contacter
            </a>
        </div>
    </div>
    
    <?php
    wp_reset_postdata();
    return ob_get_clean();
});


/**
 * Assure qu'un seul événement AIFC est actif à la fois
 */
add_action('acf/save_post', 'aifc_ensure_single_active_event', 20);
function aifc_ensure_single_active_event($post_id) {

    // Sécurité : ignorer autosave / révisions
    if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
        return;
    }

    // Vérifier le post type
    if (get_post_type($post_id) !== 'evenement_aifc') {
        return;
    }

    // Vérifier si l'événement courant est actif
    $is_active = get_field('evenement_actif', $post_id);
    if (!$is_active) {
        return;
    }

    // Désactiver tous les autres événements
    $args = [
        'post_type'      => 'evenement_aifc',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'post__not_in'   => [$post_id],
        'fields'         => 'ids',
    ];

    $events = get_posts($args);

    foreach ($events as $event_id) {
        update_field('evenement_actif', false, $event_id);
    }
}



/**
 * Formulaire de contact pour conseiller
 */
add_shortcode('formulaire_conseiller', 'rt_formulaire_conseiller_shortcode');
function rt_formulaire_conseiller_shortcode() {
    ob_start();
    ?>
    <div id="contact-form-conseiller" class="formation-contact-form">
        <h3>Échanger avec un conseiller AIFC</h3>
        <p>Un conseiller vous contactera pour discuter de votre projet de formation et vous orienter.</p>
        
        <?php 
        if (shortcode_exists('contact-form-7')) {
            echo do_shortcode('[contact-form-7 id="124" title="Contact conseiller"]');
        } else {
            ?>
            <form action="#" method="post" class="formation-conseiller-form">
                <div class="form-group">
                    <input type="text" name="nom" placeholder="Votre nom complet" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Votre adresse email" required>
                </div>
                <div class="form-group">
                    <input type="tel" name="telephone" placeholder="Votre numéro de téléphone" required>
                </div>
                <div class="form-group">
                    <select name="sujet" required>
                        <option value="">Sujet de votre demande</option>
                        <option value="orientation">Orientation et conseil</option>
                        <option value="financement">Financement de la formation</option>
                        <option value="programme">Questions sur le programme</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>
                <div class="form-group">
                    <textarea name="message" placeholder="Décrivez votre projet et vos questions" rows="5" required></textarea>
                </div>
                <input type="hidden" name="formation" value="<?php echo get_the_title(); ?>">
                <button type="submit" class="btn-submit">Demander un échange</button>
            </form>
            <?php
        }
        ?>
    </div>
    <?php
    return ob_get_clean();
}


// Ajouter du CSS pour les formations
add_action('wp_head', 'rt_formation_custom_styles');
function rt_formation_custom_styles() {
    if (get_post_type() === 'rt-project') {
        ?>
        <style>
            /* Style pour la fiche formation */
            .fiche-formation-detailed {
                background: #f9f9f9;
                border-radius: 10px;
                padding: 30px;
                margin: 20px 0;
            }
            
            .formation-section {
                margin-bottom: 30px;
                padding-bottom: 20px;
                border-bottom: 1px solid #eee;
            }
            
            .formation-section h3 {
                color: #2c3e50;
                margin-bottom: 15px;
                font-size: 1.3em;
            }
            
            .formation-icon {
                margin-right: 10px;
            }
            
            .formation-grid-details {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
                margin: 25px 0;
            }
            
            .formation-detail-box {
                background: white;
                border-radius: 8px;
                padding: 20px;
                border-left: 4px solid #3498db;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }
            
            .formation-price-box {
                border-left-color: #e74c3c;
            }
            
            .formation-detail-icon {
                font-size: 24px;
                margin-bottom: 10px;
            }
            
            .formation-detail-content h4 {
                margin: 0 0 8px 0;
                color: #2c3e50;
                font-size: 1em;
            }
            
            .formation-price {
                font-size: 1.4em;
                font-weight: bold;
                color: #e74c3c;
                margin: 5px 0;
            }
            
            .formation-paiement {
                color: #7f8c8d;
                font-size: 0.9em;
            }
            
            .modules-list {
                display: grid;
                gap: 12px;
            }
            
            .module-item {
                display: flex;
                align-items: flex-start;
                background: white;
                padding: 12px 15px;
                border-radius: 6px;
                border-left: 3px solid #9b59b6;
            }
            
            .module-bullet {
                color: #9b59b6;
                margin-right: 10px;
                font-weight: bold;
            }
            
            .btn-formation-preinscription {
                display: inline-block;
                background: linear-gradient(135deg, #f6f6f7ff, #fbfbfcff);
                color: black;
                padding: 15px 30px;
                border-radius: 50px;
                text-decoration: none;
                font-weight: bold;
                font-size: 1.1em;
                text-align: center;
                transition: all 0.3s;
                border: none;
            }
            
            .btn-formation-preinscription:hover {
                background: linear-gradient(135deg, #f6f6f7ff, #fbfbfcff);
                transform: translateY(-2px);
                box-shadow: 0 5px 15px #3dad58;
            }
            
            /* Pour l'archive/liste des formations */
            .formation-card {
                background: white;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 3px 10px rgba(0,0,0,0.1);
                transition: transform 0.3s;
            }
            
            .formation-card:hover {
                transform: translateY(-5px);
            }
            
            .formation-badge {
                display: inline-block;
                background: #3498db;
                color: white;
                padding: 3px 10px;
                border-radius: 3px;
                font-size: 0.8em;
                margin-right: 5px;
                margin-bottom: 5px;
            }           
            .finwave-breadcrumb-wrapper .entry-title{
                font-size: 2em;
            }
            .custom-swal-title-class{
                color: #085247;
            }
            #contact-form-brochure-modal{
                font-family: poppins;
            }
            
        </style>
        <?php
    }
}

// Filtrer les textes dans les templates
add_filter('gettext', 'rt_replace_project_texts', 20, 3);
function rt_replace_project_texts($translated_text, $text, $domain) {
    if ($domain === 'finwave') {
        $replacements = array(
            'Project' => 'Formation',
            'project' => 'formation',
            'Projects' => 'Formations',
            'projects' => 'formations',
            'Related Projects' => 'Formations similaires',
            'Project Category' => 'Catégorie de formation',
        );
        
        if (isset($replacements[$text])) {
            return $replacements[$text];
        }
    }
    return $translated_text;
}


// Enqueue le script sticky menu (désactivé sur mobile)
add_action('wp_enqueue_scripts', 'enqueue_sticky_menu_script');
function enqueue_sticky_menu_script() {
    wp_enqueue_script(
        'sticky-menu',
        get_stylesheet_directory_uri() . '/js/sticky-menu.js',
        array('jquery'),
        '1.0.1', // Version mise à jour
        true
    );
    
    // Variables à passer au script
    wp_localize_script('sticky-menu', 'stickyMenuVars', array(
        'selector' => '#masthead', // Ajustez selon votre thème
        'offset' => 100,
        'adminBar' => true,
        'mobileBreakpoint' => 768 // Désactiver en dessous de 768px
    ));
}

// Enqueue script for services vertical menu
add_action('wp_enqueue_scripts', 'enqueue_accordeon_click_menu_script');
function enqueue_accordeon_click_menu_script() {
    wp_enqueue_script(
        'accordeon-click-menu',
        get_stylesheet_directory_uri() . '/js/accordeon-click-menu.js',
        array('jquery'),
        '1.0.0',
        true
    );
    
    // Variables à passer au script
    wp_localize_script('sticky-menu', 'stickyMenuVars', array(
        'selector' => '#masthead', // Ajustez selon votre thème
        'offset' => 100,
        'adminBar' => true
    ));
}

// Enqueue les scripts pour les événements
add_action('wp_enqueue_scripts', 'aifc_event_scripts');
function aifc_event_scripts() {
    if (is_singular('evenement_aifc') || aifc_is_event_context()) {
        // Swiper JS (si non déjà chargé)
        if (!wp_script_is('swiper', 'enqueued')) {
            wp_enqueue_script(
                'swiper',
                'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js',
                array(),
                '8.4.5',
                true
            );
            wp_enqueue_style(
                'swiper-css',
                'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css',
                array(),
                '8.4.5'
            );
        }
        
        // Script personnalisé
        wp_enqueue_script(
            'aifc-events',
            get_stylesheet_directory_uri() . '/js/aifc-events.js',
            array('jquery', 'swiper'),
            '1.0.0',
            true
        );
    }
}

add_action('wp_enqueue_scripts', function () {

    // SweetAlert2 (CDN simple)
    wp_enqueue_script(
        'sweetalert2',
        'https://cdn.jsdelivr.net/npm/sweetalert2@11',
        array(),
        null,
        true
    );

    // Ton script d'ouverture de modal (inline)
    $js = <<<JS
            document.addEventListener('DOMContentLoaded', function () {
            function openBrochureModal() {
                const source = document.getElementById('contact-form-brochure');
                if (!source) {
                    console.warn('Formulaire brochure introuvable (#contact-form-brochure). Ajoute le shortcode [formulaire_brochure] sur la page.');
                    return;
                }

                const clone = source.cloneNode(true);
                clone.style.display = 'block';
                clone.id = 'contact-form-brochure-modal';

                Swal.fire({
                    title: 'Demander la brochure détaillée',
                    html: clone,
                    showCloseButton: true,
                    showConfirmButton: false,
                    width: '760px',
                    padding: '1.2rem',
                    focusConfirm: false,
                    didOpen: () => {
                        // Réinitialisation Gravity Forms dans le modal
                        if (window.gform && typeof window.gform.initializeOnLoaded === 'function') {
                            window.gform.initializeOnLoaded();
                            window.gform.initializeOnReady();
                        }
                    }
                });
            }
    

            // 🔁 CAS 1 : clic utilisateur
            const openBtn = document.getElementById('open-brochure-modal');
            if (openBtn) {
                openBtn.addEventListener('click', function(e){
                    e.preventDefault();
                    openBrochureModal();
                });
            }

            // 🔁 CAS 2 : retour Gravity Forms avec ancre #gf_X
            if (window.location.hash && window.location.hash.startsWith('#gf_')) {
                // Petit délai pour laisser GF reconstruire le DOM
                setTimeout(function(){
                    openBrochureModal();
                }, 400);
            }

        });
    JS;

    wp_add_inline_script('sweetalert2', $js);
});



add_action('wp_head', 'rt_widget_formation_styles');
function rt_widget_formation_styles() {
    if (is_singular('rt-project')) {
        ?>
        <style>
            /* Widget d'information de formation */
            .formation-info-widget {
                background: #ffffff;
                border-radius: 12px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                border: 1px solid #eaeaea;
                overflow: hidden;
                margin-bottom: 30px;
                transition: all 0.3s ease;
            }
            
            .formation-info-widget:hover {
                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
                transform: translateY(-2px);
            }
            
            .formation-widget-header {
                background: linear-gradient(135deg, #028140, #085247);
                color: white;
                padding: 20px;
                border-bottom: 3px solid #028140;
            }
            
            .formation-widget-title {
                margin: 0;
                font-size: 1.3em;
                display: flex;
                align-items: center;
                gap: 10px;
                color: white;
                font-size: 0.85em;
                font-weight: unset;
            }
            
            .formation-widget-title .icon-rt-book {
                color: #028140;
                font-size: 1.2em;
            }
            
            .formation-widget-content {
                padding: 25px;
            }
            
            .formation-widget-desc {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 15px;
                margin-bottom: 0px;
                border-left: 5px #028140 solid;
                font-style: normal;
                color: #555;
                font-size: medium;
            }
            
            .formation-widget-infos {
                display: flex;
                flex-direction: column;
                gap: 15px;
                margin-bottom: 30px;
            }
            
            .formation-widget-info-item {
                display: flex;
                align-items: center;
                gap: 15px;
                padding: 12px 15px;
                background: #f8f9fa;
                border-radius: 8px;
                transition: background 0.3s;
            }
            
            .formation-widget-info-item:hover {
                background: #e9ecef;
            }
            
            .formation-price {
                background: linear-gradient(135deg, #fff5f5, #ffe6e6);
                border: 1px solid #ffcccc;
            }
            
            .info-icon {
                font-size: 20px;
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: white;
                border-radius: 50%;
                box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            }
            
            .info-content {
                flex: 1;
            }
            
            .info-label {
                display: block;
                font-size: 0.85em;
                color: #7f8c8d;
                margin-bottom: 3px;
                font-weight: 500;
            }
            
            .info-value {
                display: block;
                font-weight: 600;
                color: #2c3e50;
                font-size: 1em;
            }
            
            .info-subtext {
                display: block;
                font-size: 0.8em;
                color: #e74c3c;
                margin-top: 3px;
                font-style: italic;
            }
            
            /* Section des appels à l'action */
            .formation-widget-actions {
                background: linear-gradient(135deg, #f8f9fa, #e9ecef);
                border-radius: 10px;
                /*padding: 20px;
                margin-top: 10px;*/
                border: 1px solid #dee2e6;
            }
            
            .formation-actions-header {
                text-align: center;
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 2px dashed #028140;
            }
            
            .formation-actions-header h4 {
                margin: 0;
                color: #085247;
                font-size: 1.2em;
            }
            
            .formation-actions-list {
                display: flex;
                flex-direction: column;
                gap: 12px;
                margin-bottom: 20px;
                max-height: 200px;
                overflow: scroll;
            }
            
            .formation-action-btn {
                display: flex;
                align-items: center;
                gap: 15px;
                /*padding: 15px;*/
                background: white;
                border-radius: 8px;
                text-decoration: none;
                color: inherit;
                border: 2px solid transparent;
                transition: all 0.3s;
                position: relative;
            }
            
            .formation-action-btn:hover {
                border-color: #3dad58;
                color: #085247;
            }
            
            .btn-preinscription:hover {
                border-color: #3dad58;
                color: #085247;
            }
            
            .btn-brochure:hover {
                border-color: #3dad58;
                color: #085247;
            }
            
            .btn-conseiller:hover {
                border-color: #3dad58;
                color: #085247;
            }
            
            .btn-phone:hover {
                 border-color: #3dad58;
                color: #085247;
            }
            
            .action-icon {
                font-size: 24px;
                width: 50px;
                height: 50px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #f8f9fa;
                border-radius: 50%;
            }
            
            .action-text {
                flex: 1;
            }
            
            .action-text strong {
                display: block;
                color: #2c3e50;
                font-size: 1em;
                margin-bottom: 3px;
            }
            
            .action-text small {
                display: block;
                color: #7f8c8d;
                font-size: 0.85em;
            }
            
            .action-arrow {
                color: #085247;
                font-size: 1.5em;
                font-weight: bold;
                opacity: 0;
                transition: opacity 0.3s;
            }
            
            .formation-action-btn:hover .action-arrow {
                opacity: 1;
            }

            .formation-action-btn:hover{
                 background-color: #e7fde7;
            }
           
            .formation-action-btn:hover .action-text strong,
            .formation-action-btn:hover .action-text small {
                color: #085247;
                font-weight: bolder;               
            }
            
            .formation-action-note {
                background: #e7fde7;
                border: 1px solid #e7fde7;
                border-radius: 6px;
                padding: 12px 15px;
                text-align: center;            
            }
            
            .formation-action-note p {
                margin: 0;
                color: #085247;
                font-size: 0.9em;
            }
            
            .formation-action-note strong {
                color: #856404;
            }

            .no-margin {
               margin: 0;
            }

            .compact-info-icon {
                background: white;
                padding: 3px;
                font-style: normal;
            }

            .wp-block-accordion-heading__toggle-title {
                flex: 1;
                max-width: 600px;
                background-color: #efefef;
                color: #085247;
            }

            .wp-block-accordion h3 {
                max-width: 600px;
                font-weight: 300;
                /*margin-bottom: 20px;*/
            }

            .content-area{
               padding-top: 70px;
                padding-bottom: 70px;
            }

            .aifc-formation-modes {
                display: flex;
                gap: 8px;
                margin-bottom: 10px;
                position: relative;
            }

            .aifc-mode-tab {
                background: #085247;
                color: #ffffff;
                font-size: 12px;
                font-weight: 500;
                padding: 4px 10px;
                border-radius: 4px;
                white-space: nowrap;
            }

            #contact-form-brochure {
               display: none;
            }

            #swal2-html-container{
                font-family: poppins;
            }

           .gf_progressbar .percentbar_blue, .gform_button{
                background-color: #028140 !important;
           }

           .aifc-icon {
                height: 28px;
                display: inline flex;
                color: #028140;
                margin-top: 5px;
            }

            
            /* Styles responsifs */
            @media (max-width: 768px) {
                .formation-widget-title {
                    font-size: 1.1em;
                }
                
                .formation-widget-info-item {
                    padding: 10px;
                }
                
                .formation-action-btn {
                    padding: 12px;
                }
                
                .action-icon {
                    width: 40px;
                    height: 40px;
                    font-size: 20px;
                }


            }
        </style>
        <?php
    }

    if (is_singular('evenement_aifc') || aifc_is_event_context()) :
        ?>
        <style>
            /* Layout principal */
            .aifc-event-layout {
                display: grid;
                grid-template-columns: 1fr;
                gap: 40px;
                max-width: 1200px;
                margin: 0 auto;
                padding: 30px 20px;
            }
            
            @media (min-width: 992px) {
                .aifc-event-layout {
                    grid-template-columns: 300px 1fr;
                }
            }
            
            /* Sidebar CTA */
            .aifc-event-sidebar {
                position: sticky;
                top: 100px;
                height: fit-content;
            }
            
            .aifc-event-cta {
                background: linear-gradient(135deg, #085247, #028140);
                border-radius: 15px;
                padding: 25px;
                color: white;
                box-shadow: 0 10px 30px rgba(8, 82, 71, 0.2);
                border: 2px solid rgba(255, 255, 255, 0.1);
            }
            
            .aifc-event-cta h3 {
                color: white;
                margin-top: 0;
                font-size: 1.5em;
                border-bottom: 2px solid rgba(255, 255, 255, 0.2);
                padding-bottom: 15px;
                margin-bottom: 20px;
            }
            
            .aifc-cta-buttons {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }
            
            .aifc-cta-btn {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 12px;
                padding: 16px 20px;
                border-radius: 10px;
                text-decoration: none;
                font-weight: 600;
                font-size: 0.95em;
                transition: all 0.3s ease;
                text-align: center;
                border: none;
                cursor: pointer;
                width: 100%;
            }
            
            .aifc-cta-btn-primary {
                background: white;
                color: #085247;
                box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
            }
            
            .aifc-cta-btn-primary:hover {
                background: #f8f9fa;
                transform: translateY(-3px);
                box-shadow: 0 6px 20px rgba(255, 255, 255, 0.3);
            }
            
            .aifc-cta-btn-secondary {
                background: rgba(255, 255, 255, 0.1);
                color: white;
                border: 2px solid rgba(255, 255, 255, 0.3);
            }
            
            .aifc-cta-btn-secondary:hover {
                background: rgba(255, 255, 255, 0.2);
                transform: translateY(-3px);
                border-color: white;
            }
            
            .aifc-cta-btn-whatsapp {
                background: #25D366;
                color: white;
                position: relative;
                overflow: hidden;
            }
            
            .aifc-cta-btn-whatsapp:hover {
                background: #128C7E;
                transform: translateY(-3px);
            }
            
            .aifc-cta-btn-outline {
                background: transparent;
                color: white;
                border: 2px solid rgba(255, 255, 255, 0.5);
            }
            
            .aifc-cta-btn-outline:hover {
                background: rgba(255, 255, 255, 0.1);
                border-color: white;
            }
            
            .aifc-cta-icon {
                font-size: 1.2em;
            }
            
            .aifc-cta-note {
                margin-top: 20px;
                padding: 15px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 8px;
                font-size: 0.9em;
                text-align: center;
            }
            
            /* Slider amélioré */
            .aifc-event-slider-container {
                position: relative;
                border-radius: 15px;
                overflow: hidden;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
                margin-bottom: 30px;
            }
            
            .aifc-event-slider {
                width: 100%;
                height: 500px;
                position: relative;
            }
            
            .aifc-event-slider .swiper-wrapper {
                align-items: stretch;
            }
            
            .aifc-event-slider .swiper-slide {
                height: auto;
                position: relative;
                overflow: hidden;
            }
            
            .aifc-event-slider .swiper-slide img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.5s ease;
            }
            
            .aifc-event-slider .swiper-slide:hover img {
                transform: scale(1.05);
            }
            
            .aifc-slider-overlay {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                background: linear-gradient(transparent, rgba(0,0,0,0.8));
                color: white;
                padding: 30px;
                transform: translateY(100%);
                transition: transform 0.3s ease;
            }
            
            .aifc-event-slider .swiper-slide:hover .aifc-slider-overlay {
                transform: translateY(0);
            }
            
            .swiper-pagination {
                position: absolute;
                bottom: 20px !important;
            }
            
            .swiper-pagination-bullet {
                background: rgba(255, 255, 255, 0.5);
                width: 12px;
                height: 12px;
                opacity: 1;
            }
            
            .swiper-pagination-bullet-active {
                background: #028140;
            }
            
            /* Navigation slider */
            .swiper-button-next,
            .swiper-button-prev {
                color: white;
                background: rgba(8, 82, 71, 0.8);
                width: 50px;
                height: 50px;
                border-radius: 50%;
                transition: all 0.3s;
            }
            
            .swiper-button-next:after,
            .swiper-button-prev:after {
                font-size: 20px;
            }
            
            .swiper-button-next:hover,
            .swiper-button-prev:hover {
                background: #085247;
                transform: scale(1.1);
            }
            
            /* Contenu principal */
            .aifc-event-main {
                background: white;
                border-radius: 15px;
                padding: 30px;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            }
            
            .aifc-event-header {
                margin-bottom: 30px;
                padding-bottom: 20px;
                border-bottom: 3px solid #028140;
            }
            
            .aifc-event-title {
                color: #085247;
                font-size: 2.2em;
                margin-bottom: 10px;
                line-height: 1.2;
            }
            
            .aifc-event-subtitle {
                color: #666;
                font-size: 1.1em;
                font-weight: 300;
            }
            
            .aifc-event-meta-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
                margin: 25px 0;
            }
            
            .aifc-meta-card {
                background: #f8f9fa;
                border-radius: 10px;
                padding: 20px;
                border-left: 4px solid #028140;
                transition: transform 0.3s;
            }
            
            .aifc-meta-card:hover {
                transform: translateY(-5px);
                background: #e9ecef;
            }
            
            .aifc-meta-icon {
                font-size: 24px;
                margin-bottom: 10px;
                color: #085247;
            }
            
            .aifc-meta-label {
                display: block;
                font-size: 0.9em;
                color: #666;
                margin-bottom: 5px;
                font-weight: 500;
            }
            
            .aifc-meta-value {
                display: block;
                font-size: 1.1em;
                color: #333;
                font-weight: 600;
            }
            
            .aifc-event-content {
                line-height: 1.8;
                color: #444;
            }
            
            .aifc-event-content h2,
            .aifc-event-content h3 {
                color: #085247;
                margin-top: 30px;
                padding-bottom: 10px;
                border-bottom: 2px solid #e9ecef;
            }
            
            .aifc-event-content h2 {
                font-size: 1.8em;
            }
            
            .aifc-event-content h3 {
                font-size: 1.5em;
            }
            
            .aifc-event-resume {
                background: linear-gradient(135deg, #f8fff8, #f0f9f0);
                border-left: 4px solid #028140;
                padding: 25px;
                border-radius: 8px;
                margin: 25px 0;
                font-size: 1.1em;
                color: #333;
            }
            
            .aifc-event-theme {
                background: linear-gradient(135deg, #085247, #028140);
                color: white;
                padding: 20px;
                border-radius: 10px;
                margin: 25px 0;
                position: relative;
                overflow: hidden;
            }
            
            .aifc-event-theme:before {
                content: "✨";
                position: absolute;
                top: 10px;
                right: 10px;
                font-size: 2em;
                opacity: 0.3;
            }
            
            .aifc-event-theme h3 {
                color: white;
                margin-top: 0;
            }
            
            /* Boutons d'action */
            .aifc-event-actions {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 15px;
                margin-top: 40px;
                padding-top: 30px;
                border-top: 2px dashed #dee2e6;
            }
            
            /* Countdown */
            .aifc-countdown {
                background: linear-gradient(135deg, #085247, #028140);
                color: white;
                padding: 25px;
                border-radius: 15px;
                text-align: center;
                margin: 30px 0;
            }
            
            .aifc-countdown-title {
                font-size: 1.3em;
                margin-bottom: 20px;
            }
            
            .aifc-countdown-timer {
                display: flex;
                justify-content: center;
                gap: 15px;
                flex-wrap: wrap;
            }
            
            .aifc-countdown-item {
                background: rgba(255, 255, 255, 0.1);
                padding: 15px;
                border-radius: 10px;
                min-width: 70px;
            }
            
            .aifc-countdown-number {
                font-size: 2em;
                font-weight: bold;
                display: block;
            }
            
            .aifc-countdown-label {
                font-size: 0.9em;
                opacity: 0.8;
            }
            
            /* Responsive */
            @media (max-width: 768px) {
                .aifc-event-layout {
                    grid-template-columns: 1fr;
                    gap: 30px;
                }
                
                .aifc-event-title {
                    font-size: 1.8em;
                }
                
                .aifc-event-slider {
                    height: 300px;
                }
                
                .aifc-event-sidebar {
                    position: static;
                }
                
                .aifc-event-meta-grid {
                    grid-template-columns: 1fr;
                }
                
                .aifc-event-actions {
                    grid-template-columns: 1fr;
                }
                
                .swiper-button-next,
                .swiper-button-prev {
                    display: none;
                }
            }
            
            /* Animation */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .aifc-event-layout {
                animation: fadeInUp 0.6s ease;
            }

            /* Cartes d'information */
            .aifc-event-info-card {
                background: white;
                border-radius: 12px;
                padding: 20px;
                margin-top: 20px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.05);
                border: 1px solid #eaeaea;
            }

            .aifc-event-info-card h4 {
                color: #085247;
                margin-top: 0;
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 15px;
            }

            .aifc-info-list {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .aifc-info-list li {
                display: flex;
                justify-content: space-between;
                padding: 8px 0;
                border-bottom: 1px solid #f0f0f0;
            }

            .aifc-info-list li:last-child {
                border-bottom: none;
            }

            .aifc-info-label {
                font-weight: 500;
                color: #666;
            }

            .aifc-info-value {
                color: #333;
                text-align: right;
            }

            /* Partage */
            .aifc-event-share {
                margin-top: 20px;
            }

            .aifc-share-buttons {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
                margin-top: 10px;
            }

            .aifc-share-btn {
                padding: 10px;
                border-radius: 8px;
                text-align: center;
                text-decoration: none;
                font-size: 0.9em;
                transition: all 0.3s;
                color: white;
            }

            .aifc-share-btn.facebook {
                background: #3b5998;
            }

            .aifc-share-btn.twitter {
                background: #1da1f2;
            }

            .aifc-share-btn.whatsapp {
                background: #25d366;
            }

            .aifc-share-btn:hover {
                opacity: 0.9;
                transform: translateY(-2px);
            }

            /* Programme */
            .aifc-programme-timeline {
                position: relative;
                padding: 20px 0;
            }

            .aifc-programme-timeline:before {
                content: '';
                position: absolute;
                left: 30px;
                top: 0;
                bottom: 0;
                width: 2px;
                background: #028140;
            }

            .aifc-programme-item {
                display: flex;
                margin-bottom: 25px;
                position: relative;
            }

            .aifc-programme-time {
                width: 60px;
                padding-right: 20px;
                font-weight: bold;
                color: #085247;
                text-align: right;
                position: relative;
            }

            .aifc-programme-time:after {
                content: '';
                position: absolute;
                right: -6px;
                top: 50%;
                transform: translateY(-50%);
                width: 14px;
                height: 14px;
                border-radius: 50%;
                background: #028140;
                border: 3px solid white;
                box-shadow: 0 0 0 3px #028140;
            }

            .aifc-programme-content {
                flex: 1;
                background: #f8f9fa;
                padding: 20px;
                border-radius: 10px;
                margin-left: 20px;
                border-left: 4px solid #028140;
            }

            .aifc-programme-content h4 {
                margin-top: 0;
                color: #085247;
            }

            .aifc-programme-speaker {
                margin-top: 10px;
                padding-top: 10px;
                border-top: 1px dashed #ddd;
                font-size: 0.9em;
                color: #666;
            }

            /* Intervenants */
            .aifc-speakers-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 25px;
                margin-top: 20px;
            }

            .aifc-speaker-card {
                background: white;
                border-radius: 12px;
                padding: 20px;
                text-align: center;
                box-shadow: 0 5px 15px rgba(0,0,0,0.05);
                transition: transform 0.3s;
            }

            .aifc-speaker-card:hover {
                transform: translateY(-5px);
            }

            .aifc-speaker-photo {
                width: 100px;
                height: 100px;
                border-radius: 50%;
                object-fit: cover;
                margin: 0 auto 15px;
                border: 4px solid #f0f0f0;
            }

            .aifc-speaker-card h4 {
                margin: 10px 0 5px;
                color: #085247;
            }

            .aifc-speaker-title {
                color: #666;
                font-size: 0.9em;
                margin-bottom: 10px;
            }

            .aifc-speaker-bio {
                color: #555;
                font-size: 0.9em;
                line-height: 1.5;
            }

            .elementskit-menu-close{
                color: #fff;
            }
            .elementskit-menu-close:hover{
                color: #fff;
            }
        </style>
        <?php
    endif;    
}

// Add this to your existing rt_widget_formation_styles function or create a new one
function rt_sticky_menu_mobile_fix() {
    ?>
    <style>
        /* Désactiver complètement le sticky sur mobile */
        @media (max-width: 767.98px) {
            .is-sticky,
            #masthead.is-sticky,
            .site-header.is-sticky,
            .navbar.is-sticky,
            #header.is-sticky {
                position: relative !important;
                top: auto !important;
                left: auto !important;
                right: auto !important;
                width: auto !important;
                transform: none !important;
                animation: none !important;
                box-shadow: none !important;
                z-index: auto !important;
            }
            
            /* Cacher le placeholder sur mobile */
            .sticky-placeholder {
                display: none !important;
            }
            
            /* Annuler le padding du body ajouté par le sticky */
            body {
                padding-top: 0 !important;
            }
        }
        
        /* Styles pour le sticky sur desktop seulement */
        @media (min-width: 768px) {
            .is-sticky {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                width: 100%;
                z-index: 9999;
                background-color: #fff;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                animation: slideDown 0.3s ease;
            }
            
            /* Ajuster pour la barre d'admin WordPress */
            .admin-bar .is-sticky {
                top: 32px;
            }
            
            @keyframes slideDown {
                from {
                    transform: translateY(-100%);
                }
                to {
                    transform: translateY(0);
                }
            }
            
            /* Placeholder pour éviter le saut de contenu */
            .sticky-placeholder {
                display: block;
            }
        }
    </style>
    <?php
}
add_action('wp_head', 'rt_sticky_menu_mobile_fix');