<?php

// activate/add additional scripts
add_action('wp_enqueue_scripts','add_child_scripts');

function add_child_scripts()
{

	wp_register_style('ww_main_css', get_stylesheet_directory_uri() . '/css/main.min.css', false, filemtime( get_stylesheet_directory().'/css/main.min.css'));
	wp_enqueue_style('ww_main_css');

	wp_register_script( 'ww_main_js', get_stylesheet_directory_uri() . '/js/scripts.min.js', array( 'jquery' ), filemtime( get_stylesheet_directory().'/js/scripts.min.js'), true );
	wp_enqueue_script('ww_main_js');
}

//Dequeue Styles
function project_dequeue_unnecessary_styles() {
    wp_dequeue_style( 'fontawesome' );
        wp_deregister_style( 'fontawesome' );
}
add_action( 'wp_print_styles', 'project_dequeue_unnecessary_styles' );

add_action('wp_head', 'hook_javascript');

function hook_javascript() {
    ?>
        <script defer src="https://use.fontawesome.com/releases/v5.0.8/js/all.js"></script>
    <?php
}
