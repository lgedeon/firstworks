<?php
/**
 * Eventmap template.
 *
 * @package presscore
 * @since presscore 2.2
 */

/* Template Name: Event Map */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// add filter for theme options here
add_filter( 'dt_of_get_option', 'presscore_microsite_theme_options_filter', 15, 2 );

// add menu filter here
add_filter( 'dt_menu_options', 'presscore_microsite_menu_filter' );

// hide template parts
$hidden_parts = get_post_meta( $post->ID, "_dt_microsite_hidden_parts", false );

$hide_header = in_array( 'header', $hidden_parts );
$hide_floating_menu = in_array( 'floating_menu', $hidden_parts );

if ( $hide_header && $hide_floating_menu ) {
	add_filter( 'presscore_show_header', '__return_false' );
} else if ( $hide_header ) {
	// see template-tags.php
	add_filter( 'presscore_header_classes', 'presscore_microsite_header_classes' );
}

if ( in_array( 'bottom_bar', $hidden_parts ) ) {
	add_filter( 'presscore_show_bottom_bar', '__return_false' );
}

if ( get_post_meta( $post->ID, '_dt_microsite_page_loading', true ) ) {
	// see template-tags.php
	add_action( 'presscore_body_top', 'presscore_microsite_add_loader_div' );
}

$config = Presscore_Config::get_instance();
$config->set('template', 'microsite');
$config->base_init();

get_header(); ?>

<style type="text/css">

#top-bar{
	display: none;
}
.content{
	width: 100% !important;
}
thead.cf{
background-color: #4A4B4B;
color: #fff;
}
.datetd{
	white-space: nowrap;
}

	@media only screen and (max-width: 800px) {
    
    /* Force table to not be like tables anymore */
	#no-more-tables table, 
	#no-more-tables thead, 
	#no-more-tables tbody, 
	#no-more-tables th, 
	#no-more-tables td, 
	#no-more-tables tr { 
		display: block; 
	}
 
	/* Hide table headers (but not display: none;, for accessibility) */
	#no-more-tables thead tr { 
		position: absolute;
		top: -9999px;
		left: -9999px;
	}
 
	#no-more-tables tr { border: 1px solid #ccc; }
 
	#no-more-tables td { 
		/* Behave  like a "row" */
		border: none;
		border-bottom: 1px solid #eee; 
		position: relative;
		padding-left: 50%; 
		white-space: normal;
		text-align:left;
	}
 
	#no-more-tables td:before { 
		/* Now like a table header */
		position: absolute;
		/* Top/left values mimic padding */
		top: 6px;
		left: 6px;
		width: 45%; 
		padding-right: 10px; 
		white-space: nowrap;
		text-align:left;
		font-weight: bold;
	}
	/*
	Label the data
	*/
	#no-more-tables td:before { content: attr(data-title); }
}
	#no-more-tables td a{
		color: #373A41;
		font-weight: bold;
	}
	#no-more-tables th{
		cursor: pointer;
	}
	.ART{
		color: #CB223C;
	}
	.EVENT{
		color: #4A4B4B;
	}
	.OTHER{
		color: #6C9F43;
	}
	.IDEAS{
		color: #741264;
	}
	.ART-inverse{
		background-color: #CB223C;
		color: #fff;
		padding: 3px;
	}
	.EVENT-inverse{
		background-color: #4A4B4B;
		color: #fff;
		padding: 3px;
	}
	.OTHER-inverse{
		background-color: #6C9F43;
		color: #fff;
		padding: 3px;
	}
	.IDEAS-inverse{
		background-color: #741264;
		color: #fff;
		padding: 3px;
	}
</style>

		<?php if ( presscore_is_content_visible() ): ?>	

			<div id="content" class="content" role="main">

			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

					<?php do_action('presscore_before_loop'); ?>

					<?php the_content(); ?>





<div id="map" style="width: 100%; height: 400px;"></div>

<h2 style="margin-top: 8px;">
Select an artist or event from list below to map its location and stage time
</h2>

        <div id="no-more-tables">
            <table class="col-md-12 table-bordered table-striped table-condensed cf">
        		<thead class="cf">
        			<tr>
        				<th id="sort-artist">ATTRACTION</th>
        				<th id="sort-category">CATEGORY</th>
        				<th id="sort-stage">AREA</th>
        			</tr>
        		</thead>
        		<tbody>
					<?php
					$i = 0;
					$mapitems = $wpdb->get_results( "SELECT * FROM event_map_v2 ORDER BY attraction ASC");
					foreach ($mapitems as $key => $value) {
					?>

        			<tr>
        				<td data-title="artist" data-title="artist" class="numeric"><a href="#" onclick="openmarker(<?php echo $i; ?>);"><?php echo $value->attraction; ?></a></td>
        				<td data-title="category" class="numeric <?php echo $value->type; ?>"><b><?php echo $value->type; ?></b></td>
        				<td data-title="stage" class="numeric"><?php echo $value->stage; ?></td>
        			</tr>

					<?php
						$i++;
					}
					?>
        		</tbody>
        	</table>
        </div>

<ul id="list" style="display:none;"></ul>

<script src="http://maps.google.com/maps/api/js?sensor=true" type="text/javascript"></script>
<script type="text/javascript">


		function openmarker(id){
		  jQuery('ul#list li:eq('+id+')').click();
		}

    	

	    jQuery(function() {


		jQuery.fn.sortElements = (function(){
		    
		    var sort = [].sort;
		    
		    return function(comparator, getSortable) {
		        
		        getSortable = getSortable || function(){return this;};
		        
		        var placements = this.map(function(){
		            
		            var sortElement = getSortable.call(this),
		                parentNode = sortElement.parentNode,
		                
		                // Since the element itself will change position, we have
		                // to have some way of storing it's original position in
		                // the DOM. The easiest way is to have a 'flag' node:
		                nextSibling = parentNode.insertBefore(
		                    document.createTextNode(''),
		                    sortElement.nextSibling
		                );
		            
		            return function() {
		                
		                if (parentNode === this) {
		                    throw new Error(
		                        "You can't sort elements if any one is a descendant of another."
		                    );
		                }
		                
		                // Insert before flag:
		                parentNode.insertBefore(this, nextSibling);
		                // Remove flag:
		                parentNode.removeChild(nextSibling);
		                
		            };
		            
		        });
		       
		        return sort.call(this, comparator).each(function(i){
		            placements[i].call(getSortable.call(this));
		        });
		        
		    };
		    
		})();

	    var table = jQuery('.table-condensed');

	    jQuery('#sort-artist, #sort-category, #sort-stage')
	        .wrapInner('<span title="sort this column"/>')
	        .each(function(){
	            
	            var th = jQuery(this),
	                thIndex = th.index(),
	                inverse = false;
	            
	            th.click(function(){
	                
	                table.find('td').filter(function(){
	                    
	                    return jQuery(this).index() === thIndex;
	                    
	                }).sortElements(function(a, b){
	                    
	                    return jQuery.text([a]) > jQuery.text([b]) ?
	                        inverse ? -1 : 1
	                        : inverse ? 1 : -1;
	                    
	                }, function(){
	                    
	                    // parentNode is the element we want to move
	                    return this.parentNode; 
	                    
	                });
	                
	                inverse = !inverse;
	                    
	            });
	                
	        });
	    });


	var locations = [
		<?php
		$mapitems = $wpdb->get_results( "SELECT * FROM event_map_v2 ORDER BY attraction ASC");
		$i = 0;
		foreach ($mapitems as $key => $value) {
			$at = "at";
			$value->attraction = str_replace("'", "`", $value->attraction);
			$value->type = str_replace("'", "`", $value->type);
			$value->times = str_replace("'", "`", $value->times);
			$value->stage = str_replace("'", "`", $value->stage);
			if(!$value->event_time){
				$at = "";
			}
		?>
		    ['<b class="<?php echo $value->type;?>-inverse"><?php echo $value->type;?></b><br><b><?php echo  $value->attraction; ?></b><br><?php echo $value->stage; ?><br><b><?php echo $value->times; ?></b>', <?php echo $value->lat; ?>, <?php echo $value->lon; ?>, <?php echo $value->id; ?>],
		<?php
		$i++;
		}
		?>
	];
	var map = new google.maps.Map(document.getElementById('map'), {
	    zoom: 16,
	    center: new google.maps.LatLng(41.82398910, -71.41),
	    mapTypeId: google.maps.MapTypeId.ROADMAP
	});
	var infowindow = new google.maps.InfoWindow();
	var marker, i;
	for (i = 0; i < locations.length; i++) {
	    marker = new google.maps.Marker({
	        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
	        map: map,
	        class:'map'+i,
	        id: i
	    });
        (function(marker){
            google.maps.event.addDomListener(jQuery('<li/>').text(locations[i][0]).appendTo('#list')[0],'click',function(){
                google.maps.event.trigger(marker,'click',{});
            });
        })(marker);
	    google.maps.event.addListener(marker, 'click', (function (marker, i) {
	        return function () {
	        	console.log(marker);
	            infowindow.setContent(locations[i][0]);
	            infowindow.open(map, marker);
	        }
	    })(marker, i));
	}


</script>






				<?php endwhile; ?>

			<?php else : ?>

				<?php get_template_part( 'no-results', 'microsite' ); ?>

			<?php endif; ?>

			</div><!-- #content -->

			<?php do_action('presscore_after_content'); ?>

		<?php endif; // if content visible ?>

<?php get_footer(); ?>