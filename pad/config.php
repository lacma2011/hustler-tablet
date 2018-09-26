<?php

// FOR LIVE: set define('PAD_SITE_CONFIG', 'live');
// 
// 
// TODO: have a better way of filtering out featured content with the Paysite class
// for now we are not sorting, since Paysite allows you to choose featured but not sort except for by latest
//apache_setenv('ENVIRONMENT','PRODUCTION'); // TODO: REMOVE THIS!!! just for demo on beta
//apache_setenv('ENVIRONMENT','DEVELOPMENT'); // With this on, you will see content that aren't released yet.
define('TESTING', TRUE); // turn on custom error handler, show all errors

// important app/system settings

//options for configs:
//  live  : live and beta sites. Ex. http://beta.tour.hustlerhd.com, http://tour.hustlerhd.com
//  local : jerome's machine
//  dev-hd
//  dev-hg
//  dev-tours
//  dev-his 
//        : misc test sites for hustlerhd, hustlergirls, hisvideo on dev-jbordallo.beta

define('PAD_SITE_CONFIG', 'local');

if (PAD_SITE_CONFIG == 'live') {
    $docroot = '/'; // the folder where the app is located from domain root
    $settings['tour_url_basepath'] = ''; //um may not be important anymore. was basically docroot.
    define('PAD_BASE_DIR', '/web/sites/hustler/hustler-cms/pad/');
    //define('PAD_URL_ROOT', '/');
    require(PAD_BASE_DIR . 'lib/config/functions.php'); // load all the other configs, classes, functions, etc.
    $tour_domain = getDomain();
    define('MEMBERS_BASE_DIR', '/web/sites/hustler/hustler-cms/');
    $baseroot = '/web/sites/hustler/cms.lfpcontent.com/cms/'; // hmpth.. paysite template needs it
    //mobile app paths
    define('MOBILE_BASE', MEMBERS_BASE_DIR . 'pad/sites/phone12/'); // location of old mobile app
    define('MOBILE_LIB', PAD_BASE_DIR . 'lib/includes/');
    if (1 == 1) {
        // ?? these mobile url/paths aren't needed...??
        define('MOBILE_URL_ROOT', ''); 
        // following two are subdomain and path to mobile tour... assuming it uses same server name.
        $settings['mobile_subdomain'] = '';
        // ?? we don't actually have to use mobile_path... except when needing to redirect to mobile, but why??
        $settings['mobile_path'] = '/'; // the path in url to mobile tour site, start with / for root    
    }
    // tour app
    define('TOUR_BASE_DIR', '/var/www/new-tours/');
    define('TOUR_LIB', TOUR_BASE_DIR . 'lib/');
    define('TOUR_CLASSES', TOUR_LIB . 'classes/');
    // error
    $settings['errors_skip'] = array( // if getting errors, skip from these files
        '/web/sites/hustler/cms.lfpcontent.com-trunk/includes/paysite_template.php',
        '/web/sites/hustler/cms.lfpcontent.com-trunk/includes/classes/content.class.php',
        '/web/sites/hustler/cms.lfpcontent.com-trunk/includes/site_configuration.php',
        '/web/sites/hustler/cms.lfpcontent.com/cms/includes/paysite_template.php',
        '/web/sites/hustler/cms.lfpcontent.com/cms/includes/classes/content.class.php',
        '/web/sites/hustler/cms.lfpcontent.com/cms/includes/site_configuration.php',
    );    // if domain was not defined on page, let's override content
    //if (!defined('DOMAIN')) $settings['content_override'] = 'barely-legal';
} elseif (substr(PAD_SITE_CONFIG, 0, 4) == 'dev-') {
    // REMOTE DEV (dev-jbordallo). Could be newtours, hustlerhd, hustlergirls, hisvideo
    if (PAD_SITE_CONFIG == 'dev-jbordallo') {
        $docroot = 'pad/sites/' . SITECONFIG . '/'; // the folder where the app is located from domain root
        $settings['tour_url_basepath'] = ''; //um may not be important anymore. was basically docroot.
        define('PAD_BASE_DIR', '/web/sites/hustler/development/jbordallo/hustler-cms/pad/');
        //define('PAD_URL_ROOT', '/');
        require(PAD_BASE_DIR . 'lib/config/functions.php'); // load all the other configs, classes, functions, etc.
        $tour_domain = getDomain();
    } elseif (PAD_SITE_CONFIG == 'dev-tours') { //another dev server install
    } elseif (PAD_SITE_CONFIG == 'dev-his') { //another dev server install
    } elseif (PAD_SITE_CONFIG == 'dev-hg') { //another dev server install
    }
    define('MEMBERS_BASE_DIR', '/web/sites/hustler/development/jbordallo/hustler-cms/');
    $baseroot = '/web/sites/hustler/development/jbordallo/cms.lfpcontent.com/cms/'; // hmpth.. paysite template needs it
    //mobile app paths
    define('MOBILE_BASE', MEMBERS_BASE_DIR . 'pad/sites/phone12/'); // location of old mobile app
    define('MOBILE_LIB', PAD_BASE_DIR . 'lib/includes/');
    if (1 == 1) {
        // ?? these mobile url/paths aren't needed...??
        define('MOBILE_URL_ROOT', 'pad/sites/phone12'); 
        // following two are subdomain and path to mobile tour... assuming it uses same server name.
        $settings['mobile_subdomain'] = '';
        // ?? we don't actually have to use mobile_path... except when needing to redirect to mobile, but why??
        $settings['mobile_path'] = '/pad/sites/phone12/'; // the path in url to mobile tour site, start with / for root    
    }
    // tour app
    define('TOUR_BASE_DIR', '/var/www/new-tours/');
    define('TOUR_LIB', TOUR_BASE_DIR . 'lib/');
    define('TOUR_CLASSES', TOUR_LIB . 'classes/');
    // error
    $settings['errors_skip'] = array( // if getting errors, skip from these files
        '/web/sites/hustler/development/jbordallo/cms.lfpcontent.com-trunk/includes/paysite_template.php',
        '/web/sites/hustler/development/jbordallo/cms.lfpcontent.com-trunk/includes/classes/content.class.php',
        '/web/sites/hustler/development/jbordallo/cms.lfpcontent.com-trunk/includes/site_configuration.php',
        '/web/sites/hustler/development/jbordallo/svn/trunk/trunk/cms.lfpcontent.com/cms/includes/paysite_template.php',
        '/web/sites/hustler/development/jbordallo/svn/trunk/trunk/cms.lfpcontent.com/cms/includes/classes/content.class.php',
        '/web/sites/hustler/development/jbordallo/svn/trunk/trunk/cms.lfpcontent.com/cms/includes/site_configuration.php',
    );    // if domain was not defined on page, let's override content
    //if (!defined('DOMAIN')) $settings['content_override'] = 'barely-legal';
} else {
    // LOCAL, jerome's machine
    define('MEMBERS_BASE_DIR', "/var/www/hustler-members/");
    $baseroot = '/var/www/hustler-members/cms/'; //hmph... paysite_template needs it
    //$docroot = 'pad/sites/pad_default/'; // the folder where the app is located from domain root
    $docroot = '/'; // the folder where the app is located from domain root
    //define('PAD_URL_ROOT', '/');
    define('PAD_BASE_DIR', '/var/www/hustler-members/pad/');
    require(PAD_BASE_DIR . 'lib/config/functions.php'); // load all the other configs, classes, functions, etc.
    $tour_domain = getDomain();
//$tour_domain = 'hustler.com';
    //mobile app paths
    define('MOBILE_BASE', MEMBERS_BASE_DIR . 'pad/sites/phone12/'); // location of old mobile app
    define('MOBILE_LIB', PAD_BASE_DIR . 'lib/includes/');
    if (1 == 1) {
        // ?? these mobile url/paths aren't needed...??
        define('MOBILE_URL_ROOT', ''); 
        // following two are subdomain and path to mobile tour... assuming it uses same server name.
        $settings['mobile_subdomain'] = '';
        // ?? we don't actually have to use mobile_path... except when needing to redirect to mobile, but why??
        $settings['mobile_path'] = '/'; // the path in url to mobile tour site, start with / for root    
    }
    // tour app
    define('TOUR_BASE_DIR', '/var/www/new-tours/');
    define('TOUR_LIB', TOUR_BASE_DIR . 'lib/');
    define('TOUR_CLASSES', TOUR_LIB . 'classes/');
    // error
    $settings['tour_url_basepath'] = ''; //um may not be important anymore. was basically docroot.
    $settings['errors_skip'] = array( // if getting errors, skip from these files
            //'/var/www/cms/svn_trunk/cms.lfpcontent.com/includes/paysite_template.php',
            //'/var/www/cms/svn_trunk/cms.lfpcontent.com/includes/classes/content.class.php',
            //'/var/www/cms/svn_trunk/cms.lfpcontent.com/includes/site_configuration.php',
        );
    // if domain was not defined on page, let's override content
    //if (!defined('DOMAIN')) $settings['content_override'] = 'barely-legal';
}

//set the domain. Maybe be overriden prior to this file being included
if (!defined('DOMAIN')) define('DOMAIN', $tour_domain);

if (TRUE === TESTING) {
    define('ERROR_REP', E_ALL);
    $errors_skip = $settings['errors_skip'];
} else {
    define('ERROR_REP', E_USER_ERROR);
}
error_reporting(ERROR_REP);

require(PAD_BASE_DIR . 'lib/config/init.php'); // load all the other configs, classes, functions, etc.
