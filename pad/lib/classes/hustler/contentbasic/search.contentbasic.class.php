<?
/*
 *  Hustler_Photo_Contentbasic_Search
 * 
 */

require_once(BASEROOT . "/includes/cache_config.php");

class Hustler_Contentbasic_Search implements iHustler_Contentbasic {

    private $params;
    
    //$imgTour;  // # if we want tour images, NULL if not
    //$imgPrev;  // # if we want preview image, NULL if not

    function __construct($searchText, $typeCode,  $category = "all", $site_code = NULL, $tour = FALSE) {
        $this->setParam('perPage', MOBILE_PERPAGE);

        $this->searchText = $searchText;
        $this->typeCode = $typeCode;
        $this->category = $category;
        $this->search = new SphinxClient();
		$this->search->setServer( "10.22.22.34", 9312 );
		$this->search->setMatchMode(SPH_MATCH_EXTENDED);
        
        
    }
    
    public function get($page = NULL) {
       
        $content = array();
    
        $p = $this->params;
        // get page number. The function paramter overrides the class parameter
        if (!empty($page)) {
            $page_num = $page;
        } elseif (!empty($p['page'])) {
            $page_num = $p['page'];
        } else {
            $page_num = 1;
        }

		$perpage =  $p['perPage'];
		$current_page = $page_num;

		$start = $current_page == 1 ? 0 : ($current_page - 1) * $perpage + 1;
		$end = $perpage;
		$this->setParam('page_num', $page_num); // so that createReturnArray() will have access to page_num

		$this->search->setLimits($start, $end, 10000); // last item is specific to pecl version

		$search_terms = urldecode(strip_tags(trim($this->searchText)));
		$sphinx_results = false;
        $searchVideos = $searchDvds = $searchPhotos = $searchModels = 0;
      
        if ( $this->typeCode == "searchVideos" ) {
            $this->search->setFilter('scene_live', array(1));
            $this->search->setFilterRange('filter_timestamp', strtotime('1969-01-01 00:00:00'), time());
            $this->search->setSortMode(SPH_SORT_RELEVANCE);
          
             $this->search->setFieldWeights(
    			array(
    				'title_name' => 100,
    				'search_index' => 80,
    				'release_timestamp' => 60,
    				'media_location' => 40
    			)
						);
             $sphinx_results = $this->search->query($search_terms, 'videos');  
             
             if ( $this->category != 0 ) {
             
                $sphinx_results = $this->filterForCategories($sphinx_results);
             
             }
           
             $content = $this->createReturnArray("videos", $sphinx_results);
             
        }
        
         elseif ( $this->typeCode == "searchDvds" ) {
            
           // $this->search->setFilter('performer_status', array(1));
           // $this->search->setFilterRange('filter_timestamp', strtotime('1969-01-01 00:00:00'), time());
           // $this->search->setSortMode(SPH_SORT_EXTENDED, 'title_published DESC');
          
             $this->search->setFieldWeights(
    			array(
    				'title_name' => 100,
				    'title_description' => 40

    			)
			 );
             $sphinx_results = $this->search->query($search_terms, 'dvds'); 
            

              if ( $this->category != 0 ) {
                $sphinx_results = $this->filterForCategories($sphinx_results);
             }
             
            
            $content = $this->createReturnArray("dvds", $sphinx_results);
        }
        
        
        elseif ( $this->typeCode == "searchModels" ) {

            $this->search->setFilter('performer_status', array(1));
            $this->search->setFilterRange('filter_timestamp', strtotime('1969-01-01 00:00:00'), time());
            $this->search->setSortMode(SPH_SORT_EXTENDED, 'performer_timestamp DESC');
          
             $this->search->setFieldWeights(
    			array(
    				'performer_name' => 100,
				    'performer_description' => 40,
					'performer_ethnicity' => 20
    			)
			 );
			 
             $sphinx_results = $this->search->query($search_terms, 'performers'); 
            
             if ( $this->category != 0 ) {
                $sphinx_results = $this->filterForCategories($sphinx_results);
             }
            
            
             $content = $this->createReturnArray("models", $sphinx_results);
        }
        

        elseif ( $this->typeCode == "searchPhotos" ) {

            $this->search->setFilterRange('filter_timestamp', strtotime('1969-01-01 00:00:00'), time());
            $this->search->setSortMode(SPH_SORT_EXTENDED, 'release_timestamp DESC');
            $this->search->setGroupBy('scene_id', SPH_GROUPBY_ATTR);
          
            $this->search->setFieldWeights(
    			array(
                    'scene_name' => 100,
                    'media_location' => 40
    			)
			 );
             $sphinx_results = $this->search->query($search_terms, 'photosets'); 
           
            if ( $this->category != 0 ) {
              
                $sphinx_results = $this->filterForCategories($sphinx_results);
                
             } 
             
             $content = $this->createReturnArray("photos", $sphinx_results);
        }

        return $content;
    }


    private function filterForCategories(&$sphinx_results) { // reason to pass by reference.. not to change original, but just to avoid passing the large array around
      
        $new_sphinx_results = array();
        
        switch ( $this->typeCode ) {
        
            case "searchVideos":
        
                $sql = "select  stc.scene_id ";
                $sql .= "from lfpcms_scenes_to_categories stc " .
                        "where stc.category_id = " . $this->category;
                
                $cache_key = md5($sql);
                $scene_ids = null;
                /*if (getenv('ENVIRONMENT') != "DEVELOPMENT") {
                    $scene_ids = $memcache_obj->get($cache_key);
                }*/
                
                if ( !$scene_ids ) {
                    $scene_query_res = mysql_query($sql);
                    $scene_ids = array();
                    while($data = mysql_fetch_assoc($scene_query_res)) {
                		$scene_ids[] = $data['scene_id'];
                	}
            	}
            	
                foreach ( $sphinx_results['matches'] as $id=>$val ) {
                    if ( in_array($id, $scene_ids) ) {
                        $new_sphinx_results['matches'][] = $sphinx_results['matches'][$id];
                    }
                }
            
                break;
            
            case "searchDvds":
                $sql = "select ctgc.group_id ";
                $sql .= "from lfpcms_content_groups_to_categories ctgc " .
                        "where ctgc.category_id = " . $this->category;

                $cache_key = md5($sql);
                $group_ids = null;
               /* if (getenv('ENVIRONMENT') != "DEVELOPMENT") {
                    $group_ids = $memcache_obj->get($cache_key);
                }*/
                
                if ( !$group_ids ) {
                    $dvd_query_res = mysql_query($sql);
                    $group_ids = array();
                    while($data = mysql_fetch_assoc($dvd_query_res)) {
                		$group_ids[] = $data['group_id'];
                	}
            	}
            	foreach ( $sphinx_results['matches'] as $id => $val ) {
            	    
                    if ( in_array($sphinx_results['matches'][$id]['attrs']['default_group_id'], $group_ids) ) {
                        $new_sphinx_results['matches'][] = $sphinx_results['matches'][$id];
                    }
                }
                break;
                
            case "searchModels":
                 $sql = "SELECT " .
                  "p.performer_id " .
                "FROM " .
                  "lfpcms_performers p " .
                 "LEFT JOIN lfpcms_scenes_to_performers stp " .
                   "ON stp.performer_id = p.performer_id " .
                 "LEFT JOIN lfpcms_scenes_to_categories stc " .
                   " ON stc.scene_id = stp.scene_id " .
                " WHERE p.performer_status = 1 " .
                 " AND stc.category_id = " . $this->category .
                 " GROUP BY p.performer_id ";
                
                $cache_key = md5($sql);
                $model_ids = null;
               /* if (getenv('ENVIRONMENT') != "DEVELOPMENT") {
                    $group_ids = $memcache_obj->get($cache_key);
                }*/
                if ( !$model_ids ) {
                    $model_query_res = mysql_query($sql);
                    $model_ids = array();
                    while($data = mysql_fetch_assoc($model_query_res)) {
                		$model_ids[] = $data['performer_id'];
                	}
            	}
            	foreach ( $sphinx_results['matches'] as $id=>$val ) {
                    if ( in_array($id, $model_ids) ) {
                        $new_sphinx_results['matches'][] = $sphinx_results['matches'][$id];
                    }
                }
                break;
                
                
             case "searchPhotos":
                 $sql = "select stc.scene_id " .
                        " from lfpcms_scenes_to_categories stc " .
                        " where stc.category_id = " . $this->category;

                $cache_key = md5($sql);
                $scene_ids = null;
               /* if (getenv('ENVIRONMENT') != "DEVELOPMENT") {
                    $scene_ids = $memcache_obj->get($cache_key);
                }*/
                if ( !$scene_ids ) {
                    $photo_query_res = mysql_query($sql);
                    $scene_ids = array();
                    while($data = mysql_fetch_assoc($photo_query_res)) {
                		$scene_ids[] = $data['scene_id'];
                	}
            	}
            	foreach ( $sphinx_results['matches'] as $id=>$val ) {
                    if ( in_array($id, $scene_ids) ) {
                        $new_sphinx_results['matches'][] = $sphinx_results['matches'][$id];
                    }
                }

                break;   
        
        }
        if (!empty($new_sphinx_results["matches"]))
            $new_sphinx_results["total"] = count($new_sphinx_results["matches"]);
        else
            $new_sphinx_results["total"] = 0;
        return $new_sphinx_results;
    }
   

    private function createReturnArray( $type, $sphinx_results ) {
        $updates = array();
        
        
        if( !empty($sphinx_results) && isset($sphinx_results["matches"]) ) {
			
			$updates["info"]["totalRecords"] = $sphinx_results["total"];
			$denom = (isset($this->params['perPage']) && $this->params['perPage'] >= 1) ? $this->params['perPage'] : 35;
			$updates["info"]["totalPages"] = ceil($sphinx_results["total"]/(integer)$denom);
			$updates["info"]["currentPage"] = isset($this->params["page_num"]) && $this->params["page_num"] >= 1 ? $this->params["page_num"] : 1;
			
			

			switch ( $type ) {
			    
			    case "videos":
			    
    			foreach($sphinx_results["matches"] as $id=>$val) {
                    $tmp = array();
                    $tmp['weight'] = $val['weight'];
                    $tmp['title_id'] = $id;
                    $tmp['scene_id'] = $val['attrs']['attr_scene_id'];
                    $tmp['group_id'] = $val['attrs']['group_id'];
                    $tmp['default_group_id'] = $val['attrs']['default_group_id'];
                    $tmp['title_name'] = $val['attrs']['title_name'];
                    $tmp['scene_name'] = $val['attrs']['scene_name'];
                    $tmp['scene_type'] = 'video';
                    $tmp['scene_hd'] = $val['attrs']['scene_hd'];
                    $tmp['site_code'] = $val['attrs']['site_code'];
                    $tmp['in_favorites'] = 0;
                    $tmp['group_average_score'] = $val['attrs']['group_average_score'];
                    $tmp['group_lfp_id'] = $val['attrs']['group_lfp_id'];
                    $tmp['scene_lfpid'] = $val['attrs']['scene_lfpid'];
                    $tmp['media_location'] = $val['attrs']['media_location'];
                    $tmp['store_url'] = $val['attrs']['store_url'];
                    $tmp['group_published'] = $val['attrs']['group_published'];
                    $tmp['release_timestamp'] = $val['attrs']['release_timestamp'];
                    $tmp['scene_released'] = $val['attrs']['release_timestamp'];
                    $tmp['preview_images'] = array();
                    $tmp['scene_description'] = "";
                    $tmp['mobile_description'] = "";
                    $tmp['scene_performers'] = "";
                    $clips = Paysite::get_scene_clips($val['attrs']['attr_scene_id']);
    				if(!empty($clips) && is_array($clips)) {
    					foreach($clips as $clip) {
    						$tmp['preview_images'][] = $clip;
    					}
    			     }
    				$updates['data'][] = $tmp;
    			}
    			
			    break;
			    
			    case "dvds":
                
			     
    			foreach($sphinx_results["matches"] as $id=>$val) {
                    $tmp = array();
                    $tmp['weight'] = $val['weight'];
                    
                    
                    $tmp['average_score'] = "5";
                    
                    $tmp['group_id'] = $val['attrs']['default_group_id'];
                    $tmp['grp'] = $val['attrs']['default_group_id'];
                    $tmp['default_group_id'] = $val['attrs']['default_group_id'];
                   
                   
                    $tmp['title_name'] = $val['attrs']['title_name'];
                    $tmp['name'] = $val['attrs']['title_name'];
                    $tmp['group_description'] = $val['attrs']['title_description'];
                    
                    $tmp['in_favorites'] = 0;
                   
                   
                    $tmp['title_published'] =  $val['attrs']['title_published'];
                    $tmp['group_published'] = $val['attrs']['title_published'];  
                    $tmp['date'] = $val['attrs']['title_published'];
                    $tmp['published'] = $val['attrs']['title_published'];
                   
                    $tmp['id'] = $val['attrs']['attr_title_id'];
                    $tmp['title_id'] = $val['attrs']['attr_title_id'];
                   
    				$updates['data'][] = $tmp;
    			}
    			
    			
			    
			    break;
			    
			    case "photos":
			    
    			foreach($sphinx_results["matches"] as $id=>$val) {
                    $tmp = array();
                    $tmp['weight'] = $val['weight'];
                    $tmp['title_id'] = $id;
                    $tmp['title_name'] = $val['attrs']['title_name'];
                    $tmp['default_group_id'] = $val['attrs']['default_group_id'];
                    $tmp['scene_id'] = $val['attrs']['scene_id'];
                    $tmp['group_id'] = $val['attrs']['group_id'];
                    $tmp['scene_name'] = $val['attrs']['scene_name'];
                    $tmp['scene_type'] = 'photo';
                    $tmp['site_code'] = $val['attrs']['site_code'];
                    $tmp['in_favorites'] = 0;
                    $tmp['average_score'] = $val['attrs']['average_score'];
                    $tmp['scene_lfpid'] = $val['attrs']['scene_lfpid'];
                    $tmp['scene_hd'] = $val['attrs']['scene_hd'];
                    $tmp['group_lfp_id'] = "";
                    $tmp['store_url'] = "";
                    $tmp['group_published'] = "";
                    $tmp['scene_description'] = "";
                    $tmp['mobile_description'] = "";
                    $tmp['release_timestamp'] = $val['attrs']['release_timestamp'];
                    $tmp['media_location'] = $val['attrs']['media_location'];
                    $tmp['scene_released'] = $val['attrs']['release_timestamp'];
                    $updates['data'][] = $tmp;
				}
    			
			    break;
			    
			    case "models":
			    
			    foreach($sphinx_results["matches"] as $id=>$val) {
                    $tmp = array();
                    $tmp['weight'] = $val['weight'];
                    $tmp['in_favorites'] = 0;
                    $tmp['average_score'] = $val['attrs']['average_score'];
                    $tmp['performer_id'] = $val['attrs']['attr_performer_id'];
                    $tmp['performer_name'] = $val['attrs']['performer_name'];
                    
                    $tmp['scene_description'] = "";
                    $tmp['mobile_description'] = "";
                    $tmp['performer_timestamp'] = $val['attrs']['performer_timestamp'];
						
    				$updates['data'][] = $tmp;
    			}
    			
			    break;
			
			}
			
			
		} else {
		    $updates["info"]["totalRecords"] = 0;
		    $updates["info"]["matches"] = null;
		}
		return $updates;
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