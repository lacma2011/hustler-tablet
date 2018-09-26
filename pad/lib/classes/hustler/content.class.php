<?php
// TODO: re-do class names without underscores and use namespaces when servers are on 5.3

define('PAGE_DEFAULT', 1); // page 1 of	latest content
define('LATEST_CLIP', 0); // index of the latest cip
define('TOUR_IMAGES_COUNT', 1); // number of tour images to get for getting that sort of content. Should only be one!
define('IMAGECACHE_UDELAY', 0); // number of microseconds between each imagecache call (20000 = 20 milliseconds)

// these are put in the site config instead
//define('SITECODE_CONTENT', SITECODE); // useful for overriding with content of another site code!
//define('SITECODE_CONTENT', 'barely-legal'); // ex. let's see barely-legal instead of our current site.

// these options should be used on the page as a query string parameter name. The content classes will 
// actually use parameter names
define('OPTION_SORT', 'sort');
define('OPTION_MODEL', 'model_id');

abstract class Hustler_Content {
    private $tour; // TRUE = tour site; FALSE = members site
    private $mobile; //  TRUE = this is a mobile site
    
    // basics for gettng content from the DB using content classes made before
    // also provides tools for processing of data, such as the cached image maker
    
    private $page;
    //private $perpage;
    public $imageMax; // flag (TRUE) if we need max image
    public $params;
    public $filters; // a second way of loading params... not through $_GET
    public $code;
    public $opt_type; //  not used yet. an optional input parameter to further filter content
    public $type; // mainly used for making special ui code
    public $content_raw; // content as delivered straight from the CMS class

    private $imgTour; // there are tour images for scenes... NULL to not get them, # to get up to that many
    private $gay; // some content types need to check for gender. TRUE for males
    
    public $content; // scenes after processing/parsing data

    public $info; // details of the last query
    
    private $tourJoinLink; // a tour join link may be needed

    // some constants for $type... conform to content type words used in the url
    static $TYPE_VIDEOS = 'videos';
    static $TYPE_MODELS = 'models';
    static $TYPE_MODEL_DETAIL = 'modeldetail';
    static $TYPE_DVDS = 'dvds';
    static $TYPE_DVD_DETAIL = 'dvddetail';
    static $TYPE_PHOTOS = 'photos';
    static $TYPE_PHOTOS_DETAIL = 'photodetail';
    static $TYPE_MAGAZINES = 'mags';
    static $TYPE_MAGAZINE_DETAIL = 'magdetail';
    static $TYPE_CAT = 'cat';
    static $TYPE_SITES = 'sites';
    static $TYPE_SCENE_DETAIL = 'scene'; // a scene can be a photoset, video, or both

    function __construct($tour = FALSE, $tourJoinLink = FALSE) {
        $this->params = array();
        $this->filters = NULL;
        $this->code = NULL;
        $this->page = PAGE_DEFAULT;
        $this->maxImage = FALSE;
        $this->info = NULL;
        $this->extra_count = NULL; // extra content for special content calls :/
        $this->opt_type = 'all'; // we don't use this yet, so do all;
        $this->imgTour = NULL;
        $this->gay = FALSE;
        $this->tour = $tour;
        $this->mobile = FALSE;
        $this->tourJoinLink = '';
        if (FALSE !== $tourJoinLink) {
            $this->tourJoinLink = $tourJoinLink;
        }
    }
    
    function reportParams() {
        // return the params set for this content object
    }
    
    public function setMobile() {
        $this->mobile = TRUE;
    }

    public function setMember($id) {
        if ($this->tour != TRUE) {
            $this->setParam('member_id', $id);
        }
    }

    public function setTypeVideo() {
        $this->type = self::$TYPE_VIDEOS;
    }
    
    public function getContent($num, $page) {
        // get parameters from input... in this case URL querystring
        
        // model ID
        if ($this->getParam(OPTION_MODEL) == TRUE) {
            // because we got a model ID, we need to not make this featured
            // turn OFF for now
            //$this->setParam(OPTION_SORT, 'latest_updates');
        }

        // page #
        if (! empty($page)) { // page # is a little special
            $this->params['page'] = $page;
        } elseif ($this->getParam('page')) {
            // do nothing
        } else {
            $this->params['page'] = PAGE_DEFAULT;
        }
        // PAGEBAR_STOP : we will limit the highest page # you can actually view
        if (defined('PAGEBAR_STOP')) {
            if (is_numeric(PAGEBAR_STOP) && $this->params['page'] > (int) PAGEBAR_STOP) {
                $page = PAGEBAR_STOP;
                $this->params['page'] = PAGEBAR_STOP;
            }
        }
        // get latest only
        if ($this->code == 'latest') {
            $this->params['page'] = 1;
        }

        // other overrides are possible...
        if (! empty($this->sortOverride)) {
            $this->setParam(OPTION_SORT, $this->sortOverride);
        }
        // for related scene searches only
        if (!empty($this->params[OPTION_SORT]) && $this->params[OPTION_SORT] == 'related' && !empty($this->scene)) {
            $this->setParam('search', $this->scene);
        }

        if (FALSE === $this->mobile) {
            $this->setParam('mobile_clips', FALSE); // we want non-mobile scenes as well
        } else {
            $this->setParam('mobile_clips', TRUE); // we want non-mobile scenes as well
        }

        //$this->perpage = $num;
        $type = $this->type;
        if ($type == self::$TYPE_MODELS) {
              if ($this->code == "searchModels") {
                  $content = new Hustler_Contentbasic_Search($this->params['search_text'], $this->code, $this->params['search_category'], SITECODE_CONTENT, $this->tour );
              } else {
                $this->setParam('videos_only', FALSE); // we want performers with non-video as well
                $tmp = SITECODE_CONTENT;
                if (! empty($this->params['site'])) {
                    $tmp = $this->params['site'];
                }
                $content = new Hustler_Contentbasic_Performer($tmp, $this->tour);
             }
            //$content->selectAll(); // get models with photos and/or videos
        } elseif ($type == self::$TYPE_MODEL_DETAIL) {

            $content = new Hustler_Contentbasic_Performer(SITECODE_CONTENT, $this->tour, $this->getMemberId());
        } elseif ($type == self::$TYPE_MAGAZINES) {
            $content = new Hustler_Contentbasic_Papermag(SITECODE_CONTENT, $this->tour);
            if ($this->validateCode('home') || $this->validateCode('homeretry')) {
                // home page wants all magazines
                $content->setAll();
            } elseif (! empty($this->params['site'])) {
                switch ($this->params['site']) {
                    case 'barely-legal':
                        $content->setBL();
                        break;
                    case 'hustlers-taboo':
                        $content->setTaboo();
                        break;
                }
            }
        } elseif ((SITECODE == 'hustler-girls' || SITECODE == 'hustler-hd') && $type == self::$TYPE_PHOTOS && $this->mobile !== TRUE) {
            // this is really just for Hustler HD & Hustler Girls TOUR site
//echo "\n getting magazine content";
            $content = new Hustler_Contentbasic_Mag(SITECODE_CONTENT, $this->tour);
            switch(SITECODE) {
                case 'hustler-girls':
                    $content->setBL();
                    break;
                default:
                    $content->setHustler();
                    break;
            }
        } elseif($type == self::$TYPE_DVDS) {

            // dvds
            if ($this->code == "searchDvds") {

                $content = new Hustler_Contentbasic_Search($this->params['search_text'], $this->code,  $this->params['search_category'], SITECODE_CONTENT, $this->tour );
            } else {
               $content = new Hustler_Contentbasic_Dvd(SITECODE_CONTENT, $this->tour);
             }
        
        } elseif ($type == self::$TYPE_VIDEOS) {    // videos and photos
                
                if ($this->code == "searchVideos") {
                 
                    $content = new Hustler_Contentbasic_Search($this->params['search_text'], $this->code, $this->params['search_category'], SITECODE_CONTENT, $this->tour );
                } else {
                    $content = new Hustler_Contentbasic_Simple(SITECODE_CONTENT, $this->tour);
                }
                
        } elseif ($type == self::$TYPE_PHOTOS) {
                
                if ($this->code == "searchPhotos")
                    $content = new Hustler_Contentbasic_Search($this->params['search_text'], $this->code, $this->params['search_category'], SITECODE_CONTENT, $this->tour );
                else
                    $content = new Hustler_Contentbasic_Photo(SITECODE_CONTENT, $this->tour);
        } elseif ($type == self::$TYPE_SITES) {

            $tmp = Paysite::get_sites_sorted();
            $this->content_raw['data'] = array();
            foreach ($tmp as $k=>$s) {
                $this->content_raw['data'][] = array(
                    'site_code' => $k,
                    'date' => $s,
                );
            }
            $this->content_raw['info']['totalRecords'] = count($this->content_raw);
            $this->info = $this->content_raw['info'];
            $this->content = array(
                'data' => $this->content_raw['data'],
            );
            return $this;
            // END SITES
         }
            
            //$content->setParam('flagged', TRUE); // enable when we do want flagged clips!
        

        $content->setParam('perPage', $num);
        if ($this->code == 'latest') {
            $content->setParam('perPage', 1); // get only latest clip
        }
        
        // for gay specifications
        if (TRUE === $this->gay) {
            $content->setParam('gender', 'male');
        }
        // query options
// we can get input en masse from url
//        foreach ($this->params as $p) {
//            if (! empty($_GET[$p])) {
//                $this->params[$p] = $_GET[$p];
//            }
//        }

//print_r($this->params);
        foreach ($this->params as $option=>$value) {
            $content->setParam($option, $value);
//            if ($option == URL_OPTION_FAVE) {
//                // favorites -- DISABLE.
//                $content->setParam('member_id', NULL);
//            }
        }
        
// TODO (maybe): to filter out videos that we don't want in the tour, we have to set flags here to affect get() :/

        if (!empty($this->imgTour)) {
            $content_return = $content->setImagesTour($this->imgTour)->get($page);
        } else {
            $content_return = $content->get($page);
        }
//echo "\ncontent call:\n" . print_r($content_return['data'], TRUE);
        
        // get the "extra" content.  This is if we need content from the next page.
        //$this->params['page'] // because adding page in get() doesn't work?
        if ($this->isExtra()) {
            $extra = $this->extra_count;
            
            if (round($num / $extra) == $num / $extra) { // if we can paginate to previous content using $extra as perpage...
//echo "\n got round";
                $content->setParam('perPage', $extra);
                $extra_page = ($this->params['page'] * ($num / $extra)) + 1;
//echo "\nextra_page = $extra_page";
                if (!empty($this->imgTour)) {
                    $tmp = $content->setImagesTour($this->imgTour)->get($extra_page); // we paginate up to previous content, then add one for next page
                } else {
                    $tmp = $content->get($extra_page); // we paginate up to previous content, then add one for next page
                }
                // be nice and restore old values:
                $content->setParam('perPage', $num);
            } else {
                if (!empty($this->imgTour)) {
                    $tmp = $content->setImagesTour($this->imgTour)->get($this->params['page'] + 1);
                } else {
                    $tmp = $content->get($this->params['page'] + 1);
                }
            }
            if (!empty($tmp['data'])) {
                foreach ($tmp['data'] as $k=>$t) {
                    if ($k < $extra) $content_return['data'][] = $t;
                }
            }
            
//echo "\nEXTRA content call:\n";print_r($tmp);

        }
//echo "\nNEW content added:\n";print_r($content_return);exit;
//echo "\nNEW content added:\n";foreach($tmp['data'] as $t) {echo "\n" . $t['scene_name'] . ' ' . $t['scene_id'];};
//exit;
        $this->content_raw = $content_return;
        $this->info = $content_return['info'];

        if ($this->info['totalRecords'] != 0) {
            
            if ($this->type == self::$TYPE_VIDEOS && isset($this->hd)) {
                $processed = $this->process($this->hd);
            } else {
                $processed = $this->process();
            }
            
            $this->content = $processed;
        } else {
            $this->content = array('data' => NULL);
        }
        
        return $this;
    }
        
    function setPage ($page) {
        $this->page = $page;
    }

    function setPerpage ($perpage) {
        $this->perpage = $perpage;
    }

    function clear($id) {
        $this->params = array();
    }
    
    public function getSiteType() {
        if ($this->tour === FALSE) {
            return 'members';
        } else {
            return 'tour';
        }
    }
    
    public function addExtra($num) {
        $this->extra_count = $num;
        return $this;
    }
    
    public function clearExtra() {
        $this->extra_count = NULL;
    }
    
    private function isExtra() {
        if (! empty($this->extra_count) && $this->extra_count > 0) {
            return TRUE;
        }
        return FALSE;
    }
    
    public function data() {
        return $this->content;
    }
    
    public function makeImage($image_location, $img_x, $img_y, $hd = FALSE) {
        TRUE === defined('SS_BASE_DIR') ? $tmp = SS_BASE_DIR . DEV_SS_DEFAULT : $tmp = $image_location; //SS_BASE_DIR for a local machine only. TODO: REMOVE
        //$tmp = $image_location;
        //return Content::image_handler(CACHEURL, CACHEPATH, $tmp, $img_x, $img_y); // old one, no watermark for HD
        // additional parameters for Content::image_handler below (we make $hd true: 
        //    $resize = false, $watermark = false, $trimborder=false, $nodetect=true, $passthru=false, $hd=false, $classic=false);
//        usleep(IMAGECACHE_UDELAY); // breathe time for next one in microseconds 
        return Content::image_handler(CACHEURL, CACHEPATH, $tmp, $img_x, $img_y, false, false, false, true, false, $hd);
    }
    
    // FILTERS: what we call the parameters for our search that are NOT from the $_GET/$_REQUEST objects
    
    public function setCode($code, $username = NULL) {
        $this->code = $code;
        return $this;
    }

    public function setCodeArray($code, $username = NULL) {
        $this->code[$code] = TRUE;
        return $this;
    }

    public function validateCode($code) {
        if (is_array($this->code)) {
            if (! empty($this->code[$code]) && $this->code[$code] === TRUE) {
                return TRUE;
            }
        } else {
            if ($this->code == $code) {
                return TRUE;
            }
        }
        return $this;
    }

    public function setFilters($filters, $username = NULL) {
        $this->filters = $filters;
        foreach ($filters as $k=>$f) {
            switch($k) {
                // I like to use codes to obfuscate the actual db fieldnames from the client
                case 'search_text':
                    $this->setParam('search_text', $f);
                    break;
                 case 'search_category':
                    $this->setParam('search_category', $f);
                    break;
                case 'cat':
                    $this->setParam('category_id', $f);
                    break;
                case 'mod':
                case 'detail_mod':
                    $this->setParam('model_id', $f);
                    if (!empty($username)) {
                        $this->setParam('member_id', $username);
                    }
                    break;
                case 'fav':
                    $this->setParam('favorites_only', TRUE);
                    if (!empty($username)) {
                        $this->setParam('member_id', $username);
                    }
                    break;
                case 'dvd':
                    $this->setParam('videos_in_dvd', $f);
                    break;
                case 'detail_dvd':
                    $this->setParam('dvd_id', $f);
                    if (!empty($username)) {
                        $this->setParam('member_id', $username);
                    }
                    break;
                case 'detail_mag':
                    $this->setParam('mag_id', $f);
                    if (!empty($username)) {
                        $this->setParam('member_id', $username);
                    }
                case 'scene':
                    $this->setParam('scene_id', $f);
                    if (!empty($username)) {
                        $this->setParam('member_id', $username);
                    }
                    break;
                case 'photoset_id':
                    $this->setParam('photoset_id', $f);
                    break;    
                case 's':
                    $this->setParam('site', $f);
                    break;
                default:
                    //$this->setParam($k, $f); // we don't want free input
            }
        }
        return $this;
    }

    private function setParam($name, $value) {
        $this->params[$name] = $value;
    }

    public function setMax() {
        // set image size of latest clip to maximum size
        // must be done before a get()
        // TODO: make it possible to make max size for any image?
        $this->imageMax = TRUE;
        return $this;
    }
    
    public function unsetMax() {
        // do NOT set image size of latest clip to maximum size
        // must be done before a get()
        // TODO: make it possible to make max size for any image?
        $this->imageMax = FALSE;
        return $this;
    }
    
    public function getParam($param) {
        if (! empty($this->filters)) return FALSE; // setting filters overrides this way of getting parameters
//echo "<BR>checking: " . $param . "<BR>";
        if (!empty($_GET[$param])) {
//echo "exists<BR>";
            $s = $_GET[$param];
//echo "check if $s is an option for $param <BR>";
            if (array_key_exists($s, $this->getOptions($param))
                    || $this->getOptionsAny($param)
                    ) {
//echo "OKAY!<BR>";
                $this->setParam($param, $s);
                return TRUE;
            } else {
//echo "HMMM on $param";
            }
        }
//else { echo "..was found empty<BR>";} 
//echo "NO SUCH KEY: $param <BR>";
        return NULL;
    }
    
    public function getOptions($param) {
        switch ($param) {
            case OPTION_SORT:
                $tmp = array(
                    'latest_updates' => 'Recent Additions', //default
                    'oldest' => 'Oldest',
                    'most_viewed' => 'Most Viewed',
                    'title' => 'Name',
                );
                break;
            default:
                $tmp = array();
                break;
        }
        // in a scene, add the "related" option
        if (!empty($this->scene)) {
            $tmp['related'] = 'Related Scenes'; // 
        }
        return $tmp;

    }
    
    public function getOptionsAny($param) {
        // these options can expect all kinds of values
        switch ($param) {
            case 'page':
            case OPTION_MODEL:
                return TRUE;
                break;
            default:
                break;
        }
        return FALSE;
    }
    
    public function makeOptionsDiv($param) {
        // So far, only used for Sort Order! $param = OPTION_SORT
        // Will actually create the links too
        // Won't print a bar if there's only one record TODO: is that fine??
        if ($this->content_raw['info']['totalRecords'] <= 1 && $this->params[OPTION_SORT] != 'related') {
            return '<div class="clear"></div>';
            return NULL;
        }
        //TODO: add pages to links
        $options = $this->getOptions($param);
        $selected = NULL;
        if (!empty($_GET[$param])) {
            
            if (array_key_exists($_GET[$param], $options)) {
                $selected = $_GET[$param];
            }
        }
        if ($selected == NULL && $param == OPTION_SORT) $selected = 'latest_updates'; // ugly, need to set default somehow
//echo "\n\n<BR> _GET=" . print_r($_GET, TRUE);
//echo "\n\n<BR> selected = $selected";
        
        $scene = '';
        // for scene pages only
        if (!empty($this->scene)) {
            $scene = 'vid/' . $this->scene . '/';
        }

        $model = '';
        // for model pages only
        if (!empty($this->params[OPTION_MODEL])) {
            // if on this class we have once gotten a model, assume it's a model's page.
            // So let's add the path to this model's page
            $model = 'model/' . $this->params[OPTION_MODEL] . '/';
        }

        // note: $scene and $model can both be blank, but can't both be non-blank
        

        $str = '<div class="options-' . $param . '">';
        foreach ($options as $k=>$p) {
            $selected == $k ? $select_class = ' option-selected' : $select_class = '';
            $str .= '<div class="' . $select_class . ' option-' . $param . '" ><a href="' . TOUR_URL . $scene . $model . $this->type . '/' . $this->opt_type . '/' . $k . '/">' . $p . '</a></div>';
        }
        $str .= '</div>';
        //$str .= '<div class="clear"></div>';
        return $str;
    }
    
    public function makeOptionsDivHomepage($param) {
        // This is special... used only for home page, where links are built for two pagebars: videos and pics
        // Won't print a bar if there's only one record TODO: is that fine??
        if ($this->content_raw['info']['totalRecords'] <= 1 && !empty($this->params[OPTION_SORT]) && $this->params[OPTION_SORT] != 'related') {
            return '<div class="clear"></div>';
            return NULL;
        }
        $options = $this->getOptions($param);
        $selected = NULL;
        if (!empty($_GET[$param])) {
            
            if (array_key_exists($_GET[$param], $options)) {
                $selected = $_GET[$param];
            }
        }
        if ($selected == NULL && $param == OPTION_SORT) $selected = 'latest_updates'; // ugly, need to set default somehow
//echo "\n\n<BR> _GET=" . print_r($_GET, TRUE);
//echo "param = $param" . OPTION_SORT;
//echo "\n\n<BR> selected = $selected";
  
// We don't have scene pages with two pagebars (YET!) so don't use this option yet.
// This is already applied in makeOptionsDiv()
//        $scene = '';
//        // for scene pages only
//        if (!empty($this->scene)) {
//            $scene = 'vid/' . $this->scene . '/';
//        }

        $str = '<div class="options-' . $param . '">';
        foreach ($options as $k=>$p) {
            $selected == $k ? $select_class = ' option-selected' : $select_class = '';
            if (defined('VID_PARAM_TYPE')) { // we got a url with type/sort/page parameters for videos and photos
                if ($this->type == self::$TYPE_PHOTOS) {
                    // photos bar
                    $str_vid = self::$TYPE_VIDEOS . '/' . VID_PARAM_TYPE . '/' . VID_PARAM_SORT . '/' . VID_PARAM_PAGE . '/';
                    $str_pic = self::$TYPE_PHOTOS . '/' . PHOTO_PARAM_TYPE . '/' . $k . '/1/">' . $p;   // start at page 1, new option $p / $k
                } else {
                    // videos bar
                    $str_vid = self::$TYPE_VIDEOS . '/' . VID_PARAM_TYPE . '/' . $k . '/1/';  // start at page 1, new option $p / $k
                    $str_pic = self::$TYPE_PHOTOS . '/' . PHOTO_PARAM_TYPE . '/' . PHOTO_PARAM_SORT . '/' . PHOTO_PARAM_PAGE . '/">' . $p;
                }
            } else {
                // none selected yet, fill with defaults:
                // type=all
                // sort=latest_updates
                // page=1
                if ($this->type == self::$TYPE_PHOTOS) {
                    // photos bar
                    $str_vid = self::$TYPE_VIDEOS . '/all/latest_updates/1/';
                    $str_pic = self::$TYPE_PHOTOS . '/all/' . $k . '/1/">' . $p;
                } else {
                    // videos bar
                    $str_vid = self::$TYPE_VIDEOS . '/all/' . $k . '/1/';
                    $str_pic = self::$TYPE_PHOTOS . '/all/latest_updates/1/">' . $p;
                }
            }
            
            // if model is selected...
            $model = '';
            // for model pages only
            if (!empty($this->params[OPTION_MODEL])) {
                // if on this class we have once gotten a model, assume it's a model's page.
                // So let's add the path to this model's page
                $model = 'model/' . $this->params[OPTION_MODEL] . '/';
            }

            $str .= '<div class="option-' . $param . $select_class . '" ><a href="' . TOUR_URL . $model . $str_vid . $str_pic . '</a></div>';
        }
        $str .= '</div>';
        return $str;
    }
    
    public function makePagebarDiv($pages_to_show, $homepage = FALSE) {
        // Must run only after getContent since it checks content
        // Won't print a bar if there's only one page TODO: is that fine??
        if ($this->content_raw['info']['totalPages'] == 1) {
            return '<div class="clear"></div>';
            return NULL;
        }
        $total_records = $this->content_raw['info']['totalRecords'];
        $perpage = $this->content_raw['info']['params']['perPage'];
        $page = $this->content_raw['info']['currentPage'];
        $site_type = $this->getSiteType();
        $barless = TRUE;

        // PAGEBAR_STOP : override for limiting max page to navigate to
        $tmp = $total_records;
        if (defined('PAGEBAR_STOP')) {
            if ($page > (int) PAGEBAR_STOP ) {
                $page = (int) PAGEBAR_STOP;
            }
            if (is_numeric(PAGEBAR_STOP) && round(floor($total_records / $perpage)) > (int) PAGEBAR_STOP) {
                $tmp = PAGEBAR_STOP * $perpage;
            }
        }

        $pagination = new Hustler_Mobile_Pagebar($tmp, $perpage, $page, $site_type); // pagination bar

        if (defined ('PAGEBAR_STOP')) {
            $pagination->setMaxLink($this->tourJoinLink);
            $pagination->setMaxPage($total_records); // need to, since we're using setMaxLink which overrides the total records
            //echo "pages_to_show $pages_to_show  vs   PAGEBAR_STOP " . PAGEBAR_STOP;
            if (defined('PAGEBAR_STOP_SHOWMORE')) {
                $pagination->setMaxShowMore(PAGEBAR_STOP_SHOWMORE);
            }
        }


        // build the special links array for the page bar class to construct the links properly for this app
        $arr = array(substr(TOUR_URL, 0, strlen(TOUR_URL)-1)); // remove trailing slash
        if (!empty($this->scene)) {
            // if on this class we have once gotten a video scene, assume it's a video scene's page.
            // So let's add the path to this video scene's page
            $arr[] = 'vid';
            $arr[] = $this->scene;
        }
        if (!empty($this->params[OPTION_MODEL])) {
            // if on this class we have once gotten a model, assume it's a model's page.
            // So let's add the path to this model's page
            $arr[] = 'model';
            $arr[] = $this->params[OPTION_MODEL];
        }
        
        
        
        if (! $homepage) {
            // add the type
            $arr[] = $this->type;
            // the option
            $arr[] = 'all'; // the special options field, not used yet
            if (!empty($this->params[OPTION_SORT])) {
                $arr[] = $this->params[OPTION_SORT];
            } else {
                $arr[] = 'latest_updates';
            }
            $arr[] = 'PAGE_NUM'; // page_num will get replaced by actual page number
        } else {
            if (defined('VID_PARAM_TYPE')) { // we got a url with two sets of type/sort/page parameters for videos and photos
                $arr[] = self::$TYPE_VIDEOS;
                $arr[] = VID_PARAM_TYPE;
                $arr[] = VID_PARAM_SORT;
                $this->type == self::$TYPE_VIDEOS ? $arr[] = 'PAGE_NUM' : $arr[] = VID_PARAM_PAGE;
                $arr[] = self::$TYPE_PHOTOS;
                $arr[] = PHOTO_PARAM_TYPE;
                $arr[] = PHOTO_PARAM_SORT;
                $this->type == self::$TYPE_PHOTOS ? $arr[] = 'PAGE_NUM' : $arr[] = PHOTO_PARAM_PAGE;
                // let's do the anchor labels... #pagebar_photos or #pagebar_videos
                $this->type == self::$TYPE_PHOTOS ? $arr[] = '#pagebar_photos' : $arr[] = '#pagebar_videos';
            } else {
                $arr[] = self::$TYPE_VIDEOS;
                $arr[] = 'all';
                $arr[] = 'latest_updates';
                $this->type == self::$TYPE_VIDEOS ? $arr[] = 'PAGE_NUM' : $arr[] = 1; // default to page 1
                $arr[] = self::$TYPE_PHOTOS;
                $arr[] = 'all';
                $arr[] = 'latest_updates';
                $this->type == self::$TYPE_PHOTOS ? $arr[] = 'PAGE_NUM' : $arr[] = 1;
            }
        }
        $pagination->setSpecialLinks($arr);

        $pagebar = $pagination->makePagedNav($pages_to_show, $barless);
        return $pagebar;
    }
    
    public function makePagebarDivHomepage($pages_to_show) {
        return $this->makePagebarDiv($pages_to_show, TRUE);
    }
    

    public function setTourImages($num) {
        $this->imgTour = $num;
        return $this;
    }
    
    public function setTourImagesNone() {
        $this->imgTour = NULL;
        return $this;
    }


    // may be extraneous... but easier to read
    public function getSortOptions() {
        return $this->getOptions(OPTION_SORT);
    }
    
    // get first scene only, up to $offset
    // return an array
    // return empty array if no clips found
    public function getFirst($offset = 1) {
        $tmp = array();
        for($k = 0; $k < $offset; $k++) {
            if (! empty($this->content['data'][$k])){
                switch ($this->type) {
                    case self::$TYPE_VIDEOS:
                        $tmp[$k] = new Hustler_Item_Scene($this->content['data'][$k]);
                        break;
                    case self::$TYPE_MODELS:
                        $tmp[$k] = new Hustler_Item_Model($this->content['data'][$k]);
                        break;
                    case self::$TYPE_PHOTOS:
                        $tmp[$k] = new Hustler_Item_Pic($this->content['data'][$k]);
                        break;
                    default :
                        break;
                }
                
            }
        }
        if (count($tmp) == 1) {
            return $tmp[0];
        }
        return $tmp;
    }
    
    
    // get all scenes besides the first one, or $offset
    //return an array
    // return empty array if no clips found
    public function getNonFirst($offset = 1) {
        // removing first scene
        if (empty($this->content['data'])) return array();
        $tmp = $this->content['data'];
        for ($x = 0; $x < $offset; $x++) {
            array_shift($tmp);
        }
        $obj = array();
        foreach($tmp as $t) {
            switch ($this->type) {
                case self::$TYPE_VIDEOS:
                    $obj[] = new Hustler_Item_Scene($t);
                    break;
                case self::$TYPE_MODELS:
                    $obj[] = new Hustler_Item_Model($t);
                    break;
                case self::$TYPE_PHOTOS:
                    $obj[] = new Hustler_Item_Pic($t);
                    break;
                default :
                    break;
            }
        }
        return $obj;
    }
    
    // some options not always available to all types of content
    function setGay() {
        $this->gay = TRUE;
        
    }
    
    function unsetGay() {
        $this->gay = FALSE;
    }

    public function getNumPages() {
        return $this->content_raw['info']['totalPages'];
    }
    
    
    
    abstract function reset($siteFormat);
    
    abstract function get($num, $page);
    
    public function setOrder($order = 0) {
        // order: latest_updates, featured (!!!!)

// TODO(?): why isn't ordered set any other way than $sortOverride in parent::getContent() ????
        switch ((int)$order) {
            case 1:
                $order = 'featured';
                break;
            case 2:
                $order = 'oldest';
                break;
            case 3:
                $order = 'most_viewed';
                break;
            case 4:
                $order = 'top_rated';
                break;
            case 5:
                $order = 'coming_soon';
                break;
            case 6:
                $order = 'watched_now';
                break;
            case 7:
                $order = 'title';
                break;
            default:
                $order = 'latest_updates';
                break;
        }

        $this->sortOverride = $order;
        return $this;
    }
    
    
    public function getContentDetail($num, $page = NULL) {
        if ($this->type == self::$TYPE_PHOTOS_DETAIL) {

            $content = new Hustler_Contentbasic_Photodetail($this->params['photoset_id'], SITECODE_CONTENT, $this->tour);
             
            $content_return = $content->get($page);
             
            $this->content_raw = $content_return;
            $this->content_raw['info'] = "";
            $this->info = "";
            
            $processed = $this->process();
            
            $this->content = $processed;
            
            
            return $this;
            
        }
    
    }
    
    public function load($num, $page = NULL) {
        if ($this->type == self::$TYPE_VIDEOS) {
            return $this->getContent($num, $page);
        } elseif ($this->type == self::$TYPE_MODELS) {
            return $this->getContent($num, $page);
        } elseif ($this->type == self::$TYPE_DVDS) {
            return $this->getContent($num, $page);
        } elseif ($this->type == self::$TYPE_PHOTOS) {
            $this->params['type'] = 'photo';
            return $this->getContent($num, $page);
        } elseif ($this->type == self::$TYPE_PHOTOS_DETAIL) {
            $this->params['type'] = 'photodetail';
            return $this->getContentDetail($num, $page);
        } elseif ($this->type == self::$TYPE_MAGAZINES) {
            return $this->getContent($num, $page);
        } elseif ($this->type == self::$TYPE_CAT) {
            return $this->getContent($num, $page);
        } elseif ($this->type == self::$TYPE_SITES) {
            return $this->getContent(null, null);
        } elseif ($this->type == self::$TYPE_MODEL_DETAIL || $this->type == self::$TYPE_DVD_DETAIL
                || $this->type == self::$TYPE_SCENE_DETAIL || $this->type == self::$TYPE_MAGAZINE_DETAIL) {
            // detail calls don't really get content the same way
            $this->content = array('data' => NULL);
            $this->content_raw = null;
            return $this;
        }
    }

    public function process($hd = FALSE, $nonmobile = TRUE) {
        if ($this->type == self::$TYPE_VIDEOS) {
                $sitetype = $this->getSiteType();
                // get member ID from params
                $member_id = $this->getMemberId();
                // TODO: missing $hd in param for parse_content... does HD watermark
                $data = parse_content($this->content_raw, $member_id, $sitetype, DOCROOT, CACHEPATH, CACHEURL, $nonmobile, $this->imgSizes['img_x'], $this->imgSizes['img_y'], $hd);


        //echo "\n\n content:\n";print_r($this->content_raw); echo "\n\n parsed content:\n" . print_r($data, TRUE);exit;
                // make some titles
                foreach ($data as $k=>$d) {
                    $s = $this->content_raw['data'][$k]['scene_name'];
                    if (strlen($s) > 31) {
                        $s = substr($s, 0, 31) . '...'; // size limit
                    }
                    $data[$k]['scene_name'] = $s;
                    $data[$k]['image'] = $d['ss'];
                    unset($d[$k]['ss']);
                    $data[$k]['scene_released'] = date('n/j/y', strtotime($d['date']));
                    $data[$k]['title_name'] = $this->content_raw['data'][$k]['title_name'];
                    $data[$k]['scene_name_full'] = $data[$k]['scene_name'] . ' in ' . $this->content_raw['data'][$k]['title_name'];
                    $data[$k]['scene_description'] = $this->content_raw['data'][$k]['scene_description'];
                    unset($data[$k]['date']);
                    // are there tour images?
                    if (!empty($this->content_raw['data'][$k]['tour_images'])) {
                        $data[$k]['tour_images'] = $this->content_raw['data'][$k]['tour_images'];
                    }
                    $data[$k]['media_location'] = $this->content_raw['data'][$k]['media_location'];
                    // performers
                    $p = array();
                    $p_data = array();
                    if (isset($this->content_raw['data'][$k]['performers']) && !empty($this->content_raw['data'][$k]['performers'])) {
                        foreach ($this->content_raw['data'][$k]['performers'] as $performer) {
                            if (strtolower($performer['performer_gender']) == 'female') {
                                $p[] = $performer['performer_name'];
                                $p_data[] = array(
                                    'id' => $performer['performer_id'],
                                    'name_full' => $performer['performer_name'],
                                    'name_seo' => seo_name($performer['performer_name']),
                                );
                            }
                                
                        }
                    }
                    $data[$k]['phone']['performers'] = $d['performers'];
                    $data[$k]['performers'] = $p;
                    $data[$k]['performers_data'] = $p_data;
                }

                // we shall get more images for only the latest video
        //print_r($this->content_raw['data']);exit;
                if (!empty($this->content_raw['data'][0]['preview_images'])) {
        //TODO: GET THE DARN MORE IMAGES WHEN SELECTING JUST ONE SCENE!!!
                    //$num = 5;
                    //$data[0]['more_images'] = $this->getMoreImages($this->content_raw['data'][0]['preview_images'], $num);
                    $data[0]['more_images'] = $this->getMoreImagesNonDB($this->content_raw['data'][0]['media_location']);
                }
        //print_r($data);exit;
                return array('data' => $data); 
        } elseif ($this->type == self::$TYPE_MODELS) {
                // default image sizes
                $img_x = $this->imgSizes['img_x'];
                $img_y = $this->imgSizes['img_y'];

                $data = array();
                foreach ($this->content_raw['data'] as $k=>$c) {
                    $data[$k]['model_id'] = $c['performer_id'];
                    $data[$k]['name'] = $c['performer_name'];
                    $image = BASEROOT . "content/performers/" . $c["performer_id"] . ".jpg";
                    if (!file_exists($image)) {
                        $image = DOCROOT . "/images/nophoto.jpg";
                    }
                    $data[$k]['image'] = $this->makeImage($image, $img_x, $img_y);
                    $data[$k]['timestamp'] = date("m/d/y", strtotime($c["performer_timestamp"]));
                }

                return array('data'=>$data);
        } elseif ($this->type == self::$TYPE_DVDS) {
                // default image sizes
                $img_x = $this->imgSizes['img_x'];
                $img_y = $this->imgSizes['img_y'];

                $data = array();
                foreach ($this->content_raw['data'] as $k=>$c) {
                    $img_front = BASEROOT . "/content/groups/" . $c['group_id'] . "_front.jpg";
                    //$img_back = BASEROOT . "/content/groups/" . $c['group_id'] . "_back.jpg";
                    $data[$k]['id'] = $c['title_id'];
                    $data[$k]['name'] = $c['title_name'];
                    $data[$k]['grp'] = $c['group_id'];
                    $data[$k]['img_front'] = $this->makeImage($img_front, $img_x, $img_y);
                    $data[$k]['published'] = date("m/d/y", strtotime($c["title_published"]));
                    // performers
                    $p_data = array();
                    if (isset($c['performers']) && !empty($c['performers'])) {
                        foreach ($c['performers'] as $performer) {
                            if (strtolower($performer['performer_gender']) == 'female') {
                                $p_data[] = array(
                                    'id' => $performer['performer_id'],
                                    'name_full' => $performer['performer_name'],
                                    'name_seo' => seo_name($performer['performer_name']),
                                );
                            }
                                
                        }
                    }
                    $data[$k]['performers_data'] = $p_data;
                }

                return array('data'=>$data);
                
        } elseif ($this->type == self::$TYPE_PHOTOS) {
                // maximum name size
                $max_name_size = 19;

                $data = array();
                foreach ($this->content_raw['data'] as $k=>$c) {
                    $data[$k]['scene_id'] = $c['scene_id'];
                    $data[$k]['scene_released'] = date('n/j/y', strtotime($c['release_timestamp']));
                    $data[$k]['scene_description'] = $this->content_raw['data'][$k]['scene_description'];
                    
                    $data[$k]['name'] = $c['scene_name'];
                    if (empty($data[$k]['name'])) {
                        $data[$k]['name'] = "...";
                    }
                    if (strlen($data[$k]['name']) > $max_name_size) {
                        $data[$k]['name'] = substr($data[$k]['name'], 0, $max_name_size) . '...';
                    }
                    $image = $c['media_location'];
                    if (TRUE === $this->imageMax) {
                        $data[$k]['image'] = $this->makeImage($image, $this->imgSizes['img_x_large'], $this->imgSizes['img_y_large']);
                    } else {
                        $data[$k]['image'] = $this->makeImage($image, $this->imgSizes['img_x'], $this->imgSizes['img_y']);
                    }
                }

                return array('data' => $data); 
         } elseif ($this->type == self::$TYPE_PHOTOS_DETAIL) {

                $data = array();
                
                foreach ($this->content_raw['data'] as $k=>$c) {
                    $image = $c['media_location'];
                    $data[$k]['image'] = Content::image_handler(CACHEURL, CACHEPATH, $image, $this->imgSizes['img_x'], $this->imgSizes['img_y'], true, false, false, true, false, $hd);
                    // prevent downloading of large gallery images using api through back end
                    if ( "members" == $this->getSiteType() ) {
                        $data[$k]['image_large'] = Content::image_handler(CACHEURL, CACHEPATH, $image, $this->imgSizes['img_x_large'], $this->imgSizes['img_y_large'], true, false, false, true, false, $hd);
                    } else {
                        $data[$k]['image_large'] = "";
                    }

                }
                return array('data' => $data); 
        } elseif ($this->type == self::$TYPE_MAGAZINES) {
                $data = array();
                foreach ($this->content_raw['data'] as $k=>$c) {
                    $image = "/web/sites/hustler/hustler-cms/digitalmag/" . (int) $c["magazine_id"] . "/Page_1.jpg";
                    if(!file_exists($image)) {
                            $image = "/web/sites/hustler/hustler-cms/digitalmag/" . $c["content_path"] . "/Page_1.jpg";
                    }
                    if(!file_exists($image)) {
                            $image = "/web/sites/hustler/cms.lfpcontent.com/cms/content/magazines/" . (int)$c["magazine_id"] . ".jpg";
                    }
                    if(!file_exists($image)) {
                        $image = DOCROOT . "/images/placeholder/magazine-placeholder.png";
                    }
                    $data[$k]['img_front'] = Content::image_handler(CACHEURL, CACHEPATH, $image, $this->imgSizes['img_x'], $this->imgSizes['img_y'], true, false, false, true, false, $hd);
                    //usleep(IMAGECACHE_UDELAY); // breathe time for imagecache in microseconds 
                    $data[$k]['id'] = $c['magazine_id'];
                    $data[$k]['magazine_type'] = $c['magazine_type'];
                    $data[$k]['average_score'] = $c['magazine_score'];
                    $tmp = explode('/', $c['content_path']);
                    $data[$k]['content'] = DOCROOT . 'digitalmag/' . $tmp[0] . '/index.html';
                    $data[$k]['date'] = $c['magazine_release_date'];
                    $data[$k]['name'] = $c['magazine_issue'];
                    $data[$k]['in_favorites'] = $c['in_favorites'];
                    
                }
                $this->content_raw['info']['currentPage'] = $this->content_raw['info']['current_page']; // why the discrepency?
                return array('data' => $data); 
        }

    }

    public function getAll() {
        if ($this->type == self::$TYPE_VIDEOS) {
                $tmp = $this->content['data'];
                $obj = array();
                if (!empty($tmp)) {
                    foreach($tmp as $t) {
                        $obj[] = new Hustler_Item_Scene($t, $this->getSiteType());
                    }
                }
                return $obj;
        } elseif ($this->type == self::$TYPE_MODELS) {
                $arr = array();
                foreach ($this->content['data'] as $c) {
                    $arr[] = new Hustler_Item_Model($c);
                }
                return $arr;
        } elseif ($this->type == self::$TYPE_PHOTOS) {
                $arr = array();
                if (! empty($this->content['data'])) {
                    foreach ($this->content['data'] as $c) {
                        $arr[] = new Hustler_Item_Pic($c);
                    }
                }
                return $arr;
        }
    }
    
    public function getImgSizes($siteFormat, $type) {
        
        $imgSizes = array(
            'tour2012' => array(
                'videos' => array(
                    //.5625  height to width ratio
                    'img_x' => 299, // width for thumbnails
                    'img_y' => 168, // height for thumbnails
                    'img_x_small' => 198, // width for thumbnails
                    'img_y_small' => 111, // height for thumbnails
                    'img_x_large' => 542, // width for large thumbnails (scene clip preview)
                    'img_y_large' => 305, // height for large thumbnails (scene clip preview)
                    'img_x_max' => 960, // width for large thumbnails (scene clip preview)
                    'img_y_max' => 540, // height for large thumbnails (scene clip preview)
                ),
                'photos' => array(
                    'img_x' => 152, // width for thumbnails
                    'img_y' => 192, // height for thumbnails
                    'img_x_large' => 311, // width for thumbnails
                    'img_y_large' => 406, // height for thumbnails
                ),
                'models' => array(
                    'img_x' => 299, // width for thumbnails
                    'img_y' => 394, // height for thumbnails
                ),
            ),
            'pad' => array(
                'videos' => array(
                    //.5625  height to width ratio
                    'img_x' => 242, // width for thumbnails
                    'img_y' => 180, // height for thumbnails
                    'img_x_small' => 198, // width for thumbnails
                    'img_y_small' => 111, // height for thumbnails
                    'img_x_large' => 542, // width for large thumbnails (scene clip preview)
                    'img_y_large' => 305, // height for large thumbnails (scene clip preview)
                    'img_x_max' => 960, // width for large thumbnails (scene clip preview)
                    'img_y_max' => 540, // height for large thumbnails (scene clip preview)
                ),
                'photos' => array(
                    'img_x' => 214, // width for thumbnails
                    'img_y' => 267, // height for thumbnails
                    'img_x_large' => 311, // width for thumbnails
                    'img_y_large' => 406, // height for thumbnails
                ),
                'photodetail' => array(
                    'img_x' => 482, // width for thumbnails
                    'img_y' => 321, // height for thumbnails
                    'img_x_large' => 1500, // width for thumbnails
                    'img_y_large' => 1000, // height for thumbnails
                ),
                'models' => array(
                    'img_x' => 214, // width for thumbnails
                    'img_y' => 267, // height for thumbnails
                ),
                'dvds' => array(
                    //'img_x' => 214, // width for thumbnails
                    //'img_y' => 308, // height for thumbnails
                    'img_x' => 160, // width for thumbnails
                    'img_y' => 228, // height for thumbnails
                ),
                self::$TYPE_MAGAZINES => array(
                    'img_x' => 214, // width for thumbnails
                    'img_y' => 288, // height for thumbnails
                ),
            ),
            'phone' => array(
                'videos' => array(
                    //.5625  height to width ratio
                    'img_x' => 425, // width for thumbnails
                    'img_y' => 340, // height for thumbnails
                    'img_x_small' => 198, // width for thumbnails
                    'img_y_small' => 111, // height for thumbnails
                    'img_x_large' => 542, // width for large thumbnails (scene clip preview)
                    'img_y_large' => 305, // height for large thumbnails (scene clip preview)
                    'img_x_max' => 960, // width for large thumbnails (scene clip preview)
                    'img_y_max' => 540, // height for large thumbnails (scene clip preview)
                ),
                'photos' => array(
                    'img_x' => 152, // width for thumbnails
                    'img_y' => 192, // height for thumbnails
                    'img_x_large' => 311, // width for thumbnails
                    'img_y_large' => 406, // height for thumbnails
                ),
                'models' => array(
                    'img_x' => 299, // width for thumbnails
                    'img_y' => 394, // height for thumbnails
                ),
            ),

        );
        // some duplicates
        $imgSizes['pad'][static::$TYPE_SCENE_DETAIL] = $imgSizes['pad']['videos'];
        $imgSizes['pad'][static::$TYPE_MODEL_DETAIL] = $imgSizes['pad']['models'];
        $imgSizes['pad'][static::$TYPE_DVD_DETAIL] = $imgSizes['pad']['videos'];
        $imgSizes['pad'][static::$TYPE_MAGAZINE_DETAIL] = $imgSizes['pad'][self::$TYPE_MAGAZINES];
        $imgSizes['pad'][static::$TYPE_SITES] = $imgSizes['pad']['videos'];

        return $imgSizes[$siteFormat][$type];
    }
    
    public function checkType($type) {
        if ($type != self::$TYPE_VIDEOS && 
            $type != self::$TYPE_MODELS &&
            $type != self::$TYPE_MODEL_DETAIL &&
            $type != self::$TYPE_PHOTOS && 
            $type != self::$TYPE_PHOTOS_DETAIL &&
            $type != self::$TYPE_DVDS && 
            $type != self::$TYPE_DVD_DETAIL &&
            $type != self::$TYPE_MAGAZINES &&
            $type != self::$TYPE_MAGAZINE_DETAIL &&
            $type != self::$TYPE_CAT && 
            $type != self::$TYPE_SITES &&
            $type != self::$TYPE_SCENE_DETAIL
        )
            return FALSE;
        else
            return TRUE;
    }
    
    public function getDetailScene() {
        $id = $this->params['scene_id'];

        $this->scene = $id;
        $member_id = $this->getMemberId();

        $scene = Paysite::get_content_scene($id, $member_id);
//        if (FALSE !== $getTourImages) {
//            $scene['tour_images'] = ImageHandler::get_tour_image($getTourImages, $scene['scene_id']);
////echo "\n got tour images: " . print_r($scene['tour_images'], TRUE);
//        }
        $this->content_raw['data'][0] = $scene;

        $hd = $this->checkHd();                
        $sitetype = $this->getSiteType();

//echo "\n\n content_raw:\n";print_r($this->content_raw);
//exit;
        // make some titles
        
        $data = array();
        $s = $scene['scene_name'];
        if (strlen($s) > 31) {
            $s = substr($s, 0, 31) . '...'; // size limit
        }
        $data['scene_name'] = $s;
        $data['name'] = $s;
        //$data['media_location'] = $scene['media_location'];
        //$data['image']; // don't get image?
        $data['id'] = $id;
        $data['scene_released'] = $scene['scene_released'];
        $data['title_name'] = $scene['title_name'];
        $data['title'] = $scene['title_name'] . ': ' . $scene['scene_name'] ;
        $data['scene_name_full'] = $data['scene_name'] . ' in ' . $scene['title_name'];
        $data['scene_description'] = $scene['scene_description'];
        $data['description'] = $scene['scene_description'];
        $data['average_score'] = $scene['average_score'];
        $data['date'] = date("F j Y", strtotime($scene['scene_released']));
        $data['in_favorites'] = $scene['in_favorites'];

        // categories
	if (!empty($scene['categories'])) {
            $data['categories'] = $scene['categories'];
	}

        // performers
        $p = array();
        $p_data = array();
        if (isset($scene['performers']) && !empty($scene['performers'])) {
            foreach ($scene['performers'] as $performer) {
                if (strtolower($performer['performer_gender']) == 'female') {
                    $p[] = $performer['performer_name'];
                    $p_data[] = array(
                        'id' => $performer['performer_id'],
                        'name_full' => $performer['performer_name'],
                        'name_seo' => seo_name($performer['performer_name']),
                    );
                }

            }
        }
        $data['performers'] = $p;
        $data['performers_data'] = $p_data;
        
        //get vids
        if ($scene['scene_type'] == 'video') {
            $scene_extracts = process_content_scene($scene, $sitetype, false, false);

            $clip_video = NULL;
            if ($sitetype == "members") {
    //SITECODE_CONTENT, $member_id, $sitetype, DOCROOT, CACHEPATH, CACHEURL, $nonmobile, $this->imgSizes['img_x'], $this->imgSizes['img_y'], $hd
                $clip_video2 = $scene_extracts['clip_default'];
                $clip_video = $clip_video2; // for now until we have a working video page... let user watch movie directly from this page listing
                if (!$clip_video) {
                    $clip_video = curPageURL();
                }
            }
            if ($sitetype == "members") {
                $clip = $clip_video;
            } else {
                $clip = $scene_extracts['clip_trailer'];
            }

            if ($clip != curPageURL() && $clip !== NULL) {
                $data['dl_clip'] = $clip . make_dl_token($sitetype);
            } else {
                $data['dl_clip'] = null;
            }
        }      
//echo "\n\n content processed:\n";print_r($data);exit;
        return $data;
    }
    
    public function getDetailDvd() {
        // get further info on a DVD
        // no images
        return Paysite::get_group_data($this->params['dvd_id'], $this->getMemberId());
    }
    
    public function getDetailModel() {
        // get further info on a Model
        // no images
        return Paysite::get_performer_data($this->params['model_id'], $this->getMemberId());
    }
    
    public function getDetailMag() {
        // get further info on a Magazine
        // no images
        return Paysite::get_magazine($this->params['mag_id'], $this->getMemberId());
    }

    public function getMemberId() {
        $member_id = FALSE;
        if (isset($this->params['member_id']) && !empty ($this->params['member_id'])) {
            $member_id = $this->params['member_id'];
        }
        return $member_id;
    }
    
    private function checkHd() {
        // $hd is a variable in child classes
        $hd = FALSE;
        if (isset($this->hd) && $this->hd == TRUE) {
            $hd = TRUE;
        }
        return $hd;
    }
    
// BEGIN for videos...    
    public function loadScene($scene_id, $getTourImages = FALSE) {
        // get just one video scene
        $this->scene = $scene_id;
        $member_id = $this->getMemberId();
        $this->setTypeVideo(); // a workaround for now.  //TODO: change.. .maybe loadScene is part of getContent()?
        $scene = Paysite::get_content_scene($scene_id, $member_id);
        if (FALSE !== $getTourImages) {
            $scene['tour_images'] = ImageHandler::get_tour_image($getTourImages, $scene['scene_id']);
//echo "\n got tour images: " . print_r($scene['tour_images'], TRUE);
        }
        $data = array();
        $data['data'][0] = $scene;
        $this->content_raw = $data;
        $hd = $this->checkHd();
        if ($this->mobile) {
            $this->content = $this->process($hd, FALSE);
        } else {
            $this->content = $this->process($hd, TRUE);
        }
        $this->makeMainImage(LATEST_CLIP); // make a very large image for very large panel
//print_r($this->content);
        return $this;
    }
    
    public function makeMainImage($num = LATEST_CLIP) {
        if (!empty($this->content['data'])) {
	    if (!empty($this->content_raw['data'][$num]['tour_images'])) {
		if (is_array($this->content_raw['data'][$num]['tour_images'])) {
                    $image = $this->content_raw['data'][$num]['tour_images'][0];
		} else {
                    $image = $this->content_raw['data'][$num]['tour_images'];
		}
	    } else {
                $image = $this->content_raw['data'][$num]['media_location'];
	    }
            if (TRUE === $this->imageMax) {
                $this->content['data'][$num]['image_max'] = $this->makeImage($image, $this->imgSizes['img_x_max'], $this->imgSizes['img_y_max']);
            } else {
                $this->content['data'][$num]['image_large'] = $this->makeImage($image, $this->imgSizes['img_x_large'], $this->imgSizes['img_y_large']);
            }
        }
    }

    private function getMoreImagesNonDB($preview_image) {
	// three preview images are actually made with last number as an index, such as: 
	//      photoset4_1_1, photoset4_1_2, photoset4_1_3
        // given $preview_image, deduce what the other two are
        // first parse preview image for its root name
        $x = strrpos($preview_image, '.');
        $nameNoExt = substr($preview_image, 0, $x);
        $ext = substr($preview_image, $x);

        $x = strrpos($nameNoExt, '_');
        $nameNoNum = substr($preview_image, 0, $x);
        $num = (int) substr($nameNoExt, $x + 1);
        $nums[1] = true;
        $nums[2] = true;
        $nums[3] = true;
        unset($nums[$num]);
        $return = array();
        foreach ($nums as $k=>$n) {
            TRUE === defined('SS_BASE_DIR') ? $tmp = SS_BASE_DIR . DEV_SS_DEFAULT : $tmp = $pic = $nameNoNum . '_' . (string) $k . $ext; // SS_BASE_DIR for a local machine only TODO: REMOVE
            //$tmp = $pic = $nameNoNum . '_' . (string) $k . $ext;
            $return[] = Content::image_handler(CACHEURL, CACHEPATH, $tmp, $this->imgSizes['img_x_small'], $this->imgSizes['img_y_small']);
            // additional parameters for Content::image_handler below (we make $hd true: 
            //    $resize = false, $watermark = false, $trimborder=false, $nodetect=true, $passthru=false, $hd=false, $classic=false);
            //$return[] = Content::image_handler(CACHEURL, CACHEPATH, $tmp, $this->imgSizes['img_x_small'], $this->imgSizes['img_y_small'], false, false, false, true, false, $this->hd); 
        }
        return $return;
    }

    private function getMoreImages($prevew_images, $num) {
//echo "cacheurl=" . CACHEURL . "  docroot=" . DOCROOT;exit; 
        $return = array();
        for ($x = 0; $x < $num; $x++) {
            if (!empty($prevew_images[$x])) {
                $tmp = $prevew_images[$x];
                $return[] = Content::image_handler(CACHEURL, CACHEPATH, $tmp, $this->imgSizes['img_x_small'], $this->imgSizes['img_y_small']);
                // additional parameters for Content::image_handler below (we make $hd true: 
                //    $resize = false, $watermark = false, $trimborder=false, $nodetect=true, $passthru=false, $hd=false, $classic=false);
                //$return[] = Content::image_handler(CACHEURL, CACHEPATH, $tmp, $this->imgSizes['img_x_small'], $this->imgSizes['img_y_small'], false, false, false, true, false, $this->hd);
            }
        }
        return $return;
    }    
// END for videos
// 

    // not used yet
    function setModel($id) {
        $this->params['model_id'] = $id;
    }

    // not used yet
    function setCategory($id) {
        $this->params['category_id'] = $id;
    }
}
