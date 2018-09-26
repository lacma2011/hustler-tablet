<?php

class Hustler_Content_Scenes extends Hustler_Content {
    public $imgSizes;
    
    public $scene;  // scene ID, if it is a scene
    public $sortOverride; // we can override the "sort/filter" field
    
    public $hd; // if it's an hd clip. Will add a watermark for HD icon

    function __construct($siteFormat, $tour = TRUE, $tourJoinLinks = FALSE) {
        parent::__construct($tour, $tourJoinLinks);
        $this->reset($siteFormat);
    }
    
    public function reset($siteFormat) {
        $this->type = self::$TYPE_VIDEOS;

        $this->scene = NULL;
        $this->sortOverride = NULL;
        $this->hd = FALSE;
        
        $this->imgSizes = $this->getImgSizes($siteFormat, self::$TYPE_VIDEOS);
    }

    public function setHD() {
        $this->hd = TRUE;
    }

    public function unsetHD() {
        $this->hd = FALSE;
    }

    function get($num, $page) {
        
    }

// override parent method load(), because of makeMainImage(), or rewrite ??!
    function load($num, $noXlPanel = FALSE, $page = NULL) {
        //
        // $noXlPanel... by default (FALSE) the first video will get a bigger image to fit a very large panel
        $tmp = parent::load($num, $page);
        if ($noXlPanel != TRUE) {
            $this->makeMainImage(LATEST_CLIP); // make a very large image for very large panel
        }
//print_r($this->content);
        return $tmp; // really $this
    }
    
    function loadFeatured($num, $noXlPanel = FALSE, $page = NULL) {
        //
        // $noXlPanel... by default (FALSE) the first video will get a bigger image to fit a very large panel
        $this->sortOverride = 'featured';
        $tmp = parent::load($num, $page);
        $this->sortOverride = NULL;
        if ($noXlPanel != TRUE) {
            $this->makeMainImage(LATEST_CLIP); // make a very large image for very large panel
        }
//print_r($this->content);
        return $tmp; // really $this
    }
    
    
    function loadLatest($num = 1) {
        $page = 1;
        $tmp = parent::load($num, $page);
        $this->makeMainImage(LATEST_CLIP); // make a very large image for very large panel
        return $tmp; // really $this
    }
    
    function loadLatestFeatured($num = 1) {
        $page = 1;
        $this->sortOverride = 'featured';
        $tmp = parent::load($num, $page);
        $this->sortOverride = NULL;
        $this->makeMainImage(LATEST_CLIP); // make a very large image for very large panel
        return $tmp; // really $this
    }
    
    function loadOnlyMostViewed() {
        $num = 1;
        $page = 1;
        $this->sortOverride = 'most_viewed';
        $tmp = parent::load($num, $page);
        $this->sortOverride = NULL;
        $this->makeMainImage(LATEST_CLIP); // make a very large image for very large panel
        return $tmp; // really $this
    }
    
    function loadOnlyTopRated() {
        $num = 1;
        $page = 1;
        $this->sortOverride = 'top_rated';
        $tmp = parent::load($num, $page);
        $this->sortOverride = NULL;
        $this->makeMainImage(LATEST_CLIP); // make a very large image for very large panel
        return $tmp; // really $this
    }

    public function getEmptyScene() {
        return new Hustler_Item_Scene(NULL);
    }

}

?>