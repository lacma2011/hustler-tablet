<?php
define('IPHONE', 'default'); // prepared for iPhone portrait, should be default
define('IPAD', 'ipad');
define('DROID', 'droid');
$tmp = strtolower($_SERVER['HTTP_USER_AGENT']);
if (strpos($tmp, 'ipad')) {
    $platform = IPAD;
} elseif (strpos($tmp, 'android')) {
    $platform = DROID;
} else {
    $platform = IPHONE;
}

$redirect = $_GET['clip'];

//if (TRUE === MOBILE_TEST) echo 'redirect to ' . $redirect;
if (!$redirect) {
    echo "Clip not found." . PHP_EOL;

} else {
    if (DROID === $platform) {
        html5_player($redirect);
    } else {
        header('Location: ' . $redirect);
    }
}

function html5_player($clip) {
    ?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
<video id="hustler_video" controls="controls" width="100%" poster="<?= $_GET['ss']?>">
  <source type="video/mp4" src="<?= $clip ?>" />
  Your browser doesn't support the HTML5 player! <br>
  <a href="<?= $clip ?>">Click to download clip</a>
</video>
<script>
    function callback () {
        document.getElementById('hustler_video').play();
    }
    window.addEventListener('load', callback, false);
</script>
</body>
</html>
    <?php
}

