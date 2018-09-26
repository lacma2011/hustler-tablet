<?php

function getDomain() {
        $host = $_SERVER["HTTP_HOST"];
        $x = strrpos($host, '.'); // get highlest level domain, most likely '.com'
        $d = substr($host, 0, $x);
        $top = substr($host, $x);
        if (strstr($d, '.')) {
            // if there's subdomains, so more than just 'hustlerhd.com'
            $x = strrpos($d, '.'); // get next level domain, the site we want
            $d = substr($d, $x + 1);
        }
        return $d . $top;
}

function url_double_slash($url) {
    if (substr($url, -2) == '//') {
        return substr($url, 0, -1);
    } else {
        return $url;
    }
}

?>