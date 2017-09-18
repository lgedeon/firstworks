<?php
/*
 * The code that makes this child theme unique
 */



function firstworks_action__wp_enqueue_scripts() {
	wp_enqueue_script( 'wisepops', get_stylesheet_directory_uri() . '/wisepops.js', array(), false, true );
	wp_enqueue_script( 'firstworks', get_stylesheet_directory_uri() . '/firstworks.js', array('dt-main'), false, true );
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'firstworks_action__wp_enqueue_scripts', 20 );

function firstworks_action__presscore_after_main_container () { ?>
    <!-- STASUN BOF 50 -->
    <?php edit_post_link(); // todo: remove this link :) ?>
    <div class="bottom-footer-partener">
        <div class="bottom-footer-partener-inside">
			<?php echo do_shortcode('[dt_logos columns="5" dividers="true" number="50" orderby="date" order="desc" animation="none" category="gold-partners"]');?>
		</div>
		<br clear="both">
    </div>
	<br clear="both">
	<!-- STASUN EOF -->
	<?php
}
add_action( 'presscore_after_main_container', 'firstworks_action__presscore_after_main_container' );

function firstworks_action__template_albums_jgrid_before_gallery () {
?>
	<!-- STARSUN BOF -->
	<div class="wpb_row wf-container">
	<?php if(get_post_meta(get_the_ID(), "next-p", true)){ ?>
		<div class="wf-cell wf-span-9 wpb_column column_container">
	<?php }else{ ?>
		<div class="wf-cell wf-span-12 wpb_column column_container">
	<?php } ?>
	<?php
}
add_action( 'template_albums_jgrid_before_gallery', 'firstworks_action__template_albums_jgrid_before_gallery' );

function firstworks_action__template_albums_jgrid_after_gallery () {
	?>
		</div>
		<?php if(get_post_meta(get_the_ID(), "next-p", true)){ ?>
		<div class="wf-cell wf-span-3 wpb_column column_container">
			<div class="next-perform-event">
				<?php print_r(get_post_meta(get_the_ID(), "next-p", true)); ?>
			</div>
		</div>
		<?php } ?>
	</div>
	<!-- STARSUN EOF -->
	<?php
}
add_action( 'template_albums_jgrid_after_gallery', 'firstworks_action__template_albums_jgrid_after_gallery' );




	// todo: come back and fix this - The code in this function is wrong... just... wrong.
function firstworks_action__wp_head () {
	///events/pvdfest/
	$needle = "/pvdfest";
	$haystack = $_SERVER['REQUEST_URI'];

	if (strpos($haystack, $needle) !== false){
		?>

		<script type="text/javascript">
			jQuery(function() {
/*				jQuery('#branding').find('img.preload-me').attr('src', '/wp-content/uploads/pvdfest/PIAF_PVD_FW_LOGOS.png').removeAttr( "width" );
				jQuery('#branding').find('a').attr('href', '/events/pvdfest/');
*/				jQuery('.menu-item-23148').find('a').first().attr('href', '/');
				jQuery('.menu-item-23148').find('span').first().text( "FIRSTWORKS" );
				jQuery('#page > div.bottom-footer-partener > div > section').hide();

				jQuery('#mobile-menu').click(function() {
					jQuery('.menu-item-23148').find('.dl-submenu').remove();
				});


			});
		</script>
		<style type="text/css">
/*			#branding a img, #branding img {
				width: 190px !important;
				height: 124px !important;
			}
*/		</style>

		<?php
	}
	?>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
	<link rel="icon" href="/favicon.ico" type="image/x-icon">
	<?php
}
add_action( 'wp_head', 'firstworks_action__wp_head' );


function firstworks_filter__templates_header_branding_php_logo ( $logo ) {
	$needle = "/pvdfest";
	$haystack = $_SERVER['REQUEST_URI'];

	if (strpos($haystack, $needle) !== false){
		$logo = '<img class="preload-me" src="/wp-content/uploads/pvdfest/PIAF_PVD_FW_LOGOS.png" width="276" height="124"   alt="FirstWorks" />';
	}

	return $logo;
}
//add_filter( 'templates_header_branding_php_logo', 'firstworks_filter__templates_header_branding_php_logo' );

function firstworks_filter__dt_album_title_image_args ( $args ) {
	$image_link = isset( $args['img_id'] ) ? get_post_meta( $args['img_id'], 'dt-img-link', true ) : '';
//echo 'var_export';var_export($args);
	if ( $image_link ) {
		$args['class'] .= ' firstworks-link-override';
		$args['href'] = $image_link;
	}

	return $args;
}
add_filter( 'dt_album_title_image_args', 'firstworks_filter__dt_album_title_image_args' );

// A very simple filter on the title and now the excerpt too - Expands [f12] to <span style="font-size: 12px;">
function firstworks_filter__the_title ( $title ) {
	if ( ! is_admin() ) {
		$title = preg_replace( '/\[f([0-9]*)\]/', '<span style="font-size: $1px;">', $title );
		$title = preg_replace( '/\[\/f\]/', '</span>', $title );
	}
	return $title;
}
add_filter( 'the_title', 'firstworks_filter__the_title' );
add_filter( 'get_the_excerpt', 'firstworks_filter__the_title' );

function helper__dynamic_sidebar_params__title_class ( $instance, $widget, $args ) {
	if ( isset( $instance['title'] ) ) {
		$new_class = implode( '_', array( 'widget', $widget->id_base, sanitize_title( $instance['title'] ) ) ) . " ";
		$args['before_widget'] = str_replace( 'class="', 'class="' . $new_class, $args['before_widget'] );

		// For lack of a proper filter, let's just do this ourselves - will have to keep up to date with WP, though.
		$was_cache_addition_suspended = wp_suspend_cache_addition();
		if ( $widget->is_preview() && ! $was_cache_addition_suspended ) {
			wp_suspend_cache_addition( true );
		}

		$widget->widget( $args, $instance );

		if ( $widget->is_preview() ) {
			wp_suspend_cache_addition( $was_cache_addition_suspended );
		}

		// Return false to prevent display since we just did it
		return false;
	}

	return $instance;
}
add_filter( 'widget_display_callback', 'helper__dynamic_sidebar_params__title_class', 10 , 3 );


/**
 * Primary navigation menu.
 *
 */
function firstworks_add_primary_menu() {
	$logo_align = of_get_option( 'header-layout', 'left' );
?>

<?php // Starsun BOF  ?>
            <?php if ( of_get_option('header-search_show', 1) && 'left' == $logo_align ) : ?>
            <div id="top-icon" style="display:block;">
                <div class="top-contact"><a href="<?php echo get_page_link(23221); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/contact.png" title="Contact Us" alt="Contact Us" width="43" height="32"/></a></div>
                <div class="top-search">
		<div class="wf-td mini-search">
			<?php get_search_form(); ?>
		</div>
                </div>
                <div class="top-login"><a href="#"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/cadenas.png" title="Login" alt="Login" width="32" height="32"/></a></div>
            </div>
	<?php endif; ?>
<?php // Starsun EOF ?>

<?php }

add_action( 'presscore_primary_navigation', 'firstworks_add_primary_menu', 14 );

