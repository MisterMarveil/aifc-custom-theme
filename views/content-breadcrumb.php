<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

if( function_exists( 'bcn_display') ){
	echo '<div class="breadcrumb-area"><div class="entry-breadcrumb">';
	if ( is_rtl() ) { //@rtl
		bcn_display( false, true, true );
	}
	else {
		bcn_display();
	}
	echo '</div></div>';
} else {
	echo '<div class="breadcrumb-area"><div class="entry-breadcrumb">';
	finwave_breadcrumb();
	echo '</div></div>';
}
