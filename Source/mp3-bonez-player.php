<?php
	/**
	 * Plugin Name: Mp3 Bonez Player
	 * Plugin URI: https://github.com/DolanDevelopment/mp3bonez-player
	 * Description: Wordpress responsive mp3 player and visualizer based on WavesurferJS
	 * Version: 1.0
	 * Author: Ryan Dolan
	 * Author URI: http://rdd-soft.com
	 * License: GPL2
	 */

	/*  Copyright 2014  Ryan Dolan  (email : DolanDevelopment@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*/

	function initialize() {

		if (function_exists('wp_enqueue_style')) {
			wp_enqueue_style('mp3bonez_style', get_bloginfo('wpurl') . '/wp-content/plugins/mp3-bonez-player/style.css');
		}


		if (function_exists('wp_enqueue_script')) {

			// for new libraries
			wp_register_script('jquery_1_11_0', get_bloginfo('wpurl') . '/wp-content/plugins/mp3-bonez-player/vendor/jquery-1.11.0.min.js');
			wp_register_script('wavesurfer', get_bloginfo('wpurl') . '/wp-content/plugins/mp3-bonez-player/vendor/wavesurfer.js');
			wp_register_script('wavesurfer_drawer', get_bloginfo('wpurl') . '/wp-content/plugins/mp3-bonez-player/vendor/drawer.js');
			wp_register_script('wavesurfer_drawer_canvas', get_bloginfo('wpurl') . '/wp-content/plugins/mp3-bonez-player/vendor/drawer.canvas.js');
			wp_register_script('webaudio', get_bloginfo('wpurl') . '/wp-content/plugins/mp3-bonez-player/vendor/webaudio.js');
			wp_register_script('mp3_bonez', get_bloginfo('wpurl') . '/wp-content/plugins/mp3-bonez-player/mp3bonez.js');
			
			wp_enqueue_script('jquery_1_11_0');
			wp_enqueue_script('wavesurfer');
			wp_enqueue_script('wavesurfer_drawer');
			wp_enqueue_script('wavesurfer_drawer_canvas');
			wp_enqueue_script('webaudio');
			wp_enqueue_script('mp3_bonez');
		}
	}


	//http://guid.us/GUID/PHP (modified to remove curly brackets)
	function getGUID(){
	    if (function_exists('com_create_guid')){
	        return com_create_guid();
	    }else{
	        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
	        $charid = strtoupper(md5(uniqid(rand(), true)));
	        $hyphen = chr(45);// "-"
	        $uuid = substr($charid, 0, 8).$hyphen
	            .substr($charid, 8, 4).$hyphen
	            .substr($charid,12, 4).$hyphen
	            .substr($charid,16, 4).$hyphen
	            .substr($charid,20,12);	            
	        return $uuid;
	    }
	}	

	function create_player($attrs){

		// get height and width information and use on inline styles
		extract( shortcode_atts( array(
				'src' => '',
				'height' => 72,
				'width' => 500,
				'src_relative_to_wp_content' => 'false',
				'wave_color' => '#CDCDCD',
				'progress_color' => '#474747',
				'responsive' => 'false'
		), $attrs ) );

		$play = get_bloginfo('wpurl') . "/wp-content/plugins/mp3-bonez-player/content/play-white.png";
		$stop = get_bloginfo('wpurl') . "/wp-content/plugins/mp3-bonez-player/content/stop-white.png";

		$containerHeight = $height . 'px';
		$containerWidth = $width . 'px';
		$halfContainerWidth = ($width / 2) . 'px';
		$buttonContainerWidth = ($width + 2) . 'px';
		$halfContainerHeight = ($height / 2) . 'px';

		$loadingTop = ($halfContainerHeight - 10) . 'px';
		$loadingLeft = ($halfContainerWidth - 50) . 'px';
		$pluginDir = get_bloginfo('wpurl') . "/wp-content/plugins/mp3-bonez-player/";
		$srcPath = '';
		if ($src_relative_to_wp_content === 'true')
			$srcPath = 	get_bloginfo('wpurl') . "/wp-content/" . $src;
		else
			$srcPath = $src;

		$fileName = basename($srcPath);
		$guid = getGUID();

		$registered = wp_get_current_user()->ID > 0;
		$downloadAnchor = $registered ? "<a href='{$srcPath}' type='application/octet-stream' download='{$fileName}'>download</a>" : "";

		return "
			<div id='{$guid}' src='{$srcPath}' style='width: {$containerWidth};' class='mp3-bonez'>
				<div class='wave'></div>
				<div class='loading' style='top: {$loadingTop}; left: {$loadingLeft}'>Loading...</div>
				<div class='mask' style='height: {$halfContainerHeight}; top: {$halfContainerHeight}; width:{$containerWidth};'></div>
				<div class='time'></div>
				<div class='button-container' style='width: {$buttonContainerWidth};'>
					<div class='button'><img src='{$play}' /></div>
					<div class='button'><img src='{$stop}' /></div>
					{$downloadAnchor}
				</div>
				<div class='clear'></div>
			</div>		
			<script type='text/javascript'>
				(function(){
					var element = document.getElementById('{$guid}');
					var waveElement = $(element).find('.wave').get()[0];
					var timeElement = $(element).find('.time').get()[0];
					var maskElement = $(element).find('.mask').get()[0];
					var loadingElement = $(element).find('.loading').get()[0];
					var responsive = {$responsive} ? true : false;
					window.mp3bonez.createPlayer(element, waveElement, timeElement, maskElement, loadingElement, {$width}, {$height}, '{$pluginDir}', '{$wave_color}', '{$progress_color}', responsive);
				})();
			</script>
		";
	}

	add_action('init', 'initialize');

	add_shortcode('mp3bonez-player', 'create_player');
?>