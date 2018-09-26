<?php


class Hustler_Item_Scene extends Hustler_Item {
    private $data;
    private $myData;
    private $siteType;
    private $tourJoinLink;

    function __construct($data, $siteType = 'tour', $tourJoinLink = FALSE) {
        parent::__construct(ITEMTYPE_VIDEO);
        if (empty($data)) {
            $this->data = array(
                'scene_id' => null,
                'image' => null,
                'scene_released' => null,
                'scene_name' => null ,
            );
        } else {
            $this->data = $data;
// TODO: the _output stuff should actually not be part of the class but instead a class for the page only
            $this->data['_pages'] = array(
                'special' => $this->getSpecial(),
            );
        }
        $this->siteType = $siteType;
        $this->tourJoinLink = '';
        if (FALSE !== $tourJoinLink) {
            $this->tourJoinLink = $tourJoinLink;
        }
    }

    function trailerBest() {
        $data = $this->data;
        if (empty($data['clip_trailer_download'])) {
            return NULL;
        }
        $field = 'videoBest';
        if (empty($this->myData[$field])) {
            $this->myData[$field] = $this->getClipLink($data['clip_trailer_download']);
        }
        return $this->myData[$field];
    }
    
    function fullVidBest() {
//TODO!!!
        // check for scene clip first, if not available then fall back to trailer
        $data = $this->data;
        if (empty($data['clip_video'])) {
            if (empty($data['clip_trailer_download'])) {
                return NULL;
            } else {
                $clip = $data['clip_trailer_download'];
            }
        } else {
            $clip = $data['clip_video'];
        }
        $field = 'videoBest';
        if (empty($this->myData[$field])) {
            $this->myData[$field] = $this->getClipLink($clip);
        }
        return $this->myData[$field];
    }

    function videoMedium() {
        $data = $this->data;
        $field = 'videoMed';
        if (empty($this->myData[$field])) {
            $tmp = $data['video_options']['mp4']['medium'];
            $this->myData[$field] = $this->getClipLink($tmp);
        }
        return $this->myData[$field];
    }
        
    function videoHigh() {
        $data = $this->data;
        $field = 'videoHigh';
        if (empty($this->myData[$field])) {
            $tmp = $data['video_options']['mp4']['high'];
            $this->myData[$field] = $this->getClipLink($tmp);
        }
        return $this->myData[$field];
    }

    function images($index) {
        return $this->data['more_images'][$index];
    }

    private function getClipLink($clip_url) {
        return $clip_url . make_dl_token($this->siteType);
    }
    
    private function getSpecial() {
        //categories
        $categories_html = '';
        $categories = $this->categories;
	if (!empty($categories)) {
            end($categories);
            $last = key($categories);
            reset($categories);
            foreach ($categories as $k=>$category) {
                $categories_html .= '<a href="' .  $this->tourJoinLink . '">' . $category . '</a>';
                if ($k != $last) {
                    $categories_html .= ',&nbsp;';
                }
            }
	} 
        
        return array(
            'categories_html' => $categories_html,
        );
    }

    function __get($name) {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        } elseif (method_exists($this, $name)) {
            return $this->$name();
        } else {
            return NULL;
        }
    }
}

?>