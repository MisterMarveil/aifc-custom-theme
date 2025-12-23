/**
 * Sticky Menu pour WordPress - Version optimisée
 */
(function($) {
    'use strict';
    
    $.fn.stickyMenu = function(options) {
        const settings = $.extend({
            wrapper: 'body',
            minWidth: 768,           // Desktop seulement
            adminBar: true,          // Prendre en compte la barre admin
            animation: true,
            customClass: 'is-sticky',
            zIndex: 9999,
            callback: null
        }, options);
        
        return this.each(function() {
            const $menu = $(this);
            const $window = $(window);
            const $adminBar = $('#wpadminbar');
            let isSticky = false;
            let menuHeight = $menu.outerHeight();
            let menuTop = $menu.offset().top;
            
            // Créer le placeholder
            const $placeholder = $('<div class="sticky-placeholder"></div>')
                .height(menuHeight)
                .hide()
                .insertBefore($menu);
            
            // Fonction pour activer le sticky
            function activateSticky() {
                if (isSticky) return;
                
                isSticky = true;
                menuHeight = $menu.outerHeight();
                
                // Calculer la marge pour la barre admin
                let topPosition = 0;
                if (settings.adminBar && $adminBar.length) {
                    topPosition = $adminBar.outerHeight();
                }
                
                // Appliquer les styles
                $menu.css({
                    'position': 'fixed',
                    'top': topPosition,
                    'left': 0,
                    'right': 0,
                    'z-index': settings.zIndex,
                    'width': '100%'
                });
                
                // Animation
                if (settings.animation) {
                    $menu.hide().slideDown(300);
                }
                
                // Classe personnalisée
                $menu.addClass(settings.customClass);
                
                // Afficher le placeholder
                $placeholder.height(menuHeight).show();
                
                // Callback
                if (typeof settings.callback === 'function') {
                    settings.callback.call($menu, 'activate');
                }
                
                // Déclencher un événement
                $menu.trigger('sticky-activate');
            }
            
            // Fonction pour désactiver le sticky
            function deactivateSticky() {
                if (!isSticky) return;
                
                isSticky = false;
                
                // Retirer les styles
                $menu.css({
                    'position': '',
                    'top': '',
                    'left': '',
                    'right': '',
                    'z-index': '',
                    'width': ''
                });
                
                // Retirer la classe
                $menu.removeClass(settings.customClass);
                
                // Cacher le placeholder
                $placeholder.hide();
                
                // Callback
                if (typeof settings.callback === 'function') {
                    settings.callback.call($menu, 'deactivate');
                }
                
                // Déclencher un événement
                $menu.trigger('sticky-deactivate');
            }
            
            // Gérer le défilement
            function handleScroll() {
                const scrollTop = $window.scrollTop();
                const windowWidth = $window.width();
                
                // Vérifier la largeur minimale
                if (windowWidth < settings.minWidth) {
                    deactivateSticky();
                    return;
                }
                
                // Vérifier la position
                if (scrollTop > menuTop) {
                    activateSticky();
                } else {
                    deactivateSticky();
                }
            }
            
            // Gérer le redimensionnement
            function handleResize() {
                menuHeight = $menu.outerHeight();
                menuTop = $menu.offset().top;
                
                if (isSticky) {
                    $placeholder.height(menuHeight);
                }
                
                handleScroll();
            }
            
            // Écouter les événements
            $window.on('scroll.stickyMenu', handleScroll);
            $window.on('resize.stickyMenu', handleResize);
            
            // Initialisation
            handleScroll();
            
            // Méthodes publiques
            $menu.data('stickyMenu', {
                update: function() {
                    handleResize();
                },
                destroy: function() {
                    $window.off('.stickyMenu');
                    deactivateSticky();
                    $placeholder.remove();
                    $menu.removeData('stickyMenu');
                }
            });
        });
    };
    
    // Initialisation automatique au chargement
    $(document).ready(function() {
        // Sélecteurs courants dans WordPress
        const selectors = [
            '.site-header',
            '.main-navigation',
            '#masthead',
            '.navbar',
            '#header'
        ];
        
        selectors.forEach(function(selector) {
            if ($(selector).length) {
                $(selector).stickyMenu({
                    minWidth: 768,
                    adminBar: true,
                    animation: true,
                    callback: function(state) {
                        console.log('Menu ' + state + ' pour ' + selector);
                    }
                });
                return false; // Sortir de la boucle au premier trouvé
            }
        });
    });
    
})(jQuery);