<?php


class Hustler_Content_Pics extends Hustler_Content {

    public $imgSizes;

    function __construct($siteFormat, $tour = TRUE, $tourJoinLinks = FALSE) {
        parent::__construct($tour, $tourJoinLinks);
        $this->reset($siteFormat);
    }
    
    public function reset($siteFormat) {
        $this->type = self::$TYPE_PHOTOS;
        
        $this->imgSizes = $this->getImgSizes($siteFormat, self::$TYPE_PHOTOS);
    }
    
    function get($num, $page) {

    }


    function loadOnlyLatest($num = 1) {
        $page = 1;
        $tmp = parent::load($num, $page);
        return $tmp; // really $this
    }
    
}
