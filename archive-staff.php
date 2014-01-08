<?php
$args = array(
	'post_type' => 'staff',
	'posts_per_page' => -1,
	'orderby' => 'menu_order',
	'order' => 'DESC',
);

$staff = new wp_query($args);

if($staff->have_posts()) {
	$galaxy_width = 700; // total galaxy size
	$left_offset = 0;
	
	$star_buffer = 200; // leave room for sun
	$planet_count = $staff->post_count;
	$orbital_step_size = round(($galaxy_width - ($star_buffer / 2)) / $planet_count) - 10; 
	$zindex = 99;
	$i = 0;
	$sun_page = 581;

	$galaxy  = '<ul class="solarsystem"><li class="sun">';
	$galaxy .= '<a href="'.get_permalink($sun_page).'" class="fancybox fancybox.ajax" >';
	$galaxy .= '<span></span></a></li>';
	
	$menu  = '<ul class="galaxy-menu"><li class="planet-menu" id="sun">';
	$menu .= '<a href="'.get_permalink($sun_page).'" class="fancybox fancybox.ajax">';
	$menu .= 'About Tea Leaves Health</a></li>';
	
	$css   = '<style type="text/css">';
	
	while($staff->have_posts()) : $staff->the_post();
		if($i == 0){
			$orbit_size = $star_buffer;
		} else {
			$orbit_size  = $i * ($orbital_step_size) + ($star_buffer);
		}
		$i++;
		$diameter    = get_post_meta($post->ID, '_cmb_planetoid_diameter', true);
		$diameter    = round($diameter*1.2);
		// bitcheck to determine if odd, make even
		if($diameter & 1){
			$diameter++;
		}
		$p_radius    = round($diameter / 2);
		$color       = get_post_meta($post->ID, '_cmb_planetoid_color', true);
		$color_2     = get_post_meta($post->ID, '_cmb_planetoid_color_secondary', true);
		$orbit_start = get_post_meta($post->ID, '_cmb_planetoid_orbital_start', true);
		$orbit_speed = get_post_meta($post->ID, '_cmb_planetoid_orbital_speed', true);
		$radius      = round($orbit_size / 2) + 2;
		$top_pos     = round(($galaxy_width - $orbit_size) / 2);
		$left_pos    = $top_pos + $left_offset;
		$span_top    = $p_radius * -1;
		$span_left   = ($orbit_size / 2) - ($p_radius);
		$duration    = get_post_meta($post->ID, '_cmb_planetoid_orbital_speed', true);
		$start_pos   = get_post_meta($post->ID, '_cmb_planetoid_orbital_start', true);
		$end_pos     = $start_pos + 360;
		
		// reverse some of them!
		if($i&2){
			$temp = $start_pos;
			$start_pos = $end_pos;
			$end_pos = $temp;
		}
		
		$galaxy .= '<li class="'.$post->post_name.'">';
		$galaxy .= '<a href="'.get_permalink($post).'" class="fancybox fancybox.ajax" >';
		$galaxy .= '<span>'.$post->post_title.'</span></a></li>';
		
		$menu .= '<li class="planet-menu" id="'.$post->post_name.'">';
		$menu .= '<a href="'.get_permalink($post).'" class="fancybox fancybox.ajax" >';
		$menu .= $post->post_title.'</a></li>';
		
		$css  .= 
		'ul.solarsystem li.'.$post->post_name.'{
			width:'.$orbit_size.'px;
			height:'.$orbit_size.'px;
			-webkit-border-radius: '.$radius.'px;
			-moz-border-radius: '.$radius.'px;
			-ms-border-radius: '.$radius.'px;
			border-radius: '.$radius.'px;
			top: '.$top_pos.'px;
			left: '.$left_pos.'px;
			z-index: '.$zindex--.';
		}
		ul.solarsystem li.'.$post->post_name.' span {
			width:'.$diameter.'px;
			height:'.$diameter.'px;
			background: '.$color.';
			background-image: -webkit-gradient(
				linear,
				left bottom,
				left top,
				color-stop(0.22, '.$color.'),
				color-stop(1, '.$color_2.')
			);
			background-image: -moz-linear-gradient(
				center bottom,
				'.$color.' 22%,
				'.$color_2.' 100%
			);
			background-image: -ms-linear-gradient(
				top,
				'.$color.' 22%,
				'.$color_2.' 100%
			);
			
			
			top: '.$span_top.'px;
			left: '.$span_left.'px;
			-webkit-border-radius: '.$p_radius.'px;
			-moz-border-radius: '.$p_radius.'px;
			border-radius: '.$p_radius.'px;
			-ms-border-radius: '.$p_radius.'px;
		}
		ul.solarsystem li.'.$post->post_name.' {
			-webkit-animation-iteration-count:infinite;
			-webkit-animation-timing-function:linear;
			-webkit-animation-name:orbit_'.$post->post_name.';
			-webkit-animation-duration:'.$duration.'s;
			
			-moz-animation-iteration-count:infinite;
			-moz-animation-timing-function:linear;
			-moz-animation-name:orbit_'.$post->post_name.';
			-moz-animation-duration:'.$duration.'s;
			
			-ms-animation-iteration-count:infinite;
			-ms-animation-timing-function:linear;
			-ms-animation-name:orbit_'.$post->post_name.';
			-ms-animation-duration:'.$duration.'s;
		}
		
		@-webkit-keyframes orbit_'.$post->post_name.' { 
			from { 
				-webkit-transform:rotate('.$start_pos.'deg) 
			} to { 
				-webkit-transform:rotate('.$end_pos.'deg) 
			} 
		}
		
		@-moz-keyframes orbit_'.$post->post_name.' {
			from { 
				-moz-transform:rotate('.$start_pos.'deg) 
			} to { 
				-moz-transform:rotate('.$end_pos.'deg) 
			} 
		}
		@-ms-keyframes orbit_'.$post->post_name.' {
			from {
				-ms-transform:rotate('.$start_pos.'deg)
			} to {
				-ms-transform:rotate('.$end_pos.'deg)
			}
		}
		'
		;
		
	endwhile;
	
	$galaxy .= '</ul>';
	$menu   .= '</ul>';
	$css    .= '</style>';
	
	echo $galaxy, $menu, $css;
	?>
	<script type="text/javascript">
		jQuery(function($){
			$('ul.solarsystem li').on(
				{
					mouseover: function(){
						var $this_planet = $(this);
						var $this_planet_id = $this_planet.attr('class');
						var $this_menu = $('ul.galaxy-menu li#' + $this_planet_id);
						//$('ul.solarsystem li').removeClass('active');
						//$('ul.galaxy-menu li').removeClass('active');
						$this_planet.addClass('active');
						$this_menu.addClass('active');
					},
					mouseleave: function(){
						$('ul.solarsystem li').removeClass('active');
						$('ul.galaxy-menu li').removeClass('active');
					}
				});
			
			$('ul.galaxy-menu li').on(
				{
					mouseover: function(){
						var $this_menu = $(this);
						var $this_planet_id = $(this).attr('id');
						var $this_planet = $('.' + $this_planet_id );
						//$('ul.solarsystem li').removeClass('active');
						//$('ul.galaxy-menu li').removeClass('active');
						$this_menu.addClass('active');
						$this_planet.addClass('active');
					},
					mouseleave: function(){
						$('ul.solarsystem li').removeClass('active');
						$('ul.galaxy-menu li').removeClass('active');
					}
				});
		});
	</script>
	<?php
}
