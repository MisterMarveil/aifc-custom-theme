/**
 * Sticky Menu pour WordPress - Version optimisée
 */
(function($) {
    'use strict';
    
    $(document).ready(function() {
        var $menuItems = $("li > .title-link");
        if( $menuItems.length ) {
            $menuItems.each(function() {
                var $this = $(this);
                $this.on("click", function(e) {
                    var $this = $(this);
                    var $href = $this.attr("href");
                    if( !$href.startsWith("#") ) {
                        return; // Ne pas interférer avec les liens externes
                    }
                    e.preventDefault();
                    var destElement = document.getElementById($href.substring(1));
                    destElement.click(); // Simuler le clic sur l'élément cible  
                    setTimeout(function() {
                        destElement.scrollIntoView({ 
                            top: -200,
                            behavior: 'smooth'
                        });                                      
                    }, 500); // Délai pour permettre le traitement du clic avant le défilement
                });
            });
        }
    });  
})(jQuery);