<?php

class Hustler_Content_Models extends Hustler_Content {

    public $imgSizes;
    
    function __construct($siteFormat, $tour = TRUE, $tourJoinLinks = FALSE) {
        parent::__construct($tour, $tourJoinLinks);
        $this->reset($siteFormat);
    }
    
    public function reset($siteFormat) {
        $this->type = self::$TYPE_MODELS;
        
        $this->imgSizes = $this->getImgSizes($siteFormat, self::$TYPE_MODELS);
    }


    function get($num, $page) {

    }
    
    function loadLatest($num = 1) {
        $page = 1;
        // latest_updates is the default sort already
        $tmp = parent::load($num, $page);
        return $this;
    }
    
    function loadFeatured($num = 1, $page = NULL) {
        // page received by parent class
        $this->sortOverride = 'featured';
        $tmp = parent::load($num, $page);
        $this->sortOverride = NULL;
        //trick to show more pages of models to non-featured models too...
        $this->content_raw['info']['totalRecords'] += 260; // TODO: 960 is too much, closer to truth!
        return $this;
    }
    
    function loadLatestFeatured($num = 1) {
        $page = 1;
        $this->sortOverride = 'featured';
        $tmp = parent::load($num, $page);
        $this->sortOverride = NULL;
        return $this;
    }
    
    function loadOnlyMostViewed() {
        $num = 1;
        $page = 1;
        $this->sortOverride = 'most_viewed';
        $tmp = parent::load($num, $page);
        $this->sortOverride = NULL;
        return $this;
    }
    
    function loadOnlyTopRated() {
        $num = 1;
        $page = 1;
        $this->sortOverride = 'top_rated';
        $tmp = parent::load($num, $page);
        $this->sortOverride = NULL;
        return $this;
    }
    
    public function getModelInfo($model_id) {
        // get just one model
    }

}

?>