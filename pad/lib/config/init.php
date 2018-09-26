<?
// load the mobile functions
// NOTE that we never use link in PAD_BASE_DIR/includes/functions or mobile_detect.php
require(MOBILE_LIB . 'functions.php');
require(MOBILE_LIB . 'mobile_detect.php');
if (TRUE === TESTING) set_error_handler("custom_error_handler");

// class autoloader
require(PAD_BASE_DIR . 'lib/class_autoloader.php');

// create the pad (this site) URL (needs function make_mobile_url, so has to come here)
// misnomer calling it make_mobile_url, we just get root of the current domain and add tour url path
$tmp = make_mobile_url('') . '/' . $settings['tour_url_basepath'];
define('PAD_URL', url_double_slash($tmp));
define('DOCROOT', url_double_slash(PAD_URL . $docroot));
$baseroot = url_double_slash($baseroot);
define('BASEROOT', url_double_slash($baseroot)); // preferred over $baseroot. Only paysite_template needs $baseroot
//cache paths
define('CACHEPATH', url_double_slash(MEMBERS_BASE_DIR . '/') . "imagecache/");

define('MOBILE_JS', DOCROOT . 'js/');
define('MOBILE_JS_AJAX', MOBILE_JS . 'hustler/ajax/');
define('PAD_JS', DOCROOT . 'js/');
define('MOBILE_AJAX_SCRIPT', DOCROOT . 'ajax/');

// just some constants
define('AJAX_CODE_PAD', 'pad'); // identified by hmajax (js object)


require(BASEROOT . "includes/paysite_template.php");
//require(BASEROOT . "includes/site_configuration.php");
error_reporting(ERROR_REP); // because right now paysite_template sets it to 0 :(


require(PAD_BASE_DIR . 'lib/config/sites.php');
define('SITECODE', $NICHE_TOUR_VARS[DOMAIN]['site_code']);
if (empty($settings['content_override']))
    define('SITECODE_CONTENT', SITECODE);
else
    define('SITECODE_CONTENT', $settings['content_override']);

define('PAD_JOIN', 'https://secure.hustler.com/signup/signup.php?site=' . $NICHE_TOUR_VARS[DOMAIN]['site_id']);


//$sites = Paysite::get_all_sites();
//print_r($sites);


$site = Paysite::get_current_site(SITECODE);


require('sites/' . SITECONFIG . '/config.php');
// initialize
$page = 1;
?>