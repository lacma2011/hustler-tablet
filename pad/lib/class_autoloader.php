<?
function h_autoload($class) {
    $arr = explode('_', $class);
//print_r($arr);
    
    $path = MOBILE_LIB . '../classes/';
    $classfile = '';
    
    $last = count($arr) - 1;
    $interface = FALSE;
    foreach ($arr as $k=>$a) {
        if ($k == 0) {
            if (substr($a, 0, 1) == 'i') {
                $name = strtolower(substr($a, 1));
                $interface = TRUE;
                $path .= $name . '/interfaces/';
            } else {
                $path .= strtolower($a) . '/';
            }
            $name = '';
        } else {
            $name = strtolower($a);
            if ($k != $last) {
                $path .= strtolower($a) . '/';
            }
        }
        if ($name != '') {
            $classfile = $name . '.' . $classfile;
        }
        
    }

    $classfile = substr($classfile, 0, strlen($classfile) - 1);
    if ($interface) {
        $classfile .= '.interface.php';
    } else {
        $classfile .= '.class.php';
    }
//echo PHP_EOL .PHP_EOL . "path: $path" . PHP_EOL ."classfile: $classfile";
//echo PHP_EOL .PHP_EOL . $path . $classfile;
    include($path . $classfile);
}

spl_autoload_register('h_autoload'); // TODO: 5.3 uses anonymous functions


if (1==0) {
    // test run
    error_reporting(E_ALL);
    define('MOBILE_LIB', '/var/www/hustler-members/mobile/includes/classes/');
    $a = new Hustler_Contentbasic_Simple('barely-legal', TRUE);
    $a = new Hustler_Contentbasic_Performer('barely-legal', TRUE);
    $a = new Hustler_Contentbasic_Mag('barely-legal', TRUE);
    $a = new Hustler_Mobile_Pagebar();
    exit;
}

?>