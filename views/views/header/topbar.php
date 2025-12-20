<?php
/**
 * Template part for displaying header
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package finwave
 */

use RT\Finwave\Options\Opt;

if(! Opt::$has_top_bar ) {
	return;
}
$topinfo = ( finwave_option( 'rt_contact_address' ) || finwave_option( 'rt_phone' ) || finwave_option( 'rt_email' ) || finwave_option( 'rt_website' ) ) ? true : false;
$_fullwidth = Opt::$header_width == 'full' ? '-fluid' : '';
?>

<div class="finwave-topbar">
	<div class="topbar-container rt-container<?php echo esc_attr($_fullwidth) ?>">
		<div class="topbar-row d-flex flex-wrap column-gap-30 align-items-center">
			<?php if( $topinfo ) { ?>
			<div class="topbar-left d-flex flex-wrap column-gap-30 align-items-center">
				<?php if( finwave_option( 'rt_topbar_address' ) && finwave_option( 'rt_contact_address' )  ) { ?>
					<span><i class="icon-rt-location-4"></i><?php finwave_html( finwave_option( 'rt_contact_address' ) , false );?></span>
				<?php } if( finwave_option( 'rt_topbar_phone' ) && finwave_option( 'rt_phone' ) ) { ?>
					<span><i class="icon-rt-phone-2"></i><a href="tel:<?php echo esc_attr( finwave_option( 'rt_phone' ) );?>"><?php finwave_html( finwave_option( 'rt_phone' ) , false );?></a></span>
				<?php } if( finwave_option( 'rt_topbar_email' ) && finwave_option( 'rt_email' ) ) { ?>
					<span><i class="icon-rt-email"></i><a href="mailto:<?php echo esc_attr( finwave_option( 'rt_email' ) );?>"><?php finwave_html( finwave_option( 'rt_email' ) , false );?></a></span>
				<?php } if( finwave_option( 'rt_topbar_website' ) && finwave_option( 'rt_website' ) ) { ?>
					<span><i class="icon-rt-development-service"></i><?php finwave_html( finwave_option( 'rt_website' ) , false );?></span>
				<?php } ?>
			</div>
			<?php } ?>
			<?php if( finwave_option( 'rt_topbar_social' ) ) { ?>
			<div class="topbar-right d-flex gap-30 align-items-center">
				<div class="social-icon">
					<?php if( finwave_option( 'rt_follow_us_label' ) ) { ?><label><?php echo finwave_option( 'rt_follow_us_label' ) ?></label><?php } ?>
					<?php finwave_get_social_html( '#555' ); ?>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
