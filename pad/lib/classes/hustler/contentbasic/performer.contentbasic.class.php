<?
/*
 *  Hustler_Performer_Contentbasic
 * 
 */

//namespace

// was Performer_Content from performer.content.class.php

class Hustler_Contentbasic_Performer implements iHustler_Contentbasic {

    private $params;

    function __construct($site_code = NULL, $tour = FALSE, $member_id = NULL) {
        // some defaults        
        $this->setParam('type', 'models');
        $this->setParam('sort', 'latest_updates');
        $this->setParam('pref_cat', NULL);
        $this->setParam('search', NULL);
        $this->setParam('videos_only', TRUE);
        $this->setParam('trailer', FALSE);
        $this->setParam('mobile_clips', FALSE);
        $this->setParam('member_id', $member_id);
        $this->setParam('alpha_subset', NULL);
        $this->setParam('gender', 'female');
        
        $site = Paysite::get_current_site($site_code);
        $this->setParam('site_id', $site['site_id']);
        unset ($site);
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
        if (isset($p['favorites_only']) &&  $p['favorites_only'] == 1) {
            // get only favorites for this member
            $p['type'] = 'favorite_models';
        }
//echo "\nperformers:\n";print_r($p);
        $content = Paysite::get_performers($p['sort'], $page_num, $p['perPage'], $p['type'], $p['member_id'], $p['pref_cat'],
                    $p['search'], $p['site_id'], $p['videos_only'],  $p['trailer'], $p['mobile_clips'], $p['alpha_subset'],
                    $p['gender']);

        $content['info']['params'] = $this->params;
        //$content['info']['params']['page'] = (int) $page;
//print_r($content);
        return $content;
    }
    
    public function setImagesTour($num) {
        // feature isn't available for this class
        return $this;
    }

    public function setImagesTourNone() {
        // feature isn't available for this class
        return $this;
    }
    
    public function selectAll($page = NULL) {
         // select models with photos and/or videos
        $this->setParam('videos_only', FALSE);
        return $this;
    }

    function setParam($name, $value) {
//echo "\n setting $name with $value";
        $this->params[$name] = $value;
    }

}

?>