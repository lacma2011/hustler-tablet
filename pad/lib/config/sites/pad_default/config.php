<?php
//define('CACHEURL',  url_double_slash(DOCROOT . '/') . 'imagecache/');
define('CACHEURL',  'imagecache/'); // stick to relative links

//echo "<br>\nDOMAIN:" . DOMAIN . "<br>\n SITECODE:" . SITECODE . "<br>\n PAD_JOIN (LINK):" . PAD_JOIN . 
//        "<br>\n CONTENT: " . SITECODE_CONTENT . "<br>\n CACHEURL:" . CACHEURL;
//echo "<br>CACHEPATH:" . CACHEPATH . "<br>\n BASEROOT:" . BASEROOT . "<br>\n DOCROOT:" . DOCROOT . 
//        "<br>\n DOMAIN: " . DOMAIN . "<br>\n PAD_URL:" . PAD_URL;
//echo "<br>:" .  "<br>\n MOBILE_BASE:" . MOBILE_BASE . "<br>\n MOBILE_URL_ROOT:" . MOBILE_URL_ROOT . 
//        "<br>\n PAD_BASE_DIR: " . PAD_BASE_DIR . "<br>\n MEMBERS_BASE_DIR:" . MEMBERS_BASE_DIR;
//exit;

// following are not really used but keeps a class also used by phone12 site happy

define('MOBILE_PERPAGE', 6);
define('MOBILE_URL', make_mobile_url(MOBILE_URL_ROOT));
// following are needed for some functions in mobile app's includes/functions.php.
// Read mobile's config.php for more info.
define('DL_PAGE_TOKEN', TRUE); // since we will get dl token from an extra ajax call for getting download clip
define('MOBILE_TEST', FALSE);
define('FIXED_QUERY_STRING', '');

?>