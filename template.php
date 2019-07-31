<?php

/**
 * @file
 * Contains the theme's functions to manipulate Drupal's default markup.
 *
 * A QUICK OVERVIEW OF DRUPAL THEMING
 *
 *   The default HTML for all of Drupal's markup is specified by its modules.
 *   For example, the comment.module provides the default HTML markup and CSS
 *   styling that is wrapped around each comment. Fortunately, each piece of
 *   markup can optionally be overridden by the theme.
 *
 *   Drupal deals with each chunk of content using a "theme hook". The raw
 *   content is placed in PHP variables and passed through the theme hook, which
 *   can either be a template file (which you should already be familiary with)
 *   or a theme function. For example, the "comment" theme hook is implemented
 *   with a comment.tpl.php template file, but the "breadcrumb" theme hooks is
 *   implemented with a theme_breadcrumb() theme function. Regardless if the
 *   theme hook uses a template file or theme function, the template or function
 *   does the same kind of work; it takes the PHP variables passed to it and
 *   wraps the raw content with the desired HTML markup.
 *
 *   Most theme hooks are implemented with template files. Theme hooks that use
 *   theme functions do so for performance reasons - theme_field() is faster
 *   than a field.tpl.php - or for legacy reasons - theme_breadcrumb() has "been
 *   that way forever."
 *
 *   The variables used by theme functions or template files come from a handful
 *   of sources:
 *   - the contents of other theme hooks that have already been rendered into
 *     HTML. For example, the HTML from theme_breadcrumb() is put into the
 *     $breadcrumb variable of the page.tpl.php template file.
 *   - raw data provided directly by a module (often pulled from a database)
 *   - a "render element" provided directly by a module. A render element is a
 *     nested PHP array which contains both content and meta data with hints on
 *     how the content should be rendered. If a variable in a template file is a
 *     render element, it needs to be rendered with the render() function and
 *     then printed using:
 *       <?php print render($variable); ?>
 *
 * ABOUT THE TEMPLATE.PHP FILE
 *
 *   The template.php file is one of the most useful files when creating or
 *   modifying Drupal themes. With this file you can do three things:
 *   - Modify any theme hooks variables or add your own variables, using
 *     preprocess or process functions.
 *   - Override any theme function. That is, replace a module's default theme
 *     function with one you write.
 *   - Call hook_*_alter() functions which allow you to alter various parts of
 *     Drupal's internals, including the render elements in forms. The most
 *     useful of which include hook_form_alter(), hook_form_FORM_ID_alter(),
 *     and hook_page_alter(). See api.drupal.org for more information about
 *     _alter functions.
 *
 * OVERRIDING THEME FUNCTIONS
 *
 *   If a theme hook uses a theme function, Drupal will use the default theme
 *   function unless your theme overrides it. To override a theme function, you
 *   have to first find the theme function that generates the output. (The
 *   api.drupal.org website is a good place to find which file contains which
 *   function.) Then you can copy the original function in its entirety and
 *   paste it in this template.php file, changing the prefix from theme_ to
 *   parabola_. For example:
 *
 *     original, found in modules/field/field.module: theme_field()
 *     theme override, found in template.php: parabola_field()
 *
 *   where parabola is the name of your sub-theme. For example, the
 *   zen_classic theme would define a zen_classic_field() function.
 *
 *   Note that base themes can also override theme functions. And those
 *   overrides will be used by sub-themes unless the sub-theme chooses to
 *   override again.
 *
 *   Zen core only overrides one theme function. If you wish to override it, you
 *   should first look at how Zen core implements this function:
 *     theme_breadcrumbs()      in zen/template.php
 *
 *   For more information, please visit the Theme Developer's Guide on
 *   Drupal.org: http://drupal.org/node/173880
 *
 * CREATE OR MODIFY VARIABLES FOR YOUR THEME
 *
 *   Each tpl.php template file has several variables which hold various pieces
 *   of content. You can modify those variables (or add new ones) before they
 *   are used in the template files by using preprocess functions.
 *
 *   This makes THEME_preprocess_HOOK() functions the most powerful functions
 *   available to themers.
 *
 *   It works by having one preprocess function for each template file or its
 *   derivatives (called theme hook suggestions). For example:
 *     THEME_preprocess_page    alters the variables for page.tpl.php
 *     THEME_preprocess_node    alters the variables for node.tpl.php or
 *                              for node--forum.tpl.php
 *     THEME_preprocess_comment alters the variables for comment.tpl.php
 *     THEME_preprocess_block   alters the variables for block.tpl.php
 *
 *   For more information on preprocess functions and theme hook suggestions,
 *   please visit the Theme Developer's Guide on Drupal.org:
 *   http://drupal.org/node/223440 and http://drupal.org/node/1089656
 */


/**
 * Override or insert variables into the maintenance page template.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("maintenance_page" in this case.)
 */
/* -- Delete this line if you want to use this function
function parabola_preprocess_maintenance_page(&$variables, $hook) {
  // When a variable is manipulated or added in preprocess_html or
  // preprocess_page, that same work is probably needed for the maintenance page
  // as well, so we can just re-use those functions to do that work here.
  parabola_preprocess_html($variables, $hook);
  parabola_preprocess_page($variables, $hook);
}
// */
/**
 * Override or insert variables into the html templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("html" in this case.)
 */
include_once('features/font/google.web.fonts.inc');
include_once('features/slideshow/logics.inc');
function parabola_preprocess_html(&$variables, $hook) {
  global $theme_key;
  $current_theme_path = drupal_get_path('theme', 'parabola');
  drupal_add_css($current_theme_path . '/features/font/fonts.settings.css', 'file');

  /* BEGIN SKIN SETTING */
  // Load skin to apply for theme
  $skin = theme_get_setting('skins_type')?theme_get_setting('skins_type'):'default';
   if(isset($_COOKIE['skin'])) {$skin = $_COOKIE['skin']?$_COOKIE['skin']:$skin;}
  drupal_add_css($current_theme_path . '/css/skins/'.$skin.'/'.$skin.'.css', array('group' => CSS_THEME));
  // Load background image
  if(theme_get_setting('bgs_type') && theme_get_setting('bgs_type') != "none") {
    drupal_add_css('body{background-image: url("'.theme_get_setting('bgs_type').'")}', array('group'=>CSS_THEME, 'type' => 'inline'));
  }
  if(theme_get_setting('bgs_img_type') && theme_get_setting('bgs_img_type') != "none") {
   str_replace("%body%", "black", "<body text='%body%'>");  
    $background_link = str_replace("bg/thumb", "bg", theme_get_setting('bgs_img_type'));
    drupal_add_css('body{background-image: url("'.$background_link.'")}', array('group'=>CSS_THEME, 'type' => 'inline'));
    if(theme_get_setting('position_choose')) {
      drupal_add_css('body{background-position: '.theme_get_setting('position_choose').'}', array('group'=>CSS_THEME, 'type' => 'inline'));
    }
    if(theme_get_setting('bg_repeat') == FALSE) {
      drupal_add_css('body{background-repeat: no-repeat}', array('group'=>CSS_THEME, 'type' => 'inline'));
    }
  }
  /* END SKIN SETTING */

  // Font family settings
  $fonts = array(
    'bf'  => 'base_font',
    'snf' => 'site_name_font',
    'ssf' => 'site_slogan_font',
    'mmf' => 'main_menu_font',
    'ptf' => 'page_title_font',
    'ntf' => 'node_title_font',
    'ctf' => 'comment_title_font',
    'btf' => 'block_title_font'
  );
  $families = parabola_get_font_families($fonts, $theme_key);
  if (!empty($families)) {
    foreach ($families as $family) {
       $variables['classes_array'][] = $family;
    }
  }
  $variables['layout_chosen'] = theme_get_setting('layouts');
  if($variables['layout_chosen']=="three-col-grail"){
	$variables['classes_array'][] = 'three-col-grail';
  }
  else if($variables['layout_chosen']=="three-col-right"){
	$variables['classes_array'][] = 'three-col-right';
  }
  else {
	$variables['classes_array'][] = 'three-col-left';
  }
  // The body tag's classes are controlled by the $classes_array variable. To
  // remove a class from $classes_array, use array_diff().
  //$variables['classes_array'] = array_diff($variables['classes_array'], array('class-to-remove'));
}
// */

/**
 * Override or insert variables into the page templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("page" in this case.)
 */
function parabola_process_page(&$variables, $hook) {

}
function parabola_preprocess_page(&$variables, $hook) {
  $current_theme_path = drupal_get_path('theme', 'parabola');
  $variables['gpmenu'] = '';
  if (theme_get_setting('menu_type') == 3) {
   drupal_add_css($current_theme_path . '/features/menu/cssdropdown/gmenu.css');
                    // Get the entire main menu tree
                    $main_menu_tree = menu_tree_all_data(theme_get_setting('cssmenu_element'));

                    // Add the rendered output to the $mainmenu variable
                    $variables['gpmenu'] = menu_tree_output($main_menu_tree);
                    $submenu_width = (theme_get_setting('cssmenu_width')?theme_get_setting('cssmenu_width'):'200');
                    drupal_add_css('#menu-bar .menu ul {width: '.$submenu_width.'px;} #menu-bar ul.menu li li:hover ul,#menu-bar ul.menu li li li:hover ul{left: '.$submenu_width.'px;}', array('group' => CSS_THEME, 'type' => 'inline'));
  } 
  elseif (theme_get_setting('menu_type') == 1) {
  drupal_add_css($current_theme_path . '/features/menu/superfishdropdown/gmenu.css');
	drupal_add_js($current_theme_path . '/features/menu/superfishdropdown/superfish.js');
	drupal_add_js($current_theme_path . '/features/menu/superfishdropdown/easing.js');
	drupal_add_js($current_theme_path . '/features/menu/superfishdropdown/invoke-superfish.js');
	
	// Get the entire main menu tree
	$main_menu_tree = menu_tree_all_data(theme_get_setting('menu_element'));

	// Add the rendered output to the $mainmenu variable
	$variables['gpmenu'] = menu_tree_output($main_menu_tree);

	// pass the text variables to javascript
						
	if(theme_get_setting('properties_show')) {
		foreach (theme_get_setting('properties_show') as $property => $value) {
			if ($value) {
				drupal_add_js(array('menu' => array('animationShow' => array($property => 'show'))), 'setting');
			}
		}
	}
	if(theme_get_setting('properties_hide')) {
		foreach (theme_get_setting('properties_hide') as $property => $value) {
			if ($value) {
				drupal_add_js(array('menu' => array('animationHide' => array($property => 'hide'))), 'setting');
			}
		}
	}
	drupal_add_js(
		array(
			'menu' => array(
				'menu_speed_show' => theme_get_setting('speed_show')?theme_get_setting('speed_show'):400,
				'menu_easing_show' => theme_get_setting('easing_show')?theme_get_setting('easing_show'):'easeInBounce',
				'menu_speed_hide' => theme_get_setting('speed_hide')?theme_get_setting('speed_hide'):200,
				'menu_easing_hide' => theme_get_setting('easing_hide')?theme_get_setting('easing_hide'):'easeInElastic',
				'submenu_width' => theme_get_setting('width')?theme_get_setting('width'): 250,
				'menu_delay' => theme_get_setting('delay')?theme_get_setting('delay'):500,
			)
		),
			array('type' => 'setting')
		);  
  }
  
   $variables['useslideshow']   = (theme_get_setting('slide_usage') != 0) ? TRUE : FALSE;
   $slides = parabola_show_slides();
   $variables['slide_image'] = parabola_slides_markup($slides);
   // Add css file.
   if($variables['useslideshow']) {
        drupal_add_css($current_theme_path . '/features/slideshow/slideshow.css');
		drupal_add_js($current_theme_path . '/features/slideshow/jquery.flexslider.js');
		drupal_add_js($current_theme_path . '/features/slideshow/jquery.zaccordion.min.js');
		drupal_add_js($current_theme_path . '/features/slideshow/jquery.easing.1.3.js');
   }
	
}
// */

/**
 * Override or insert variables into the node templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("node" in this case.)
 */
/* -- Delete this line if you want to use this function
function parabola_preprocess_node(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');

  // Optionally, run node-type-specific preprocess functions, like
  // parabola_preprocess_node_page() or parabola_preprocess_node_story().
  $function = __FUNCTION__ . '_' . $variables['node']->type;
  if (function_exists($function)) {
    $function($variables, $hook);
  }
}
// */

/**
 * Override or insert variables into the comment templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("comment" in this case.)
 */
/* -- Delete this line if you want to use this function
function parabola_preprocess_comment(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');
}
// */

/**
 * Override or insert variables into the region templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("region" in this case.)
 */

function parabola_preprocess_region(&$variables, $hook) {
  // Don't use Zen's region--sidebar.tpl.php template for sidebars.
  //if (strpos($variables['region'], 'sidebar_') === 0) {
  //  $variables['theme_hook_suggestions'] = array_diff($variables['theme_hook_suggestions'], array('region__sidebar'));
  //}
}
// */
function parabola_preprocess_comment_wrapper(&$variables, $hook) {
  if($variables['node']->comment_count) {
	$variables['total_comments_in_node']= $variables['node']->comment_count;
  }
  else {
	$variables['total_comments_in_node'] = 0;
  }
}

function parabola_preprocess_comment(&$variables, $hook) {
  // Add pubdate to submitted variable.
  $variables['pubdate'] = '<time pubdate datetime="' . format_date($variables['comment']->created, 'custom', 'F d,Y') . '">' . format_date($variables['comment']->created, 'custom', 'F d,Y g')  . '</time>';
  $variables['submitted'] = t('!username said : !datetime ago', array('!username' => $variables['author'], '!datetime' => format_interval((time() - $variables['comment']->created) , 2)));
}
/**
 * Override or insert variables into the block templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("block" in this case.)
 */
/* -- Delete this line if you want to use this function
function parabola_preprocess_block(&$variables, $hook) {
  // Add a count to all the blocks in the region.
  // $variables['classes_array'][] = 'count-' . $variables['block_id'];

  // By default, Zen will use the block--no-wrapper.tpl.php for the main
  // content. This optional bit of code undoes that:
  //if ($variables['block_html_id'] == 'block-system-main') {
  //  $variables['theme_hook_suggestions'] = array_diff($variables['theme_hook_suggestions'], array('block__no_wrapper'));
  //}
}
// */
