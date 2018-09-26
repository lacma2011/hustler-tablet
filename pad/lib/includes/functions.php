<?

function process_content_scene($content_scene, $site_type = 'tour', $get_vid_folder = false, $nonmobile = FALSE) {
    // process the results of Paysite::get_content_scene()
    // $nonmobile is special... for some pages that are not for mobile site :/
    $vid_list = Paysite::get_video_listing($content_scene['scene_id'], $content_scene);
//print "\n\nget_video_listing():\n" . print_r($vid_list, TRUE);
    if ($get_vid_folder) {
        $folder = get_video_folder($vid_list);
    }
    $tmp = read_video_listings($vid_list, $site_type, $content_scene['scene_id'], DL_PAGE_TOKEN);
    $all_clips = $tmp['videos'];
//print "\n\nread_video_listings():\n" . print_r($tmp, TRUE);
    $clip_trailer = get_trailerclip($tmp, $nonmobile);
    $clip_default = default_video_extraction($all_clips, $site_type, $tmp['trailerfile'], $tmp['trailerfile2'], scene_released($content_scene), $nonmobile);
    $tmp = array(
        'clip_default' => $clip_default,
        'clip_trailer' => $clip_trailer,
    );
    if (TRUE === $nonmobile) {
        $tmp['video_options'] = $all_clips;
    }
    if ($get_vid_folder) {
        $tmp['folder'] = $folder;
    }
//echo PHP_EOL . 'process_content_scene:' . PHP_EOL . print_r($tmp,TRUE);
    return $tmp;
}

function scene_released($scene) {
    if(strtotime($scene["scene_released"]) > time()) {
        return false;
    } else {
        return true;
    }
}

function read_video_listings($video_listings, $site_type, $scene_id, $no_token = true) {
    // parses the result from get_video_listing()
    $srcvideos = $video_listings;

    // stolen from video.php (non-mobile)....
    if ($no_token) {  // for now we only do no_token for tours... until members has a dl page like vid.php to create token after user clicks for clip.
        $token = '';
    }
    else {
        $token = make_dl_token($site_type);
    }


    // stolen from video.php (non-mobile)...
    $cdn = "http://cdn.assets.lfpcontent.com/secure/";
    $cdndl = "http://cdn.downloads.lfpcontent.com/secure/";
    $cdn2 = "mms://streaming.lfpcontent.com/secure/";
    $videos = array();

//echo '<!-- source videos' . PHP_EOL;
//print_r($srcvideos);
//echo '-->' . PHP_EOL . PHP_EOL;

// a dumb fix.  When we couldn't find m4v's, it could be because they are 1400 quality and not 1500
    if (empty($srcvideos['m4v']) && (!empty($srcvideos['mp4']) && count($srcvideos['mp4'])) > 0) {
        $high = '';
        if (! empty($srcvideos['mp4']['high'])) {
            $high = preg_replace('/1500/', '1400', $srcvideos['mp4']['high']);
            $high = preg_replace('/\.mp4/', '.m4v', $high);
        }
        $med = '';
        if (! empty($srcvideos['mp4']['medium'])) {
            $med = preg_replace('/700/', '700', $srcvideos['mp4']['medium']);
            $med = preg_replace('/\.mp4/', '.m4v', $med);
        }
	$srcvideos['m4v'] = array(
		'high' => $high,
		'medium' => $med,
	);
    }

    $trailerfile = NULL;
    $trailerfile2 = NULL;
    $dl = array();
    foreach($srcvideos as $type=>$srcvideos2) {
	if($type == "m4v") {
		$type = "Mobile";	
	}
	$videos[$type]["high"] = false;
	$videos[$type]["medium"] = false;
	$videos[$type]["low"] = false;		
	$videos[$type]["hd"] = false;			
	$ok = false;

	foreach($srcvideos2 as $stype=>$vid) {
                if(!file_exists($vid) && MOBILE_TEST !== TRUE) continue;

		$filesize[$type][$stype] = number_format(round(filesize($vid)/1048576,0),0) . " MB";
//		$parts = explode("/", $vid);
		list($base, $v) = explode("/videos/", $vid, 2);
		
		
		$fparts = substr($v, 0, strrpos($v, "_"));
		$fparts = str_replace("&", "%26", $fparts);
//echo '<!-- trailer clips' . PHP_EOL;
//print_r($fparts);
//echo 'exist?: ' . $base . "/videos/" . $fparts . "_trl.m4v" . PHP_EOL;
//echo '-->' . PHP_EOL . PHP_EOL;
                if(file_exists($base . "/videos/" . $fparts . "_trl.m4v")) {
                    $trailerfile = $cdn . $fparts . "_trl.m4v" . $token;
                } elseif (file_exists($base . "/videos/" . $fparts . "_trl.mp4")) {
                    $trailerfile = $cdn . $fparts . "_trl.mp4" . $token;
                }
		if(file_exists($base . "/videos/" . $fparts . "_trl.flv") || MOBILE_TEST === TRUE)		
                    $trailerfile2 = $cdn . $fparts . "_trl.flv" . $token;
		
		$v = str_replace("&", "%26", $v);	
		$url = $cdn . $v . $token;
		$videos[$type][$stype] = $url;

		$urldl = $cdndl . $v . $token;
		$dl[$type][$stype] = $urldl;		
		
		if($type == "wmv") {
			$url2 = $cdn2 . $v . $token;
			$videos["wmv-streaming"][$stype] = $url2;
		}
		$ok = true;
	}
	if(!$ok) unset($videos[$type]);

    }
//echo '<!-- source videos' . PHP_EOL;
//print_r($srcvideos);
//echo '-->' . PHP_EOL . PHP_EOL;

//echo '<!-- video clips' . PHP_EOL;
//print_r($videos);
//echo '-->' . PHP_EOL . PHP_EOL;

    return array(
        'videos' => $videos,
        'trailerfile' => $trailerfile,
        'trailerfile2' => $trailerfile2,
        'download' => $dl,
    );
}

function default_video_extraction($videos, $_SITETYPE, $trailerfile, $trailerfile2, $scene_released, $nonmobile = FALSE) {
    // using the output of read_video_listings(), determine the 'default video' clip which may be a scene or trailer
    // $nonmobile is special... for some pages that are not for mobile site :/
    global $_USERDATA;

//    don't need this anymore, we won't show hd at all for any format. But if we do later and need to restrict Mobile only to high...
//    if (!$videos['Mobile']['hd'] && $videos['Mobile']['high']) {
//        // to prioritize Mobile 'High' over all other formats 'HD'...
//        $videos['Mobile']['hd'] = $videos['Mobile']['high'];
//    }

    //print "formats available:";unset($videos['flv']);unset($videos['wmv-streaming']);unset($videos['wmv']);print "<pre>";print_r($videos);print "</pre>";

    //$res = array("hd", "high", "medium", "low");
    //$format = array("Mobile", "mp4", "flv", "mov");
    //restrict to only these formats.  Note that if there's only HD there would be no clip, but that is unlikely...
    $format = array("Mobile", "mp4");
    $res = array("high", "medium", "low");
    //override for non-mobile pages
    if (TRUE === $nonmobile) {
        $format = array("mp4", "flv", "mov");
        $res = array("hd", "high", "medium", "low");
    }

    $defaultvideo = NULL;
    $defaultquality = NULL;

    if (isset($_USERDATA["member"]["preferences"]["preference_connection"])) {
	// this doesn't work yet... to determine video quality based on user preference
	//$user_pref = $_USERDATA["member"]["preferences"]["preference_connection"];
	$user_pref = 4;
    } else {
	$user_pref = 4;
    }

    // let's get Mobile first...
    if (TRUE !== $nonmobile) {
        for ($i=4; $user_pref >= $i, $i>0 ; $i--) {
            $format_primary = array("Mobile");
            foreach ($format_primary as $f) {
                if (isset($res[4 - $i])) {
                    $x = $res[4 - $i]; // go through resolution list in forward order
                    if (!empty($videos[$f][$x]) && empty($defaultvideo)) {
                        $defaultvideo = $videos[$f][$x];
                        $defaultquality = $res[4 - $i];
                        //echo "\n\ngot video $defaultvideo with quality $defaultquality";
                    }
                }
            }
        }
    }

    // if no Mobile found...
    if (empty($defaultvideo)) {
        for ($i=4; $user_pref >= $i, $i>0 ; $i--) {
            foreach ($format as $f) {
                if (isset($res[4 - $i])) {
                    $x = $res[4 - $i]; // go through resolution list in forward order
                    if (!empty($videos[$f][$x]) && empty($defaultvideo)) {
                        $defaultvideo = $videos[$f][$x];
                        $defaultquality = $res[4 - $i];
                        //echo "\n\ngot video $defaultvideo with quality $defaultquality";
                    }
                }
            }
        }
    }

    $showwmvs = FALSE;
    if(isset($videos["wmv-streaming"]["hd"])) $videos["wmv-streaming"]["hd"] = FALSE;
    if(!empty($videos["wmv-streaming"])) {
            foreach($videos["wmv-streaming"] as $item) {
                    if($item) $showwmvs = TRUE;
            }
    }
    if(empty($showwmvs)) $videos["wmv-streaming"] = FALSE;

    // if still no video found...
    if(empty($defaultvideo)) {
            $res = array("hd", "high", "medium", "low");
            $format = array("Mobile", "mp4", "flv", "mov");
            
            $defaultvideo = "";
            $defaultquality = "";
            foreach ($res as $r) {
                foreach ($format as $f) {
//echo "\nchecking format $f at $r";
                    if (!empty($videos[$f][$r]) && empty($defaultvideo)) {
//echo "... FOUND";
                        $defaultvideo = $videos[$f][$r];
                        $defaultquality = $r;
                    }
                }
            }
            // wmv is last resort
            if (empty($defaultvideo)) {
                $format = array("wmv-streaming");
                foreach ($format as $f) {
//echo "\nchecking format $f at $r";
                    if (isset($videos[$f][$r]) && $videos[$f][$r] && !$defaultvideo) {
//echo "... FOUND";
                        $defaultvideo = $videos[$f][$r];
                        $defaultquality = $r;
                    }
                }
            }
    }
    //echo "<pre>";
    //print_r($videos);
    //echo "</pre>";
    //echo $defaultvideo;

    if($_SITETYPE == "tour") {
            // TOUR VIDEO LIMITATION HERE
            $defaultvideo = "";	
    }

    //$_SESSION["member"]["member_type"] = "half";


// members get limits?
// 
//    if($_SESSION["member"]["member_type"] != "full") {
//            $playcount = mysql_result(mysql_query("SELECT count(scene_id) FROM lfpcms_logs_member_view WHERE member_id='" . mysql_escape_string($_SESSION["member"]["member_id"]) . "'"),0,0);
//            if($playcount > 5) {
//                    $defaultvideo = false;
//                    $_SITETYPE = "tour";
//                    $NOSECONDTAB = true;
//            }
//    }

    if(!$scene_released) {
            $defaultvideo = false;
    }

    if(!$defaultvideo) {
            $defaultvideo = $trailerfile;
    }

    if(!$defaultvideo) {
            $defaultvideo = $trailerfile2;
    }

    return $defaultvideo;
}

function get_trailerclip($videos_listing, $nonmobile = FALSE) {
    // using the output of read_video_listings(), determine the trailer video clip
    if ($nonmobile) {
	// non mobiles DO want FLV
	$tmp = $videos_listing['trailerfile'];
	if (substr($tmp, -3) == 'flv') {
	    return $tmp;
    	}
	$tmp = $videos_listing['trailerfile2'];
	if (substr($tmp, -3) == 'flv') {
	    return $tmp;
    	}
    }
    $trailer_clip = $videos_listing['trailerfile'];
    if (!$trailer_clip) {
        $trailer_clip = $videos_listing['trailerfile2'];
    }
    if (substr($trailer_clip, -3) == 'flv') {
	// Got an FLV, don't really want it
	return NULL;
    }
    return $trailer_clip;
}

function file_exists_test() {
    return TRUE;
}

function fix_dvd_title($s) {
    $title = preg_replace('/[#]/', '', seo_name($s));
    $title = preg_replace('/[!]/', '', $title);
    $title = preg_replace('/[\']/', '', $title);
    return $title;
}

function get_mobile_content($site_code, $tour, $perpage, $page, $member_id, $content_options) {
    // after initializing important site info (page, tour, site code) this gets content
    // by checking url query string (listed $content_options)
    $content = new Hustler_Contentbasic_Simple($site_code, $tour);
    $content->setParam('perPage', $perpage); // clips per page: 4 or MOBILE_PERPAGE
    // query options
    foreach ($content_options as $option) {
        if (isset($_GET[$option])) {
            if ($_GET[$option] != null && $_GET[$option] != '') {
                $content->setParam($option, $_GET[$option]);
            }
        }
    }
    $content->setParam('member_id', $member_id);
    // for hustlerHD tour, get featured only clips
    if ($site_code == 'hustler-hd' && $tour == TRUE) {
        $content->setParam('sort', 'featured'); // featured only
    }
    return $content->get($page);
}

function get_list_title($category, $site, $special_site = false) {

    if ($_GET["type"] == "videos" && $_GET["show_only_favorites"]) {
        return 'Favorite Videos';
    } else if (true == $special_site) {
        return 'Showing Site ' . $site['site_name'];
    } else if ($_GET["type"] == "videos") {
        return TITLE_ALL_VIDEOS;
    } else if ($_GET["type"] == "dvds") {
        return TITLE_ALL_DVDS;
    } else if ($_GET["type"] == "photos") {
        return TITLE_ALL_PHOTOS;
    } else if ($_GET["type"] == "models") {
        return TITLE_MODELS;
    } else if ($_GET["type"] == "favorites") {
        return 'Showing Favorites';
    } else if ($_GET["type"] == "favorite_models") {
        return TITLE_FAVORITE_MODELS;
    } else if ($_GET["type"] == "categories") {
        // TITLE_SHOWING_CATEGORY
        return 'Showing Category ' . $category["category_name"];
    } else if ($_GET["type"] == "search" || $_GET["type"] == "advanced-search") {
        return TITLE_SEARCH_RESULTS . '<b>' . str_replace("-", " ", $_GET["string"]) . '</b>';
    } else if ($_GET["type"] == "favorites") {
        return TITLE_MY_FAVORITES;
    } else if ($_GET["type"] == "magazines") {
        return TITLE_ALL_MAGAZINES;
    } else if ($_GET["type"] == "favorite_magazines") {
        return TITLE_FAVORITE_MAGAZINES;
    } else if ($_GET["type"] == "categories_listing") {
        return TITLE_CATEGORIES;
    } else if ($_GET["type"] == "favorite_categories") {
        return TITLE_FAVORITE_CATEGORIES;
    } else if ($_GET["type"] == "coming_soon") {
        return TITLE_UPCOMING_SCENES;
    } else if ($_GET["type"] == "model") {
        $data = Paysite::get_performer_data($_GET["model_id"]);
        return 'Showing Pornstar ' . $data['performer_name'];
    } else if ($_GET["type"] == "videos_in_dvd") {
        return 'Showing DVD ' . $data['group_name'];
    }
    return '';
}

function get_site_colors($sitecode) {
    switch($sitecode) {
        case 'anal-hookers':
            $color1 = '#42090b';
            $color2 = '#a61f23';
            break;
        case 'asian-fever':
            $color1 = '#42090b';
            $color2 = '#a61f23';
            break;
        case 'barely-legal':
            $color1 = '#ef5e91';
            $color2 = '#f890b4';
            break;
        case 'beaver-hunt':
            $color1 = '#205088';
            $color2 = '#242525';
            break;
        case 'bossy-milfs':
            $color1 = '#56adec';
            $color2 = '#1e6ba4';
            break;
        case 'busty-beauties':
            $color1 = '#dd7fb0';
            $color2 = '#b8347a';
            break;
        case 'daddy-gets-lucky':
            $color1 = '#ac5eae';
            $color2 = '#7e2781';
            break;
       case 'his-classics':
            $color1 = '#ffcc00';
            $color2 = '#4d6077';
            break;
        case 'hometown-girls':
            $color1 = '#25647f';
            $color2 = '#1e8dc2';
            break;
        case 'hottie-moms':
            $color1 = '#63adb4';
            $color2 = '#375d5f';
            break;
        case 'hustlaz':
            $color1 = '#bfb1aa';
            $color2 = '#8d817b';
            break;
        case 'hustler':
            $color1 = '#333333';
            $color2 = '#0394D0';
            break;
        case 'hustlers-college-girls':
            $color1 = '#5b7d2e';
            $color2 = '#3f5d18';
            break;
        case 'hustler-hd':
            $color1 = '#0c0e0b';
            $color2 = '#ffdd22';
            break;
        case 'hustler-girls':
            $color1 = '#0c0e0b';
            $color2 = '#221b1f';
            break;
        case 'hustlers-lesbians':
            $color1 = '#c92154';
            $color2 = '#f05584';
            break;
        case 'hustlers-taboo':
            $color1 = '#1b0606';
            $color2 = '#5e1417)';
            break;
        case 'muchas-latinas':
            $color1 = '#e7639a';
            $color2 = '#8c2752';
            break;
        case 'scary-big-dicks':
            $color1 = '#5096c8';
            $color2 = '#165b8b';
            break;
        case 'too-many-trannies':
            $color1 = '#854eaf';
            $color2 = '#5c3776';
            break;
        case 'vcaxxx':
            $color1 = '#d75606';
            $color2 = '#ec761b';
            break;
        default:
            $color1 = '#d75606';
            $color2 = '#ec761b';
            break;
    }
    return array(
        'color1' => $color1,
        'color2' => $color2,
    );
}

function get_description($mobile, $full) {
    if (empty($mobile)) {
        return $full;
    } else {
        return $mobile;
    }
}

function getUrlSansPage() {
    // when in a page with sub-pages, remove URL.
    $url = curPageURL();
    $url = preg_replace('/\?.*$/', '', $url); // remove query string
    if (substr($url,-7) == '119/69/') {
        // special URL case: damn page for category called "69";       
        return $url;
    }
    $url = preg_replace('#/[0-9]*/$#', '', $url);
    if (substr($url,-1) != '/') $url .= '/';
    return $url;
}

function getSiteType() {
    $tmp = substr(curPageURL(), strlen('http://'));
    $tmp = explode('/', $tmp);
    $tmp = explode('.', $tmp[0]);
    foreach ($tmp as $t) {
        if ($t == 'members') {
            return $t;
        }
    }
    return 'tour';
}

function curPageURL() {
    $pageURL = 'http';
    if (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
     $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
     $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    $pageURL = preg_replace('/[a-z]*.php$/', '', $pageURL);
    // sometimes there's a trailing ? for postbacks that have no real content
    if (substr($pageURL,-1,1) == '?') {
        $pageURL = rtrim($pageURL,'?');
    }
    return $pageURL;
}

function seo_name($string) {
	$string = Paysite::truncate_at_word($string, 50, false);
	$string = str_replace(" ", "-", strtolower(trim($string)));
	$string = str_replace("--" , "-" , $string);
	$string = str_replace("--" , "-" , $string);
	$string = str_replace("+" , "plus" , $string);
	$string = str_replace('&' , "and" , $string);		
	return $string;	
}
   
function get_members_url($root = '') {
    //global $_SERVERNAME, $sites, $_SITECODE;
    $server = $_SERVER['SERVER_NAME'];
    if (preg_match('/^tour\./', $server)) {
        // this is beta.*
        $ret = preg_replace('^tour','members', $server);
    } elseif (preg_match('/\.tour\./', $server)) {
        // this is *.beta.*
        $ret = preg_replace('/\.tour\./','.members.', $server);
    } else {
        $ret = 'hustler.com';
    }
    if ($root == '') {
        return 'http://' . $ret;
    }
    return 'http://' . $ret . '/' . $root . '/';

    
}

function make_url_performer($id, $name) {
    return MOBILE_URL . 'pornstar/' . $id . '/' . seo_name($name);
}

function make_dl_token($site_type = 'tour') {
    $token = '';
    if($site_type == "members") {
        $ec_expire = (time()+2700);
        exec("/usr/local/bin/ec_encrypt m4CD8cg25Ev93jK ec_expire=$ec_expire",$token_arr);
        if (isset($token_arr[0])) $token = '?' . $token_arr[0];
    } else {
        $ec_expire = (time()+180);
        exec("/usr/local/bin/ec_encrypt m4CD8cg25Ev93jK ec_expire=$ec_expire",$token_arr);
        if (isset($token_arr[0])) $token = '?' . $token_arr[0];
    }
    return $token;
}

function get_video_folder($v) {
    foreach ($v as $f) {
        foreach ($f as $x) {
            $t = split('clip', $x);
            return $t[0];
        }
    }
}

function parse_content($content, $member_id, $_SITETYPE, $docroot, $cachepath, $cacheurl, $nonmobile = FALSE, $img_x = 425, $img_y = 340, $hd = FALSE) {
    // parses content in $content, and also complements it with more scene content from the database
    // $nonmobile is special... for some pages that are not for mobile site    
    $allData = array();
    foreach ($content['data'] as $count => $c) {
        $data = array();
	$data['count'] = $count;
//TODO: for ajax calls, $count will have to be modified... let ajax consumer handle it
        $data['title'] = $c['title_name'] . ': ' . $c['scene_name'] ; // . '(' . $c['scene_id'] . ')';
        $data['date'] = $c['scene_released'];

        $scene = Paysite::get_content_scene($c['scene_id'], $member_id);

        // favorites
//TODO: make favorites link an ajax call
        $data['scene_id'] = $c["scene_id"];
        $data['fav_status'] = $scene['in_favorites'];

        $scene_extracts = process_content_scene($scene, $_SITETYPE, false, $nonmobile);
        
        if (TRUE === $nonmobile) {
            $data['video_options'] = $scene_extracts['video_options'];
     	}
//print "\n\nscene:\n" . print_r($scene, TRUE) . "\n\nscene_extracts:\n" . print_r($scene_extracts, TRUE);
        
        // just categories
	if (!empty($scene['categories'])) {
		$data['categories'] = $scene['categories'];
        	// categories list formatted with html tags
        	$str_cat = "";
        	foreach ($scene['categories'] as $k => $cat) {
            		$str_cat = $str_cat . '<a href="' . MOBILE_URL . 'category/videos/' . $k . '/' . seo_name($cat) . '/">' . $cat . '</a>, ';
        	}
        	$data['str_cat'] = substr($str_cat, 0, strlen($str_cat) - 2); //remove trailing comma & space
	}
        
        // video clip
        $clip_video = NULL;
        if ($_SITETYPE == "members") {
            $clip_video = $docroot . '/video/' . $c["scene_id"] . '/' . seo_name($c["scene_name"]) . '/' . $c["scene_lfpid"] . '/';
            $clip_video2 = $scene_extracts['clip_default'];
            $clip_video = $clip_video2; // for now until we have a working video page... let user watch movie directly from this page listing
            if (!$clip_video) {
                $clip_video = curPageURL();
            }
        }

        // screenshot
        $img = $c["media_location"];
        if (! empty($c["tour_images"])) {
            if (! is_array($c["tour_images"])) {
                $img = $c["tour_images"];
            } else {
                $img = $c["tour_images"][0]; // it could possibly be an array, if there's more than one?
            }
        }
        if (!$hd) {
            $data['ss'] = Content::image_handler($cacheurl, $cachepath, $img, $img_x, $img_y);
        } else {
            // additional parameters for Content::image_handler below (we make $hd true: 
            //    $resize = false, $watermark = false, $trimborder=false, $nodetect=true, $passthru=false, $hd=false, $classic=false);
            $data['ss'] = Content::image_handler($cacheurl, $cachepath, $img, $img_x, $img_y,false, false, false, true, false, TRUE);
        }
        
        
        
// TODO: put a video player page link for non-trailers too
        // trailer clip
        if ($scene_extracts['clip_trailer']) {
            $clip_trailer = MOBILE_URL . 'vid/' . $c["scene_id"] . '/';
        } else {
            $clip_trailer = '#clip_' . $count;
        }
	$data['clip_trailer_download'] = $scene_extracts['clip_trailer'];

        if ($_SITETYPE == "members") {
            $clip = $clip_video;
        } else {
            $clip = $clip_trailer;
        }
        $data['clip'] = $clip;
        $data['clip_trailer'] = $clip_trailer;
        $data['clip_video'] = $clip_video;

        //performers
        $performers = "";
        if (!empty($c['performers'])) {
            foreach ($c['performers'] as $k=>$cat) {
                        $performers = $performers . '<a href="' . make_url_performer($cat['performer_id'], $cat['performer_name']) . '/">' . $cat['performer_name'] . '</a>, ';
            }
        }
            
        if ($performers != '') {
            $performers = substr($performers,0,strlen($performers) - 2); //remove trailing comma & space)
        } else {
            $performers = $c['scene_name'];
            if ($c['scene_performers']) {
                $performers = $c['scene_performers'];
            }
        }
        $data['performers'] = $performers;

        // misc info
        $description = get_description($c['mobile_description'], $c['scene_description']);
        $data['description'] = $description;
        $allData[] = $data;
    }
    return $allData;
}

function lnk($path = '') {
    // append query string to path/url
    defined('FIXED_QUERY_STRING') ? $s = FIXED_QUERY_STRING : $s = '';
    return $path . $s;
}

function make_query_string($exceptions) {
    $tmp = array();
    $get = $_GET;
    $filter_qs = explode(',', $exceptions);
    foreach ($filter_qs as $q) {
        $q = preg_replace('/\s/','',$q);
        if (isset($get[$q])) unset($get[$q]);
    }
    foreach($get as $k=>$q) {
        $tmp[] = "$k=$q";        
    }
    $x = '?' . implode('&', $tmp);
    if ($x == '?') {
        return '';
    }
    return $x;
}

function check_join_redirect() {
    if (count($_SESSION['counting']) >= TOUR_MAX_PAGES) {
        if (!$_SESSION['counting'][$_SERVER["REQUEST_URI"]]) {
            ob_clean();
            //error_reporting(E_ALL);
            header('Location: ' . MOBILE_URL_JOIN);
            exit;
        }
    } elseif (!$_SESSION['counting'][$_SERVER["REQUEST_URI"]]) {
        $_SESSION['counting'][$_SERVER["REQUEST_URI"]] = true;
    }
}

function make_mobile_url($mobile_url_root) {
    $tmp = 'http://' . $_SERVER['SERVER_NAME'] . '/' . $mobile_url_root;
    if ($mobile_url_root != '') { //trailing slash necessary
            $tmp .= '/';
    }
    return $tmp;
}

function get_join_link($sitecode) {
	 $sitejoins = array (
	  'anal-hookers' => 
	  array (
	    'key' => 'MC4wLjIuMzcuMC4wLjAuMC4w',
	    'url' => 'secure.analhookers.com',
	  ),
	  'asian-fever' => 
	  array (
	    'key' => 'MC4wLjMuMzguMC4wLjAuMC4w',
	    'url' => 'secure.asianfever.com',
	  ),
	  'barely-legal' => 
	  array (
	    'key' => 'MC4wLjQuMzkuMC4wLjAuMC4w',
	    'url' => 'secure.barelylegal.com',
	  ),
	  'beaver-hunt' => 
	  array (
	    'key' => 'MC4wLjUuMTQzLjAuMC4wLjAuMA',
	    'url' => 'secure.beaverhunt.com',
	  ),
	  'bossy-milfs' => 
	  array (
	    'key' => 'MC4wLjUyLjQwLjAuMC4wLjAuMA',
	    'url' => 'secure.bossymilfs.com',
	  ),
	  'busty-beauties' => 
	  array (
	    'key' => 'MC4wLjYuNDEuMC4wLjAuMC4w',
	    'url' => 'secure.bustybeauties.com',
	  ),
	  'daddy-gets-lucky' => 
	  array (
	    'key' => 'MC4wLjUwLjQyLjAuMC4wLjAuMA',
	    'url' => 'secure.daddygetslucky.com',
	  ),
	  'his-videos' => 
	  array (
	    'key' => '',
	    'url' => '',
	  ),
	  'hometown-girls' => 
	  array (
	    'key' => 'MC4wLjcuNDMuMC4wLjAuMC4w',
	    'url' => 'secure.hometowngirls.com',
	  ),
	  'hottie-moms' => 
	  array (
	    'key' => 'MC4wLjguNDQuMC4wLjAuMC4w',
	    'url' => 'secure.hottiemoms.com',
	  ),
	  'hustlaz' => 
	  array (
	    'key' => 'MC4wLjQyLjQ1LjAuMC4wLjAuMA',
	    'url' => 'secure.hustlaz.com',
	  ),
	  'hustler' => 
	  array (
	    'key' => 'MC4wLjkuNDYuMC4wLjAuMC4w',
	    'url' => 'secure.hustler.com',
	  ),
	  'hustler-hd' => 
	  array (
	    'key' => 'MC4wLjQzLjE0Mi4wLjAuMC4wLjA',
	    'url' => 'secure.hustlerhd.com',
	  ),
	  'hustler-parodies' =>  //hustler for now
	  array (
	    'key' => 'MC4wLjkuNDYuMC4wLjAuMC4w',
	    'url' => 'secure.hustler.com',
	  ),
	  'hustlers-college-girls' => //hustler for now
	  array (
	    'key' => 'MC4wLjQ0LjQ5LjAuMC4wLjAuMA',
	    'url' => 'secure.hustlerscollegegirls.com',
	  ),
	  'hustlers-lesbians' => 
	  array (
	    'key' => 'MC4wLjEzLjQ3LjAuMC4wLjAuMA',
	    'url' => 'secure.hustlerslesbians.com',
	  ),
	  'hustlers-taboo' => 
	  array (
	    'key' => 'MC4wLjE0LjQ4LjAuMC4wLjAuMA',
	    'url' => 'secure.hustlerstaboo.com',
	  ),
	  'muchas-latinas' => 
	  array (
	    'key' => 'MC4wLjQwLjUzLjAuMC4wLjAuMA',
	    'url' => 'secure.muchaslatinas.com',
	  ),
	  'scary-big-dicks' => 
	  array (
	    'key' => 'MC4wLjQ2LjUyLjAuMC4wLjAuMA',
	    'url' => 'secure.scarybigdicks.com',
	  ),
	  'too-many-trannies' => 
	  array (
	    'key' => 'MC4wLjQ5LjUxLjAuMC4wLjAuMA',
	    'url' => 'secure.toomanytrannies.com',
	  ),
	  'vcaxxx' => 
	  array (
	    'key' => 'MC4wLjE4LjUwLjAuMC4wLjAuMA',
	    'url' => 'secure.vcaxxx.com',
	  ),
	 );

	if (isset($_REQUEST) && isset($_REQUEST['nats']))
		$k = $_REQUEST['nats'];
	else
		$k = $sitejoins[$sitecode]['key'];
        if (isset($sitejoins[$sitecode]))
		$u = $sitejoins[$sitecode]['url'];
	else
		$u = $sitejoins['hustler']['url'];
	return 'http://' . $u . '/signup/signup.php?nats=' . $k . '&step=2';
}

function get_desktop_url($sitetype, $site) {
    $tmp = $_SERVER['SERVER_NAME'];
    if (strstr($tmp, 'beta.')) {
        if ($sitetype == 'tour') {
            $tmp = $site['site_tour_docroot_staging'];
        } else {
            $tmp = $site['site_members_docroot_staging'];
        }
    } else {
        if ($sitetype == 'tour') {
            $tmp = $site['site_tour_docroot'];
        } else {
            $tmp = $site['site_members_docroot'];
        }
    }
    if (substr($tmp, 0, 7) != 'http://') {
	return 'http://' . $tmp;
    }
    return $tmp;
}

function is_home() {
	if (
		MOBILE_SCRIPT == 'index.php' &&
		empty($_GET['page']) &&
		empty($_GET['site']) &&
		empty($_GET['model_id']) &&
		empty($_GET['category_id']) &&
		empty($_GET['site'])
	) {
		return TRUE;
	}
	return FALSE;
}

function custom_error_handler($number, $string, $file, $line, $context) {
    global $errors_skip;
    if (! isset($errors_skip)) $errors_skip = array();
    // Determine if this error is one of the enabled ones in php config (php.ini, .htaccess, etc)
    $error_is_enabled = (bool)($number & ini_get('error_reporting') );
   
    // -- FATAL ERROR
    // throw an Error Exception, to be handled by whatever Exception handling logic is available in this context
    if( in_array($number, array(E_USER_ERROR, E_RECOVERABLE_ERROR)) && $error_is_enabled ) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
   
    // -- NON-FATAL ERROR/WARNING/NOTICE
    // Log the error if it's enabled, otherwise just ignore it
    else if( $error_is_enabled ) {
        $ok = TRUE;
        foreach ($errors_skip as $f)
            if ($f == $file)
                $ok = FALSE;
        if (TRUE === $ok) {
            error_log( $string, 0 );
            return false; // Make sure this ends up in $php_errormsg, if appropriate
        }
    }
}

function ech($str) {
    if (is_array($str)) {
        print print_r($str, TRUE) . "<br/>" . PHP_EOL;
    }
    else {
        echo  "$str <br/>" . PHP_EOL;
    }
}

?>