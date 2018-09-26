<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Paysite
{
    public static function get_current_site($sitecode) {
        return 'huster'; // not sure if 'huster' or 'hustler.com' or an ID???
    }
    
    public static function get_content($type, $sort, $page_num, $perPage, $search, 
                $model_id, $site, $group, $category_id, $watched_now, 
                $member_id, $fave, $limit, $pgroup_id, $magazine_id, $true, $false, $check_trailer,
                $all_perf_status, $mobile_clips,
                $hdonly, $flagged) {
        $data = [];
        for ($x = 0; $x < $perPage; $x++) {
            $data[] = [
                // dvds
                'title_id'=> 'title_' . ($x + 1 + ($page_num * $perPage)),
                'title_name'=> 'My DVD#' . random_int(1, 1000),
                'group_id'=> random_int(1,4),
                'title_published'=> date('Y-m-d H:i:s', time()),
                'performers' => [
                    [
                        'performer_id' => random_int(1,400),
                        'performer_gender' => 'female',
                        'performer_name' => 'performer ' . random_int(1, 400),
                    ],
                    [
                        'performer_id' => random_int(1,400),
                        'performer_gender' => 'female',
                        'performer_name' => 'performer ' . random_int(1, 400),
                    ],                    
                ],
                // vids 
                'scene_id' => 'title_' . ($x + 1 + ($page_num * $perPage)),
                'scene_name' => 'My Scene#' . random_int(1, 1000),
                'media_location' => 'thevid.mp4',
                'scene_released' => date('Y-m-d H:i:s', time()),                
            ];
        }
        return [
            'data' => $data,
            'info' => [
                'totalRecords' => 10,
            ],
        ];
    }
    
    public static function truncate_at_word($str, $size, $bool) {
        return substr($str, 0, $size);
    } 
}


class Content
{
    public static function image_handler($cache_url, $cache_path, $media_location, $size_x, $size_y) {
        return '/images/placeholder/photo-placeholder.png';
        return '/images/logos/hustler.png';
    }
}
