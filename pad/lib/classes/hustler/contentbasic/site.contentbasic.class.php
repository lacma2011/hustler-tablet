<?php

/*
 *  Hustler_Contentbasic_Site
 * 
 *  gets the Sites sorted by latest updated, along with info on their latest video update
 * 
 */

//namespace

class Hustler_Contentbasic_Site implements iHustler_Contentbasic {
    private $params;

    function __construct($site_code = NULL, $tour = FALSE) {
        
        // some defaults
        $this->type = '';

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