<?
/*
 *  Hustler_Photo_Contentbasic
 * 
 */


class Hustler_Contentbasic_Photodetail implements iHustler_Contentbasic {

    private $params;
    
    //$imgTour;  // # if we want tour images, NULL if not
    //$imgPrev;  // # if we want preview image, NULL if not

    function __construct($scene_id, $site_code = NULL, $tour = FALSE) {
        
        // some defaults
        $this->type = 'content';
       
       // set the incoming scene_id
        $this->setParam('scene_id', $scene_id);

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
        
        
        $content = Paysite::get_photo_listing($p['scene_id']);

        
        if (! empty($p['imgTour'])) {
            foreach ($content['data'] as $k=>$scene) {
                $content['data'][$k]['tour_images'] = ImageHandler::get_tour_image($p['imgTour'], $scene['scene_id']);
            }
        }


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

?>