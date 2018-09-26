<?

// fix errors /notices:
// 
//     view-source:http://pad.local.beta.tour.barelylegal.com/ajax/?page=1
//

define('SITECONFIG', 'pad_default');
require("../../../config.php");
require(MOBILE_LIB . 'route.php');
require(MOBILE_LIB . 'ajax.php');

// some config for content class
define('URL_OPTION_FAVE', 'favorites_only');
getSiteType() == 'tour' ? $tour = TRUE : $tour = FALSE;
// some config for ajax.php in library
getSiteType() == 'tour' ? $_SITETYPE = 'tour' :  $_SITETYPE = 'members';
// member ID...

$member_id = FALSE;
if (! $tour) {
    if(! isset($_SESSION)) {
        if (isset($_SERVER["PHP_AUTH_USER"])) $_SESSION["member"]["member_id"] = $_SERVER["PHP_AUTH_USER"];
    } elseif (!$_SESSION["member"]["member_id"]) {
        if (isset($_SERVER["PHP_AUTH_USER"])) $_SESSION["member"]["member_id"] = $_SERVER["PHP_AUTH_USER"];
    }

    if (isset($_SESSION["member"]["member_id"]) && !empty($_SESSION["member"]["member_id"])) {
        $member_id = $_SESSION["member"]["member_id"];
    }
}

// init content object
$content = new Hustler_Content_Ajax('pad', $tour, $member_id); // initialize for pad/iPad

// additional config
//$site = Paysite::get_current_site(SITECODE);
//$perpage = MOBILE_PERPAGE;
$perpage = 35;



/* inputs via _GET:

perpage
page
type
order
favorites

/type/order/filter/code/page/
/type/order/code/page/
/type/order/page/

    filter - a filter, like category and/or model
    code - determines additional filter and search options... favorites, and perpage

    models/0/filter/a/1/ -- nonfavorites
    models/0/filter/b/1/ -- favorites
    models/0/filter/c/1/ -- nonfavorites, 200 perpage


/type_specified/id_item_specified/page/

    model id, category id, video id

    model/2444/1/ -- nonfavorites, 200 perpage

*/

/*
 *  types (from content class): videos, photos, models, dvds
 * 
 *  "order" (from paysite::template get_content(): latest-updates, featured
 * 
 *  filters:
 * 
 *  code:
 *  
 */

$route = new Route();

$route->add('/get/con/', function() {
    global $content, $perpage, $tour;

    echo getContentAjax($content, array(
        'perpage' => $perpage
    ));
});

$route->add('/get/con/.+/', function($type) {
    global $content, $perpage, $tour;

    echo getContentAjax($content, array(
        'perpage' => $perpage,
        'type' => $type
    ));
});

// type/page
// or: inidividual video scene (w/o dl link)
$route->add('/get/con/.+/.+/', function($type, $b) {
    global $content, $perpage, $tour;

    if ($type == 'scene') {
        echo getContentAjax($content, array(
            'type' => 'scene',
            'scene' => $b,
            'dl' => FALSE,
        ));
    } else {
        echo getContentAjax($content, array(
            'perpage' => $perpage,
            'type' => $type,
            'page' => $b,
        ));
    }
    
});

//type/order/page/
// or: individual video scene (w/ dl link), dvd scenes, dvd detail, model detail, mag detail, faves check list
$route->add('/get/con/.+/.+/.+/', function($type, $b, $c) {
    global $content, $perpage, $tour, $member_id;

    if ($type == 'scene') { // video scene selected
        echo getContentAjax($content, array(
            'perpage' => 12, //unused
            'type' => 'scene',
            'page' => $c, //unused
            'order' => 0, //unused
            'code' => 'a', //unused
            'filter' => array(
                'scene' => $b,
            ),
            'username' => $member_id,
        ));
    } elseif ($type == 'dvd') { // DVD selected
        echo getContentAjax($content, array(
            'perpage' => 12,
            'type' => 'videos',
            'page' => $c,
            'order' => 0,
            'code' => 'a',
            'filter' => array(
                'dvd' => $b,
            ),
            'username' => $member_id,
        ));
    } elseif ($type == 'dvddetail') { // DVD detail only
        echo getContentAjax($content, array(
            'perpage' => 12,
            'type' => 'dvddetail',
            'page' => $c,
            'order' => 0,
            'code' => 'a',
            'filter' => array(
                'detail_dvd' => $b,
            ),
            'username' => $member_id,
        ));
    } elseif ($type == 'modeldetail') { // model detail only
        echo getContentAjax($content, array(
            'perpage' => 12,
            'type' => 'modeldetail',
            'page' => $c,
            'order' => 0,
            'code' => 'a',
            'filter' => array(
                'detail_mod' => $b,
            ),
            'username' => $member_id,
        ));
    }  elseif ($type == 'magdetail') { // magazine detail only
        echo getContentAjax($content, array(
            'perpage' => 12,
            'type' => 'magdetail',
            'page' => $c,
            'order' => 0,
            'code' => 'a',
            'filter' => array(
                'detail_mag' => $b,
            ),
            'username' => $member_id,
        ));
    }  elseif ($type == 'faves') { // faves
        // check options
        switch ($b) {
            default:
            case 'status':
                echo ajax_fave_check($_POST['scenes'], $c, $member_id);
                break;
        }
    }  else {
        echo getContentAjax($content, array(
            'perpage' => $perpage,
            'type' => $type,
            'page' => $c,
            'order' => $b
        ));
    }
});

//type/order/code/page/
$route->add('/get/con/.+/.+/.+/.+/', function($type, $order, $code, $page) {
    global $content, $perpage, $tour;

    echo getContentAjax($content, array(
        'perpage' => $perpage,
        'type' => $type, 
        'page' => $page,
        'order' => $order,
        'code' => $code,
    ));
});

//type/order/filter/code/page/
$route->add('/get/con/.+/.+/.+/.+/.+/', function($type, $order, $filter, $code, $page) {
    // for DVDs: type="dvds"
    global $content, $perpage, $member_id, $tour;

    $filters = getAjaxFilters($filter);
    echo getContentAjax($content, array(
        'perpage' => $perpage,
        'type' => $type,
        'page' => $page,
        'order' => $order,
        'code' => $code,
        'filter' => $filters,
        'username' => $member_id,
    ));
});

$route->add('/update/fav/.+/.+/.+', $callbackFav);

$route->submit();

//end

?>
