<?php
 
/*
  Plugin Name: WordPress Spartan templating engine
  Idea from: Darko Goleš http://inchoo.net/author/darko.goles/
  Author: Andrei Draganescu
  Author URI: http://andreidraganescu.info
 */

//Main spartan file
require_once dirname(__FILE__) . '/controller/my.php'; 

function spartanAutoLoad() {
	
	// init the app
	$app = my::app();

	// lets set the models path to the theme directory
	$app->model_path = get_template_directory().'/models';

	// add wp db to the app
	$app->connection(get_bloginfo('wpurl'), DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);
	$app->server(get_bloginfo('wpurl'), 'no-log', 'no-track','no-profile','no-debug','UTC','not',false);
	
	// setup the spartan specifics (base tag, folders etc)
	$theme_directory = get_template_directory();
	$theme = strrchr($theme_directory, '/');
	$app->theme = '../../../themes'.$theme;
	$app->base_tag = get_bloginfo('template_directory');

	// a framework flag that breaks the normal uri to template route
	$app->forced_templates = true;
}

/**
 * Adding a filter for each default template type so that spartan can route properly
 */
function setup_spartan_default_tpls()
{

	$indexfile = get_template_directory() . '/index.php';

	// detect the currect display type and set it accordingly
	add_filter( "single_template", create_function('', '$app = my::app(); $app->forced_template = "single"; return "'.$indexfile.'";') ); 
	add_filter( "search_template", create_function('', '$app = my::app(); $app->forced_template = "search"; return "'.$indexfile.'";') ); 
	add_filter( "page_template", create_function('', '$app = my::app(); $app->forced_template = "page"; return "'.$indexfile.'";') ); 
	add_filter( "home_template", create_function('', '$app = my::app(); $app->forced_template = "index"; return "'.$indexfile.'";') ); 
	add_filter( "category_template", create_function('', '$app = my::app(); $app->forced_template = "category"; return "'.$indexfile.'";') ); 
	add_filter( "attachment_template", create_function('', '$app = my::app(); $app->forced_template = "attachment"; return "'.$indexfile.'";') ); 
	add_filter( "archive_template", create_function('', '$app = my::app(); $app->forced_template = "archive";  return "'.$indexfile.'";') ); 
	add_filter( "404_template", create_function('', '$app = my::app(); $app->forced_template = "404"; return "'.$indexfile.'";') ); 
	add_filter( "author_template", create_function('', '$app = my::app(); $app->forced_template = "author"; return "'.$indexfile.'";') ); 
	
}



// and here we go, we're hooked!
add_action('template_redirect', 'setup_spartan_default_tpls');
add_action('init', 'spartanAutoLoad');

?>
