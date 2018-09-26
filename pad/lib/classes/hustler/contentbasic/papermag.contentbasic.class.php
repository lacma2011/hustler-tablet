<?php

/*
 *  Hustler_Mag_Contentbasic
 * 
 */

//namespace

// This retrieves magazine listings

class Hustler_Contentbasic_Papermag implements iHustler_Contentbasic {
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
        $this->setParam('fave', 'photo');
        $this->setParam('type', 'photo');
        
        $this->brand = self::$Hustler;
        $this->num_magazines = 35; // default
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
    
    public function setAll() {
        $this->brand = FALSE;
        return $this;
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

//print "<BR>" . print_r($p, TRUE) . "<BR> page#$page_num ...";        
        return $this->retrievePaperMags($page_num, $this->num_magazines); // can override number of magazines we go back to

    }
    
    private function retrievePaperMags($page = NULL, $perPage = NULL) {
        if ($page === NULL) {
            $page =  $this->p['page'];
        }

        if ($perPage === NULL) {
            $num =  $this->num_magazines;
        } else {
            $num =  $perPage;
        }
        
        ! empty($this->params['member_id']) ? $member_id = $this->params['member_id'] : $member_id = NULL;
        if (! empty($this->params['favorites_only'])) {
            $fave = TRUE;
            $type = 'favorite_magazines';
            $sort = FALSE;
        } else {
            $fave = FALSE;
            $type = FALSE;
            $sort = $this->brand;
        }

        $m = array(
            'sort' => $sort,
            'page' => $page,
            'perpage' => $num, // get five issues
            'type' => $type,
            'member_id' => $member_id,
            'fave' => $fave,
        );
//print_r($m);
        $mags_list = Paysite::get_magazines($m['sort'], $m['page'], $m['perpage'], $m['type'], $m['member_id'], $m['fave']);
        //same thing? 
        //$mags_list = Paysite::get_magazine_list($m['sort'], $m['page'], $m['perpage'], $m['type'], $$m['member_id'], $m['fave']);
        return $mags_list;
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
}

?>