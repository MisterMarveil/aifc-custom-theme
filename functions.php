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
                background: linear-gradient(135deg, #3498db, #2980b9);
                color: white;
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
                background: linear-gradient(135deg, #2980b9, #1c5a7d);
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(41, 128, 185, 0.3);
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