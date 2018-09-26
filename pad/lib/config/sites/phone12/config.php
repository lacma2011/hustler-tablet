<?php
require(BASEROOT . "includes/site_configuration.php");

//error_reporting(E_ALL);
ob_start();

$cachepath = CACHEPATH;

$cacheurl =  '/' . MOBILE_URL_ROOT . '/imagecache/'; // can't be relative because we use subfolders for routing content!
if (substr($cacheurl,0,2) == '//') {
    $cacheurl = substr($cacheurl,1);
}
define('CACHEURL', $cacheurl);
//echo $cacheurl;exit;

$docpath = rtrim(MEMBERS_BASE_DIR,'/'); // don't want end slash  (DOCROOT?)
session_start();
mysql_query("SET NAMES utf8");

// detect NON-mobile, forward to desktop link
mobile_device_detect(true,false,true,true,true,true,true, false, $docroot_desktop);

// use SITECODE instead of $_SITECODE ???

$_DATA["site_data"] = $site;
$_SESSION["site"] = $_SITECODE;

//$_SERVER["PHP_AUTH_USER"] = "156005";
//$_SESSION["member"]["member_id"] = "156005";
//$_SESSION["member"]["member_type"] = "trial";

if (1 == 0) {
    $_SESSION["member"]["member_id"] = 'jbordallo99';
}
$_SESSION["member"]["member_type"] = "full";
if (isset($_SERVER["PHP_AUTH_USER"])) if(!$_SESSION["member"]["member_id"]) $_SESSION["member"]["member_id"] = $_SERVER["PHP_AUTH_USER"];

if ($_SITETYPE == 'members') {
	$_USERDATA["member"]["preferences"] = Paysite::get_member_preferences();
}

$language = $_USERDATA["member"]["preferences"]["preference_language"];
if(!$language) $language = "en";
if(file_exists($baseroot . "languages/" . $language . "/pack.php")) require_once($baseroot . "languages/" . $language . "/pack.php");
require_once($baseroot . "languages/en/pack.php");

    
$alphabet = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");

//if(strstr($_SERVER["REQUEST_URI"], '?change_language=')) {
//	$_REQUEST["change_language"] = substr($_SERVER["REQUEST_URI"],	strlen($_SERVER["REQUEST_URI"]) - 2, 2);
//}
//if(isset($_REQUEST["change_language"])) {
//	$languages = Paysite::get_languages();
//	foreach($languages as $l) {
//		$l_array[] = $l["language_code"];	
//	}
//	if(in_array($_REQUEST["change_language"], $l_array)) {
//
//		mysql_query("UPDATE " . $_DATA["prefix"] . "member_settings SET `preference_language`='" . mysql_escape_string($_REQUEST["change_language"]) . "' WHERE member_id='" . (int)$_SESSION["member"]["member_id"] . "' LIMIT 1");		
//
//		header("Location: ?switched-language=" . $_REQUEST["change_language"]);
//		exit();
//	}
//}

//if(isset($_REQUEST["simple_search"])) {
//	header("Location: /mobile/search/" . seo_name($_REQUEST["simple_search"]) . "/");
//	exit();	
//}

if(isset($_REQUEST["language"])) {
	
}

Paysite::vanilla_login($_SESSION["member"]["member_id"]);
$sites = Paysite::get_all_sites();
$member = Paysite::get_member_info($_SESSION["member"]["member_id"]);
//$flagship_sites = Paysite::get_all_sites("flagship");
//$straight_sites = Paysite::get_all_sites("straight");
//$gay_sites = Paysite::get_all_sites("gay");

$STATE = $_SESSION["states"];


// remove gay sites in cache
if (isset($_DATA['cache']['sites_list']['default_xXx'])) {
    foreach($_DATA['cache']['sites_list']['default_xXx'] as $k=>$s) {
        if ($s['site_type'] == 'gay') {
            unset($_DATA['cache']['sites_list']['default_xXx'][$k]);
        }
    }
}

// sites with deals
$site_deals = array(array(
        'amount' => 1.99,
        'banner_text' => 'Sign Up $1.99 Only!',
        'banner_text_exclude' => 'Sign Up Now!',
        'sites' => array(2, 3, 4, 5, 52, 6, 44, 50, 7, 8, 18, 49, 14, 46, 40, 42, 13, 9, 41, 43, 39, 11), // site IDs with the deal.
                                    // (11 is special, really uses 42 for signup form)
        'exclude' => array(), // if excluding is easier
        ));

// QUERYSTRING_EXCEPTIONS: comma-delimited list of query string parameters to not pass into links, 
// because they rewrite to path names. check .htaccess: cat .htaccess | egrep -o [\&\|?][a-zA-Z0-9_]*= | sort | uniq
define('QUERYSTRING_EXCEPTIONS','category_id, id, junk, model_id, order, page, scene_id, site, string, sub, type, types'); 
// For now, we won't pass query string parametetrs, so leave FIXED_QUERY_STRING blank
// Besides, other than pagination bar, links aren't set up to add this fixed query string. Could use function lnk() or append them to the links.
if (1==0) {
    define('FIXED_QUERY_STRING', make_query_string(QUERYSTRING_EXCEPTIONS));
} else {
    define('FIXED_QUERY_STRING', '');
}
define('URL_OPTION_FAVE', 'favorites_only');
// these are query string parameters for filtering content
$qs_options = array(
    'category_id',
    'model_id',
    'site',
    URL_OPTION_FAVE,
);

//webmaster ID
if (isset($_GET['w'])) {
    $_SESSION['w'] = $_GET['w'];
}

//join page
$join_page = get_join_link($_SITECODE);

define('MOBILE_URL', make_mobile_url(MOBILE_URL_ROOT));
define('MOBILE_IMAGES', MOBILE_URL . 'images/v2/');
define('MOBILE_IMAGES_THEME', MOBILE_IMAGES . $_SITECODE . '/');

$_SITETYPE == 'members' ? define('MOBILE_AJAX', FALSE) : define('MOBILE_AJAX', FALSE); // to get content using ajax calls
//$_SITETYPE == 'members' ? define('MOBILE_AJAX', TRUE) : define('MOBILE_AJAX', FALSE); // to get content using ajax calls

define('MOBILE_JQUERY', TRUE); // To use features of Jquery Mobile. disable at the moment, styles are weird

// MOBILE_NAV_PAGECOUNT: number of pages to show in the bottom bar in landscape-- 
// Will actually be MOBILE_NAV_PAGECOUNT - 1 for pages with 'first' and 'last' links. Or MOBILE_NAV_PAGECOUNT + 1 for those without.
define('MOBILE_NAV_PAGECOUNT', 4);
define('MOBILE_NAV_PAGECOUNT_WIDE', 7); // view for landscape, or wider than portrait
define('MOBILE_NAV_BARLESS', TRUE); // no page bars
if($_SITETYPE != "tour") {
    define('MOBILE_NAV_SWIPE', TRUE); // allow swiping of the numbers
} else {
    define('MOBILE_NAV_SWIPE', FALSE);
}

define('MOBILE_URL_JOIN', $join_page);
define('MOBILE_SCRIPT', preg_replace('/.*\/([A-Za-z0-9_]*\.php)$/', '$1', $_SERVER['PHP_SELF']));

define('DESKTOP_VERSION', get_desktop_url($_SITETYPE, $site) . '/?fm=1'); // url for desktop site
define('MOBILE_PERPAGE', 6);
define('TOUR_RESET', FALSE); // TRUE = allow for user to reset limits of play and content pages browsed in the tour
define('TOUR_PLAY_LIMIT', 0); // how many clips that can be played in tour session
define('TOUR_MAX_PAGES', 10); // how many pages tour user can browse. TOUR_COUNT_ALL will influence
//define('TOUR_PLAY_LIMIT', 2000); // how many clips that can be played in tour session
//define('TOUR_MAX_PAGES', 6000); // how many pages tour user can browse
define('TOUR_COUNT_ALL', FALSE); // count all pages, not just content pages,
if ($_SITETYPE == 'tour') {
    // token will be created for downloading clips at a download page (like vid.php), not the content list pages. Tour
    // side can use this only, since members side doesn't have a download page yet.
    define('DL_PAGE_TOKEN', TRUE);
} else {
    define('DL_PAGE_TOKEN', FALSE);
}


// visual elements
define('IMG_NARROW', FALSE); // landscape images are narrower
//
// check for mobile platform
// these values correspond to important folder or file names
define('IPHONE', 'default'); // prepared for iPhone portrait, should be default
define('IPAD', 'ipad');
define('DROID', 'droid');
$tmp = strtolower($_SERVER['HTTP_USER_AGENT']);
if (strpos($tmp, 'ipad')) {
    $platform = IPAD;
} elseif (strpos($tmp, 'android')) {
    $platform = DROID;
} else {
    $platform = IPHONE;
}

if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) {
    define('IE_DESKTOP', TRUE); // IE desktop browser
}


//$_DATA["IS_DEV_SERVER"] = true;
//echo "dev:" . $_DATA["IS_DEV_SERVER"];

?>