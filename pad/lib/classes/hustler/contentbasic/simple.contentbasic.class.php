<?php
/*
 *  Hustler_Simple_Contentbasic
 * 
 */

//namespace

// was Simple_Content from simple.content.class.php

class Hustler_Contentbasic_Simple implements iHustler_Contentbasic {

    private $params;
    
    //$imgTour;  // # if we want tour images, NULL if not
    //$imgPrev;  // # if we want preview image, NULL if not

    function __construct($site_code = NULL, $tour = FALSE) {
        
        // some defaults
        $this->type = 'content';
        $this->setParam('perPage', MOBILE_PERPAGE);
        $this->setParam('sort', 'latest_updates');
        $this->setParam('member_id', FALSE);
        $this->setParam('site', $site_code);

        $this->setParam('type', 'video');
        $tour? $this->setParam('check_trailer', TRUE): $this->setParam('check_trailer', FALSE);
        $this->setParam('model_id', FALSE);
        $this->setParam('all_perf_status', TRUE);
        $this->setParam('mobile_clips', TRUE);
        // generic stuff we hardly change
        $this->setParam('search', FALSE);
        $this->setParam('group', FALSE);
        $this->setParam('category_id', FALSE);
        $this->setParam('watched_now', FALSE);
        $this->setParam(URL_OPTION_FAVE, FALSE);
        $this->setParam('limit', FALSE);
        $this->setParam('group_id', FALSE);
        $this->setParam('magazine_id', FALSE);
        $this->setParam('hdonly', FALSE);
        $this->setParam('flagged', FALSE);

        
        // special ones
        $this->setParam('imgTour', NULL);
        $this->setParam('imgPrev', NULL); // not used
        
    }
    
    public function get($page = NULL) {
        $p = $this->params;
        // get page number. The function paramter overrides the class parameter
        if (!empty($page)) {
            $page_num = $page;
        } elseif (!empty($p['page'])) {
            $page_num = $p['page'];
        } else {
            $page_num = 1;
        }
        // for getting dvd's
        if (isset($p['videos_in_dvd'])) {
            //$p['type'] = 'videos_in_dvd';
            $p['group_id'] = $p['videos_in_dvd'];
            unset($p['videos_in_dvd']);
            //unset($p['mobile_clips']);
        }
//print "<BR>" . print_r($p, TRUE) . "<BR> page#$page_num";
        $content = Paysite::get_content($p['type'], $p['sort'], $page_num, $p['perPage'], $p['search'], 
                $p['model_id'], $p['site'], $p['group'], $p['category_id'], $p['watched_now'], 
                $p['member_id'], $p[URL_OPTION_FAVE], $p['limit'], $p['group_id'], $p['magazine_id'], 
                true, false, $p['check_trailer'], $p['all_perf_status'], $p['mobile_clips'],
                $p['hdonly'], $p['flagged']);
        
        if (! empty($p['imgTour'])) {
            foreach ($content['data'] as $k=>$scene) {
                $content['data'][$k]['tour_images'] = ImageHandler::get_tour_image($p['imgTour'], $scene['scene_id']);
            }
        }

        $content['info']['params'] = $this->params;
        //$content['info']['params']['page'] = (int) $page;
        return $content;
    }

    public function setImagesTour($num) {
        $this->setParam('imgTour', $num);
        return $this;
    }

    public function setImagesTourNone() {
        $this->setParam('imgTour', NULL);
        return $this;
    }

    public function setParam($name, $value) {
//echo "\n setting $name with $value";
        $this->params[$name] = $value;
    }

}
