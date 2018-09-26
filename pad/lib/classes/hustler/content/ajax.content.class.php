<?php

// TODO(?): why isn't ordered set any other way than $sortOverride in parent::getContent() ????
// 
// Unlike the other content classes, this is one class to support multiple content type
// TODO: These methods must eventually go to Hustler_Content

class Hustler_Content_Ajax extends Hustler_Content {

    public $imgSizes;
    private $siteFormat; // pad, phone, tour2012, etc.
    public $sortOverride; // we can override the "sort/filter" field
    private $output; // 'data' (array) or 'ajax' (default)
    
    function __construct($siteFormat = 'pad', $tour = TRUE, $member_id = FALSE) {
        // $siteFormat: pad, tour, phone
        $this->reset($siteFormat);        

        $tourJoinLinks = FALSE;
        parent::__construct($tour, $tourJoinLinks);
        if ($tour != TRUE) $this->setMember($member_id);
        $this->setMobile(); // we currently are using this only for mobile/phone/pad
        //$this->reset();
        return $this;
    }
    
    public function reset($siteFormat) {
        $this->siteFormat = $siteFormat;
        $this->output = 'ajax';
    }
    
    public function get($num, $page) {
        if ($this->output == 'ajax') {
            return $this->load($num, $page)->getJson();
        } else {
            // data array
            return $this->load($num, $page)->getData();
        }
    }
    
    public function getScene($scene_id, $dl = FALSE) {
	// JSON only (not data array)
        $data = $this->loadScene($scene_id)->getFirst(); // get Hustler Scene object

        $dl ? $dl_clip = $data->fullVidBest() : $dl_clip = NULL;
        
        $output = array();
        if ($this->type == self::$TYPE_VIDEOS) {
            //initialize output array to be JSON'd                
            if (!empty($data)) {
                if ($this->siteFormat == 'pad') {
                    $output[$this->type][] = array(
                        'scene_id' => $data->scene_id,
                        'title' => $data->title,
                        'name' => $data->scene_name,
                        'date' => date("M j Y", strtotime($data->scene_released)),
                        'dl_clip' => $dl_clip,
                        'clip_video' => $data->clip_video,
                        'ss' => $data->image,
                        'ss_large' => $data->image_large,
                    );
                } elseif ($this->siteFormat == 'phone') {

                }
            }
        }
        if ($this->output == 'ajax') {
            return json_encode($output);
        }
        return $output;
    }
   
    public function setOutput($output = 'ajax') {
        $this->output = $output;
        return $this;
    }
        
    public function setType($type = 'videos') {
        // type: videos, photos, models

        // make sure it is correct type...
        if (! $this->checkType($type)) {
            error_log('wrong content type for ajax object!');
            return $this;
        }

        $this->type = $type;
        $this->imgSizes = $this->getImgSizes($this->siteFormat, $type);
        return $this;
    }
 

    
    public function process($hd = FALSE, $nonmobile = TRUE) {
        if ($this->siteFormat == 'phone') { // don't run this on phone
            return parent::process($hd, $nonmobile);
        }
            
        if ($this->type == self::$TYPE_VIDEOS) {
            // for videos, we will do less than what content.class.phhp does, to be more efficient on database.
            // like process() but don't do parse_content() 
            
            $sitetype = $this->getSiteType();
            // get member ID from params
            $member_id = $this->getMemberId();

//echo "\n\n content raw:\n";print_r($this->content_raw); exit;
            // make some titles
            $data = array();
            foreach ($this->content_raw['data'] as $k=>$d) {
                $arr = array();
                $arr['scene_id'] = $d['scene_id'];
                $arr['count'] = $k;
                $arr['title'] = $d['title_name'] . ': ' . $d['scene_name'];
                $s = $d['scene_name'];
                if (strlen($s) > 31) {
                    $s = substr($s, 0, 31) . '...'; // size limit
                }
                $arr['scene_name'] = $s;
                //images

                if (!$hd) {
                    $arr['image'] = Content::image_handler(CACHEURL, CACHEPATH, $d['media_location'], $this->imgSizes['img_x'], $this->imgSizes['img_y']);
                } else {
                    // additional parameters for Content::image_handler below (we make $hd true: 
                    //    $resize = false, $watermark = false, $trimborder=false, $nodetect=true, $passthru=false, $hd=false, $classic=false);
                    $arr['image'] = Content::image_handler(CACHEURL, CACHEPATH, $d['media_location'], $this->imgSizes['img_x'], $this->imgSizes['img_y'],false, false, false, true, false, true);
                }
//                usleep(IMAGECACHE_UDELAY); // breathe time for image cacheing
                
                $arr['scene_released'] = date('n/j/y', strtotime($d['scene_released']));
                $arr['title_name'] = $d['title_name'];
                //$arr['scene_name_full'] = $arr['scene_name'] . ' in ' . $d['title_name'];
                //$arr['scene_description'] = $d['scene_description'];
                
                // are there tour images?
                if (!empty($d['tour_images'])) {
                    $arr['tour_images'] = $d['tour_images'];
                }
                $arr['media_location'] = $d['media_location'];
                // performers
//                $p = array();
//                $p_data = array();
//                if (isset($d['performers']) && !empty($d['performers'])) {
//                    foreach ($d['performers'] as $performer) {
//                        if (strtolower($performer['performer_gender']) == 'female') {
//                            $p[] = $performer['performer_name'];
//                            $p_data[] = array(
//                                'id' => $performer['performer_id'],
//                                'name_full' => $performer['performer_name'],
//                                'name_seo' => seo_name($performer['performer_name']),
//                            );
//                        }
//
//                    }
//                }
//                $arr['performers'] = $p;
//                $arr['performers_data'] = $p_data;

                $data[] = $arr;
            }

            return array('data' => $data);
        }
        else {
            return parent::process($hd, $nonmobile);
        }
    }

    private function getJson() {
        return json_encode($this->getData());
    }


    public function getData() {
        $type = $this->type;
        $data = $this->content['data'];
        //initialize output array to be JSON'd
        $output = array(
            $type => array(),
            'info' => $this->content_raw['info'],
        );

        if ($type == self::$TYPE_VIDEOS) {
            //initialize output array to be JSON'd                
            if (!empty($data)) {
                foreach ($data as $k=>$d) {
                    if ($this->siteFormat == 'pad') {
                        $categories = array();
                        if (isset($d['categories'])) {
                            $categories = $d['categories'];
                        }
                        $output[$type][] = array(
                            'id' => $d['scene_id'],
                            'name' => $d['scene_name'],
                            'title' => $d['title'],
                            'date' => date("F j Y", strtotime($d['scene_released'])),
                            'ss' => $d['image'],
                            
                            ////expanded... for further details
                            //'description' => $d['scene_description'],
                            //'performers_data' => $d['performers_data'],
                            'title_name' => $d['title_name'],
                            //'categories' => $categories,
                            //'average_score' => $this->content_raw['data'][$k]['average_score'],
                        );
                    } elseif ($this->siteFormat == 'phone') {
                        $str_cat = '';
                        if (isset($d['str_cat'])) {
                            $str_cat = $d['str_cat'];
                        }
                        $output[$type][] = array(
                            'title' => $d['title'], // phone title
                            'clip' => $d['clip'],
                            'ss' => $d['image'],
                            'date' => date("M j Y", strtotime($d['scene_released'])),
                            'fav_status' => $d['fav_status'],
                            'str_cat' => $str_cat,
                            'description' => $d['description'],
                            'performers' => $d['phone']['performers'],
                            'scene_id' => $d['scene_id'],
                        );
                    }
                }
            }
        } elseif ($this->type == self::$TYPE_MODELS) {
            //initialize output array to be JSON'd                
            if (!empty($data)) {
                foreach ($data as $k=>$d) {
                    if ($this->siteFormat == 'pad') {
                        $output[$type][] = array(
                            'name' => $d['name'],
                            'image' => $d['image'],
                            'date' => date("F j Y", strtotime($d['timestamp'])),
                            'id' => $d['model_id'],
                        );
                    }
                }
            }

        } elseif ($this->type == self::$TYPE_DVDS) {
            if (!empty($data)) {
                if ($this->siteFormat == 'pad') {
                    foreach ($data as $k=>$d) {
                        $output[$type][] = $d;
                    }
                }
            }
        } elseif ($this->type == self::$TYPE_MAGAZINES) {
            if (!empty($data)) {
                if ($this->siteFormat == 'pad') {
                    foreach ($data as $k=>$d) {
                        $output[$type][] = $d;
                    }
                }
            }
        } elseif ($this->type == self::$TYPE_PHOTOS) {
            if (!empty($data)) {
                foreach ($data as $k=>$d) {
                    $output[$type][] = array(
                        'id' => $d['scene_id'],
                        'name' => $d['name'],
                        'image' => $d['image'],
                        'description' => $d['scene_description'],
                        'average_score' => $this->content_raw['data'][$k]['average_score'],
                        'title_name' => $this->content_raw['data'][$k]['title_name'],
                        'date' => date("F j Y", strtotime($d['scene_released']))
                         //'date' => date("F j Y")
    //                        'scene_name' => $d['scene_name'],
    //                        'date' => date("M j Y", strtotime($d['scene_released'])),
    //                        'ss' => $d['image'],
                    );
                }
            }
        } elseif ($this->type == self::$TYPE_PHOTOS_DETAIL) {
            if (!empty($data)) {

                foreach ($data as $k=>$d) {
                    $output[$type][] = array(
                        'image' => $d['image'],
                        'image_large' => $d['image_large'],
                    );
                }
            }

        } elseif ($this->type == self::$TYPE_DVD_DETAIL) {
            // details are special, for one item. Don't use regular content classes nor pagination. So output will go with 'info'
            unset($output[$this->type]);
            if ($this->siteFormat == 'pad') {
                $dvd = $this->getDetailDvd();
                if (! empty($dvd)) {
                    $output['info']['contentDetails']['dvd'] = $dvd;
                }
            }

        } elseif ($this->type == self::$TYPE_MODEL_DETAIL) {
            // details are special, for one item. Don't use regular content classes nor pagination. So output will go with 'info'
            unset($output[$this->type]);
            if ($this->siteFormat == 'pad') {
                $model = $this->getDetailModel();
                if (! empty($model)) {
                    $output['info']['contentDetails']['model'] = array(
                        'performer_id' => $model['performer_id'],
                        'performer_name' => $model['performer_name'],
                        'performer_description' => $model['performer_description'],
                        'performer_body' => $model['performer_body'],
                        'performer_hair' => $model['performer_hair'],
                        'performer_eye' => $model['performer_eye'],
                        'performer_breast_type' => $model['performer_breast_type'],
                        //'performer_sign' => $model['performer_sign'], // not always there???
                        'average_score' => $model['average_score'],
                        'count_photosets' => $model['count_photosets'],
                        'count_videos' => $model['count_videos'],
                        'performer_timestamp' => $model['performer_timestamp'],
                        'in_favorites' => $model['in_favorites'],
                    );
                }
            }
        } elseif ($this->type == self::$TYPE_MAGAZINE_DETAIL) {
            // details are special, for one item. Don't use regular content classes nor pagination. So output will go with 'info'
            unset($output[$this->type]);
            if ($this->siteFormat == 'pad') {
                $mag = $this->getDetailMag();
                if (! empty($mag)) {
                    $output['info']['contentDetails']['mag'] = array( // not much
                        'in_favorites' => $mag['in_favorites'],
                        'desc' => $mag['magazine_description'],
                        'rating' => $mag['magazine_score'],
                        'type' => $mag['magazine_type'],
                        'issue' => $mag['magazine_issue'], // really a date in shorthand                        
                    );
                }
            }

        } elseif ($this->type == self::$TYPE_SCENE_DETAIL) {
            // details are special, for one item. Don't use regular content classes nor pagination. So output will go with 'info'
            // a "scene" can have info for a video, a photoset, or both. The ID identifies it.
            unset($output[$this->type]);
            if ($this->siteFormat == 'pad') {
                $scene = $this->getDetailScene();
                if (! empty($scene)) {
                    $output['info']['contentDetails']['scene'] = $scene;
                }
            }
        } elseif ($this->type == self::$TYPE_SITES) {
            if (!empty($data)) {
                $output[$type] = $data;
            }
        }
        
//print_r($output);exit;
        if (isset($output['info']['params'])) {
            // insecure to give back this info! But useful for testing, and a few fields are useful for pad app.
            $tmp = array();
            if (isset($output['info']['params']['videos_in_dvd'])) $tmp['videos_in_dvd'] = $output['info']['params']['videos_in_dvd'];
            if (isset($output['info']['params']['model_id'])) $tmp['model'] = $output['info']['params']['model_id'];
            $output['info']['params'] = $tmp;
	}
        $output['misc'] = array(
            //'mobile_images' => MOBILE_IMAGES,
            'mobile_url' => MOBILE_URL,
            'site_type' => $this->getSiteType(),
            //'tour_reset' => TOUR_RESET,
            //'tour_play_limit' => TOUR_PLAY_LIMIT,
            //'tour_max_pages' => TOUR_MAX_PAGES,
            'current_page_url' => curPageURL(),
            //'session_page_count' => count($_SESSION['counting']),
            //'session_tour_plays' => count($_SESSION['tour_plays']),
        );
        return $output;
    }


}


