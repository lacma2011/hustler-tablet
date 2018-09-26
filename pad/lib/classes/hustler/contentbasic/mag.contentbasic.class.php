<?php

/*
 *  Hustler_Mag_Contentbasic
 * 
 */

//namespace

// This retrieves photo content from magazines

class Hustler_Contentbasic_Mag implements iHustler_Contentbasic {
    private $params;
    private $brand; // hu (hustler) or bl (barely legal) or tb (taboo)
    
    private $num_magazines; // how many magazines we will go back up to for content
    private $limit; // how much content max to get.  Note that $num_magazines should be enough to reach this

    // magazine codes
    private static $Taboo = 'tb';
    private static $Hustler = 'hu';
    private static $BarelyLegal = 'bl';

    function __construct($site_code = NULL, $tour = FALSE) {
        // some defaults
        $this->setParam('magazine_id', NULL); // will get later
        $this->type = 'content';
        $this->setParam('perPage', MOBILE_PERPAGE);
        $this->setParam('sort', 'latest_updates');
        $this->setParam('site', $site_code);

        // generic stuff we don't change
        $this->setParam('member_id', FALSE);
        $this->setParam('type', 'photo');
        $this->setParam('check_trailer', FALSE);
        $this->setParam('model_id', FALSE);
        $this->setParam('search', FALSE);
        $this->setParam('group', FALSE);
        $this->setParam('category_id', FALSE);
        $this->setParam('watched_now', FALSE);
        $this->setParam(URL_OPTION_FAVE, FALSE);
        $this->setParam('limit', FALSE);
        $this->setParam('group_id', FALSE);
        $this->setParam('all_perf_status', FALSE);
        $this->setParam('mobile_clips', FALSE);
        $this->setParam('hdonly', FALSE);
        $this->setParam('flagged', FALSE);
        
        $this->brand = self::$Hustler;
        $this->num_magazines = 50; // default
        $this->limit = 70;
    }
    
    public function setHustler() {
        $this->brand = self::$Hustler;
        return $this;
    }

    public function setBL() {
        $this->brand = self::$BarelyLegal;
        return $this;
    }

    public function setTaboo() {
        $this->brand = self::$Taboo;
        return $this;
    }

    public function retrieveMagContent($totalMags, $limit = NULL) {
        // figure out perpage 
        // 
        if (empty($limit)) {
            $limit = $this->limit;
        }

        $mag_ids = $this->getLatestIDs($totalMags);
//echo "\n let's get $totalMags magazines \n\n" . print_r($mag_ids, TRUE);

        $content = array();
        $count = 0;
        // get the magazine content
        $contentPerMagazine = 100; // let's get up to 100 per magazine issue, we won't hit this ceiling!
	$scenes_found = array(); //  need to track what scenes we already have
        foreach ($mag_ids as $k=>$m_id) {
            $m = $this->params;
            $m['magazine_id'] = $m_id;
            $m['page'] = 1;
	    $m['sort'] = 'latest_updates';
            $m['perPage'] = $contentPerMagazine;

//print_r($m); echo "\n\n";
            $tmp = Paysite::get_content($m['type'], $m['sort'], $m['page'], $m['perPage'], $m['search'], 
                $m['model_id'], $m['site'], $m['group'], $m['category_id'], $m['watched_now'], 
                $m['member_id'], $m[URL_OPTION_FAVE], $m['limit'], $m['group_id'], $m['magazine_id'], 
                true, false, $m['check_trailer'], $m['all_perf_status'], $m['mobile_clips'],
                $m['hdonly'], $m['flagged']);
	    $x = 0;
 // print_r($tmp, TRUE);
#echo "\n mag content. #" . $m_id;             
            if (! empty($tmp['data'])) {
                foreach ($tmp['data'] as $t) {
		    if (empty($scenes_found[$t['scene_id']])) {
#echo "\n found " . $t['scene_id'];
                    	$content[] = $t;			
			$scenes_found[$t['scene_id']] = TRUE;
			$x++;
                    } else {
#echo "\n DUPE " . $t['scene_id'];
		    }
                }
            }
            $count += $x;
//echo "\n at $count for limit $limit";
            if ($count >= $limit) {
                break;
            }
                
        }
        return $content;

    }

    public function get($page = NULL) {
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
        
        $content = $this->retrieveMagContent($this->num_magazines, $this->limit); // can override number of magazines we go back to
//echo "\n\n returned Content:\n";print_r($content);
        if (!empty ($content)) {
            $tmp = array_chunk($content, $p['perPage']);
    #foreach ($tmp as $b) {foreach ($b as $t) {print "\n " . $t['scene_id'] . ' -- ' . $t['scene_name'];}}exit;
            $ret = Array (
                'data' => $tmp[$page_num - 1],
                'info' => array(
                    'totalRecords' => count($content) + 400, // forcing more photosets because we do have a lot!
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

        return $ret;
    }
    
    public function setImagesTour($num) {
        // feature isn't available for this class
        return $this;
    }

    public function setImagesTourNone() {
        // feature isn't available for this class
        return $this;
    }

    public function setParam($name, $value) {
//echo "\n setting $name with $value";
        $this->params[$name] = $value;
    }

    private function getLatestIDs($totalMags = NULL) {
        // TODO: maybe cache this? Paysite::get_magazines does do cacheing though

        if (empty($totalMags)) {
            $num =  $this->num_magazines;
        } else {
            $num =  $totalMags;
        }

        $m = array(
            'sort' => $this->brand,
            'page' => 1,
            'perpage' => $num, // get five issues
            'type' => NULL,
            'member_id' => NULL,
            'fave' => FALSE,
        );
        $mags_list = Paysite::get_magazines($m['sort'], $m['page'], $m['perpage'], $m['type'], $m['member_id'], $m['fave']);
        //same thing? 
        //$mags_list = Paysite::get_magazine_list($m['sort'], $m['page'], $m['perpage'], $m['type'], $$m['member_id'], $m['fave']);
        $ret = array();
        foreach($mags_list['data'] as $m) {
            $ret[] = $m['magazine_id'];
        }
        return $ret;
    }
}

?>