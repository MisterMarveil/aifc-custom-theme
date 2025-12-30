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
    $formation_categorie = get_post_meta($post->ID, '_rt_formation_categorie', true);
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
        <div class="formation-field">
            <label for="rt_formation_categorie">📂 Catégorie détaillée</label>
            <input type="text" id="rt_formation_categorie" name="rt_formation_categorie" 
                   value="<?php echo esc_attr($formation_categorie); ?>" placeholder="Ex: Formations Standards — Islamic Finance">
        </div>
        
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
    </div>
    
    <!-- Section pour les modules de formation (éditeur WYSIWYG) -->
    <div style="margin-top: 30px; border-top: 2px solid #eee; padding-top: 20px;">
        <h3>📚 Modules de la formation</h3>
        <p class="formation-note">Listez ici les différents modules de la formation (un par ligne)</p>
        <?php 
        $formation_modules = get_post_meta($post->ID, '_rt_formation_modules', true);
        wp_editor(
            $formation_modules ? $formation_modules : "Introduction au système d'économie islamique\nGestion des risques en finance islamique\nMode opératoire des obligations islamiques (Sukuk)",
            'rt_formation_modules',
            array(
                'textarea_name' => 'rt_formation_modules',
                'textarea_rows' => 6,
                'media_buttons' => false,
                'teeny' => true,
                'quicktags' => false,
            )
        );
        ?>
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
        'rt_formation_categorie',
        'rt_formation_duree',
        'rt_formation_niveau',
        'rt_formation_public',
        'rt_formation_prix',
        'rt_formation_paiement',
        'rt_formation_mode',
        'rt_formation_certification',
        'rt_formation_prochaine_rentree',
        'rt_formation_lien_preinscription',
        'rt_formation_modules',
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
    $new_columns['formation_categorie'] = '📂 Catégorie';
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
        case 'formation_categorie':
            $categorie = get_post_meta($post_id, '_rt_formation_categorie', true);
            echo $categorie ? esc_html($categorie) : '—';
            break;
            
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
        <?php if (!empty($formation_data['lien_preinscription'])): ?>
        <div class="formation-section formation-preinscription">
            <a href="<?php echo esc_url($formation_data['lien_preinscription']); ?>" 
               class="btn-formation-preinscription" 
               target="_blank">
               🔗 Préinscription - Réserver une place
            </a>
        </div>
        <?php endif; ?>
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
        'categorie' => get_post_meta($post_id, '_rt_formation_categorie', true),
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
        <?php if ($atts['show_title'] === 'yes'): ?>
        <div class="formation-widget-header">
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
                    <?php if (!empty($formation_data['lien_preinscription'])): ?>
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
                    <?php endif; ?>
                    
                    <!-- Brochure détaillée -->
                    <a href="#contact-form-brochure" 
                       class="formation-action-btn btn-brochure"
                       onclick="event.preventDefault(); document.getElementById('contact-form-brochure').scrollIntoView({behavior: 'smooth'});">
                        <span class="action-icon">📄</span>
                        <span class="action-text">
                            <strong>Recevez la brochure détaillée</strong>
                            <small>Programme complet et modalités</small>
                        </span>
                        <span class="action-arrow">→</span>
                    </a>
                    
                    <!-- Échange avec conseiller -->
                    <a href="#contact-form-conseiller" 
                       class="formation-action-btn btn-conseiller"
                       onclick="event.preventDefault(); document.getElementById('contact-form-conseiller').scrollIntoView({behavior: 'smooth'});">
                        <span class="action-icon">👨‍💼</span>
                        <span class="action-text">
                            <strong>Échangez avec un conseiller AIFC</strong>
                            <small>Orientation personnalisée</small>
                        </span>
                        <span class="action-arrow">→</span>
                    </a>
                    
                    <!-- Téléphone direct -->
                    <a href="tel:+221338699595" class="formation-action-btn btn-phone">
                        <span class="action-icon">📞</span>
                        <span class="action-text">
                            <strong>Appelez-nous directement</strong>
                            <small>+221 33 869 95 95</small>
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
        <h3>Demander la brochure détaillée</h3>
        <p>Recevez le programme complet, les modalités d'inscription et toutes les informations sur cette formation.</p>
        
        <?php 
        // Vous pouvez utiliser Contact Form 7, Gravity Forms, ou un formulaire HTML simple
        // Exemple avec Contact Form 7 :
        if (shortcode_exists('contact-form-7')) {
            echo do_shortcode('[contact-form-7 id="123" title="Demande de brochure"]');
        } else {
            // Fallback HTML
            ?>
            <form action="#" method="post" class="formation-brochure-form">
                <div class="form-group">
                    <input type="text" name="nom" placeholder="Votre nom complet" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Votre adresse email" required>
                </div>
                <div class="form-group">
                    <input type="tel" name="telephone" placeholder="Votre numéro de téléphone">
                </div>
                <div class="form-group">
                    <textarea name="message" placeholder="Votre message ou questions" rows="4"></textarea>
                </div>
                <input type="hidden" name="formation" value="<?php echo get_the_title(); ?>">
                <button type="submit" class="btn-submit">Envoyer la demande</button>
            </form>
            <?php
        }
        ?>
    </div>
    <?php
    return ob_get_clean();
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


// Enqueue le script sticky menu
add_action('wp_enqueue_scripts', 'enqueue_sticky_menu_script');
function enqueue_sticky_menu_script() {
    wp_enqueue_script(
        'sticky-menu',
        get_stylesheet_directory_uri() . '/js/sticky-menu.js',
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


// Ajoutez cette fonction à votre functions.php
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
                font-style: italic;
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
                background: #fff3cd;
                border: 1px solid #ffeaa7;
                border-radius: 6px;
                padding: 12px 15px;
                text-align: center;
            }
            
            .formation-action-note p {
                margin: 0;
                color: #856404;
                font-size: 0.9em;
            }
            
            .formation-action-note strong {
                color: #d63031;
            }

            .no-margin {
               margin: 0;
            }

            .compact-info-icon {
                background: white;
                padding: 3px;
                font-style: normal;
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
}