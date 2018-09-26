<?php

function addDataAjax($data, $format) {
    // override or add some data from content object's returned data
    global $_SITETYPE;

    if ($format == 'phone') {
        if (isset($_SESSION) && isset($_SESSION['tour_plays'])) {
            $tour_plays = count($_SESSION['tour_plays']);
        } else {
            $tour_plays = NULL;
        }
        if (isset($_SESSION) && isset($_SESSION['counting'])) {
            $counting = count($_SESSION['counting']);
        } else {
            $counting = NULL;
        }

        $reset = '';
        $reset_script = '';
        if (TRUE === TOUR_RESET) {
            $reset_script = 'session_clear.php';
        }
        $data['misc'] = array(
            'mobile_images' => MOBILE_IMAGES,
            'mobile_url' => MOBILE_URL,
            'site_type' => $_SITETYPE,
            'tour_reset' => TOUR_RESET,
            'tour_play_limit' => TOUR_PLAY_LIMIT,
            'tour_max_pages' => TOUR_MAX_PAGES,
            'current_page_url' => curPageURL(),
            'reset' => $reset_script,
            'session_page_count' => $counting,
            'session_tour_plays' => $tour_plays,
        );
    //print_r($data);
        return $data;
    }
    
}


function ajax_fave_update($member_id, $id, $type, $err_invalid, $phone = FALSE) {
    // check scene ID validity. numeric.
    $bool = ( !is_int($id) ? (ctype_digit($id)) : true );
    if (! $bool) {
        // not a valid scene ID
        //echo json_encode(array('error' => $err_invalid));
        return FALSE;
    }

    $details = array();
    // get scene/content info
    switch ($type) {
        case 'video':
        case 'photo':
            // get scene info (does not really validate scene by $site_id, apparently)
            $scene = Paysite::get_content_scene($id, $member_id);
            if ($scene === NULL) {
                // not a valid scene ID
                //echo json_encode(array('error' => $err_invalid));
                return FALSE;
            }

            //makes sure it is not a future release
            if (strtotime($scene['scene_released']) > time()) {
                // not a valid scene ID, releases in the future
                //echo json_encode(array('error' => $err_invalid));
                return FALSE;
            }

            // just get a few data from scenes
            $details = array(
                'name' => $scene['scene_name'],
                'title' => $scene['title_name'],
                'id' => $scene['scene_id'],
                //'in_favorites' => $scene['in_favorites'], // if it was in favorites, prior to the toggle
            );
            if ($phone == TRUE) {
                $details['image_html'] = '<img src="'. MOBILE_IMAGES . 'icon_menu_favorite.png" style="width:8px;border:none;" />';
            }
            $state = Paysite::toggle_favorite($id, $member_id);
            break;
        case 'dvd':
            $dvd = Paysite::get_group_data($id, $member_id);
            if ($dvd === NULL) {
                // not a valid scene ID
                echo json_encode(array('error' => 'DVD Invalid!'));
                return FALSE;
            }
            //makes sure it is not a future release
            if (strtotime($dvd['title_published']) > time()) {
                echo json_encode(array('error' => 'DVD Invalid! Publish date is in the future!'));
                return FALSE;
            }
            
            $details = array(
                'name' => $dvd['group_name'],
                'id' => $dvd['group_id'],
            );
            $state = Paysite::toggle_group_favorite($id, $member_id);
            break;
        case 'model':
            $model = Paysite::get_performer_data($id, $member_id);
            if ($model === NULL) {
                // not a valid scene ID
                echo json_encode(array('error' => $err_invalid));
                return FALSE;
            }
            
            $details = array(
                'name' => $model['performer_name'],
                'id' => $model['performer_id'],
            );
            $state = Paysite::toggle_model_favorite($id, $member_id);
            break;
        case 'mag':
            $state = Paysite::toggle_magazine_favorite($id, $member_id);
            break;
        case 'cat':
            // can favor categories too
            $state = Paysite::toggle_category_favorite($id, $member_id);
            break;
        default:
            break;
    }

    $tmp = array(
        $type => array(
            $id => array(
                'fav' => $state, // state: 1 = favorite, 0 not favorite
                'details' => $details,
            ),
        )
    );


    //SUCCESS

    return json_encode($tmp);
}

// given an array of numbers $list get the ones that are faves
function ajax_fave_check($list, $type, $member_id = NULL) {
    // no member?
    if (is_null($member_id) || empty($member_id)) {
        return json_encode(array('error' => 'NO MEMBER ID'));
    }
    sort($list);
    $faves = Paysite::get_favorite_status($list, $member_id, $type);
    $return = array();
    foreach ($faves as $f) {
        $return[$f] = 1;
    }
    foreach ($list as $l) {
        if (empty($return[$l])) {
            $return[$l] = 0;
        }
    }
    return json_encode(array('fav_status' => $return, 'more_info' => array('type' => $type, 'user' => $member_id)));
}


// callbacks
$callbackFav = function ($type, $option_code, $id) {

    //error_reporting(E_ALL);
    $invalid = 'SCENE INVALID';


    // check for member
    if (isset($_SESSION["member"]["member_id"])) {
        $member_id =$_SESSION["member"]["member_id"];
    } else {
        echo json_encode(array('error' => 'NO MEMBER ID'));
        exit;
    }

    if ($option_code == 'p') {
        $phone = TRUE; // this is the phone site
    } else {
        $phone = FALSE;
    }
    
    echo ajax_fave_update($member_id, $id, $type, $invalid, $phone);
};



//TODO: make part of ajax object??
function getContentAjax(Hustler_Content_Ajax $content, $params = array()) {
// params: array(
//      'type',
//      'perpage',
//      'page',
//      'order'
//      'code'
//      'filter'
//      'username'
//    )
// array(
//      'type' => 'scene',
//      'scene' => scene ID
//      ),
    
    $p = $params;
    
    if (isset($p['type'])) {
        if (isset($p['page'])) {
            if (isset($p['order'])) {
                if (isset($p['code'])) {
                    if (isset($p['filter'])) {
///echo "type={$p['type']}/order={$p['order']}/filter={$p['filter']}/code={$p['code']}/page={$p['page']}" . PHP_EOL . PHP_EOL . PHP_EOL;

                        return $content->setType($p['type'])->setCode($p['code'])->setFilters($p['filter'], $p['username'])->setOrder($p['order'])->get($p['perpage'], $p['page']);
                    }
//echo "type={$p['type']}/order={$p['order']}/code={$p['code']}/page={$p['page']}" . PHP_EOL . PHP_EOL . PHP_EOL;
//TODO:  USE $p['code']
                    return $content->setType($p['type'])->setOrder($p['order'])->get($p['perpage'], $p['page']);
                }
//echo "type={$p['type']}/order={$p['order']}/page={$p['page']}" . PHP_EOL . PHP_EOL . PHP_EOL;
                return $content->setType($p['type'])->setOrder($p['order'])->get($p['perpage'], $p['page']);
            }
//echo "type={$p['type']}/page={$p['page']}/" . PHP_EOL . PHP_EOL . PHP_EOL;
            return $content->setType($p['type'])->get($p['perpage'], $p['page']);
        }
//echo "type={$p['type']}" . PHP_EOL . PHP_EOL . PHP_EOL;
        $p['page'] = 1;
        return $content->setType($p['type'])->get($p['perpage'], $p['page']);
    }
//echo 'no type' . PHP_EOL . PHP_EOL . PHP_EOL;
    $p['page'] = 1;
    return $content->setType()->get($p['perpage'], $p['page']);
}


function getAjaxFilters($filters) {
    $tmp = explode('|', $filters);
    $arr = array();
    foreach ($tmp as $t) {
        $tmp2 = explode('=', $t);
        if ((isset($tmp2[0]) && isset($tmp2[1])) && 
            (!empty($tmp2[0]) && !empty($tmp2[1]))
        ) {
            $arr[$tmp2[0]] = $tmp2[1];
        }
    }
    return $arr;
}

