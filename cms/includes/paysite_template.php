<?php


// This script mocks data for API. And other random functions that app may need.
//

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
                'title_id'=> $x + 1 + ($page_num * $perPage),
                'title_name'=> 'My Video #' . random_int(1, 1000),
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

                // photos
                'release_timestamp' => date('Y-m-d H:i:s', time()),
                'scene_description' => 'A description of the scene... blah blah blah',
                'average_score' => 4,
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
    
    public static function get_content_scene ($id) {
        return [
                // vids 
                'scene_id' => $id,
                'title_name' => 'Just a Title name',
                'scene_name' => 'Just a Scene name',
                'media_location' => 'thevid.mp4',
                'scene_released' => date('Y-m-d H:i:s', time()),
                'scene_description' => 'A description of the scene... blah blah blah',
                'scene_type' => 4,
                'average_score' => 4,
                'in_favorites' => 0,
            ];        
    }
    
    public static function get_group_data($dvd_id, $member_id) {
        return array_merge([
            'title_id'=> $dvd_id,
            'group_id' => random_int(1, 1000),
            'title_name'=> 'My DVD#' . random_int(1, 1000),
            'group_id'=> random_int(1,4),
            'title_published'=> date('Y-m-d H:i:s', time()),
            
        ], self::get_content_scene(random_int(1, 1000)));
    }
}


class Content
{
    public static function image_handler($cache_url, $cache_path, $media_location, $size_x, $size_y) {
        return '/images/placeholder/photo-placeholder.png';
        return '/images/logos/hustler.png';
    }
}

if (!function_exists('random_int')) {
    function random_int($min, $max) {
        if (!function_exists('mcrypt_create_iv')) {
            trigger_error(
                'mcrypt must be loaded for random_int to work', 
                E_USER_WARNING
            );
            return null;
        }
        
        if (!is_int($min) || !is_int($max)) {
            trigger_error('$min and $max must be integer values', E_USER_NOTICE);
            $min = (int)$min;
            $max = (int)$max;
        }
        
        if ($min > $max) {
            trigger_error('$max can\'t be lesser than $min', E_USER_WARNING);
            return null;
        }
        
        $range = $counter = $max - $min;
        $bits = 1;
        
        while ($counter >>= 1) {
            ++$bits;
        }
        
        $bytes = (int)max(ceil($bits/8), 1);
        $bitmask = pow(2, $bits) - 1;

        if ($bitmask >= PHP_INT_MAX) {
            $bitmask = PHP_INT_MAX;
        }

        do {
            $result = hexdec(
                bin2hex(
                    mcrypt_create_iv($bytes, MCRYPT_DEV_URANDOM)
                )
            ) & $bitmask;
        } while ($result > $range);

        return $result + $min;
    }
}
