<?php

/*
 *  Hustler_Contentbasic_Dvd
 * 
 *  gets the DVD titles
 * 
 */

//namespace

class Hustler_Contentbasic_Dvd implements iHustler_Contentbasic {
    private $params;

    function __construct($site_code = NULL, $tour = FALSE) {
        
        // some defaults
        $this->type = 'content';
        $this->setParam('perPage', MOBILE_PERPAGE);
        $this->setParam('sort', 'latest_updates');
        $this->setParam('member_id', FALSE);
        $this->setParam('site', $site_code);

        $this->setParam('type', 'video');
        $tour? $this->setParam('check_trailer', TRUE): $this->setParam('check_trailer', FALSE);  // NOTE: DVDs aren't filtered whether scenes have mobile clip or not
        $this->setParam('model_id', FALSE);
        $this->setParam('all_perf_status', TRUE);
        $this->setParam('mobile_clips', TRUE);   // NOTE: DVDs aren't filtered whether scenes have mobile clip or not
        // generic stuff we hardly change
        $this->setParam('search', FALSE);
        $this->setParam('group', TRUE); // determines that we will use DVD
        $this->setParam('category_id', FALSE);
        $this->setParam('watched_now', FALSE);
        $this->setParam(URL_OPTION_FAVE, FALSE);
        $this->setParam('limit', FALSE);
        $this->setParam('group_id', FALSE);
        $this->setParam('magazine_id', FALSE);
        $this->setParam('hdonly', FALSE);
        $this->setParam('flagged', FALSE);
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

//print "<BR>" . print_r($p, TRUE) . "<BR> page#$page_num";
        $content = Paysite::get_content($p['type'], $p['sort'], $page_num, $p['perPage'], $p['search'], 
                $p['model_id'], $p['site'], $p['group'], $p['category_id'], $p['watched_now'], 
                $p['member_id'], $p[URL_OPTION_FAVE], $p['limit'], $p['group_id'], $p['magazine_id'], 
                true, false, $p['check_trailer'], $p['all_perf_status'], $p['mobile_clips'],
                $p['hdonly'], $p['flagged']);

        $content['info']['params'] = $this->params;
        //$content['info']['params']['page'] = (int) $page;
        return $content;
    }

    public function getAllTitles($page = NULL) { // uses a different Paysite method, and adds pagination
        // this works differently than other content classes. We will only get a lot starting from page 1
        $p = $this->params;
        // get page number. The function paramter overrides the class parameter
        if (!empty($page)) {
            $page_num = $page;
        } elseif (!empty($p['page'])) {
            $page_num = $p['page'];
        } else {
            $page_num = 1;
        }
//print "<BR>" . print_r($p, TRUE) . "<BR> page#$page_num ...";
        
        $content = Paysite::get_all_titles();
//echo "\n\n returned Content:\n";print_r($content);
        if (!empty ($content)) {
            $tmp = array_chunk($content, $p['perPage']);
            $ret = Array (
                'data' => $tmp[$page_num - 1],
                'info' => array(
                    'totalRecords' => count($content), // forcing more photosets because we do have a lot!
                    'totalPages' => count($tmp),
                    'currentPage' => $page_num,
            ));
            $ret['info']['params'] = $this->params;
        } else {
            // got nothing
            $ret = Array (
                'data' => array(),
                'info' => array(
                    'totalRecords' => 0, // forcing more photosets because we do have a lot!
                    'totalPages' => 0,
                    'currentPage' => 1,
            ));
        }
//print_r($ret);
        return $ret;
    }

    public function setParam($name, $value) {
//echo "\n setting $name with $value";
        $this->params[$name] = $value;
    }

}

?>