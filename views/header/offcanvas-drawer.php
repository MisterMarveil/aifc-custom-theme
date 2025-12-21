<?php
/**
 * Template part for displaying header offcanvas
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package finwave
 */
use RT\Finwave\Options\Opt;
use RT\Finwave\Helpers\Fns;
$logo_h1 = ! is_singular( [ 'post' ] );
$topinfo = ( finwave_option( 'rt_contact_address' ) || finwave_option( 'rt_phone' ) || finwave_option( 'rt_email' ) || finwave_option( 'rt_email' ) ) ? true : false;
?>


<div class="finwave-offcanvas-drawer">
	<div class="offcanvas-logo">
		<?php echo finwave_site_logo( $logo_h1 ); ?>
		<a class="trigger-icon trigger-off-canvas" href="#">×</a>
	</div>
	<?php if( finwave_option( 'rt_about_label' ) || finwave_option( 'rt_about_text' ) ) { ?>
	<div class="offcanvas-about offcanvas-address">
		<?php if( finwave_option( 'rt_about_label' ) ) { ?><label><?php echo finwave_option( 'rt_about_label' ) ?></label><?php } ?>
		<?php if( finwave_option( 'rt_about_text' ) ) { ?><p><?php echo finwave_option( 'rt_about_text' ) ?></p><?php } ?>
	</div>
	<?php } ?>
	<nav class="offcanvas-navigation" role="navigation">
		<?php
		if ( has_nav_menu( 'primary' ) ) :
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'walker'         => new RT\Finwave\Core\WalkerNav(),
				)
			);
		endif;
		?>
	</nav><!-- .finwave-navigation -->

	<div class="offcanvas-address">
		<?php if( $topinfo ) { ?>
			<?php if( finwave_option( 'rt_contact_info_label' ) ) { ?><label><?php echo finwave_option( 'rt_contact_info_label' ) ?></label><?php } ?>
			<ul class="offcanvas-info">
				<?php if( finwave_option( 'rt_contact_address' ) ) { ?>
					<li><i class="icon-rt-location-4"></i><?php finwave_html( finwave_option( 'rt_contact_address' ) , false );?> </li>
				<?php } if( finwave_option( 'rt_phone' ) ) { ?>
					<li><i class="icon-rt-phone-2"></i><a href="tel:<?php echo esc_attr( finwave_option( 'rt_phone' ) );?>"><?php finwave_html( finwave_option( 'rt_phone' ) , false );?></a> </li>
				<?php } if( finwave_option( 'rt_email' ) ) { ?>
					<li><i class="icon-rt-email"></i><a href="mailto:<?php echo esc_attr( finwave_option( 'rt_email' ) );?>"><?php finwave_html( finwave_option( 'rt_email' ) , false );?></a> </li>
				<?php } if( finwave_option( 'rt_website' ) ) { ?>
					<li><i class="icon-rt-development-service"></i><?php finwave_html( finwave_option( 'rt_website' ) , false );?></li>
				<?php } ?>
			</ul>
		<?php } ?>

		<?php if( finwave_option( 'rt_offcanvas_social' ) ) { ?>
			<div class="offcanvas-social-icon">
				<?php finwave_get_social_html( '#555' ); ?>
			</div>
		<?php } ?>
	</div>

</div><!-- .container -->

<div class="finwave-body-overlay"></div>
