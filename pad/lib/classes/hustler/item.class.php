<?php

// TODO: re-do class names without underscores and use namespaces when servers are on 5.3


define('ITEMTYPE_VIDEO', 0);
define('ITEMTYPE_MODEL', 1);
define('ITEMTYPE_PIC', 2);

class Hustler_Item {

    private $type;

    function __construct($type) {
        $this->type = $type;
    }

    public function isVideo() {
        if ($this->type == ITEMTYPE_VIDEO) {
            return TRUE;
        }
        return FALSE;
    }
    
    public function isPic() {
        if ($this->type == ITEMTYPE_PIC) {
            return TRUE;
        }
        return FALSE;
    }

    public function isModel() {
        if ($this->type == ITEMTYPE_MODEL) {
            return TRUE;
        }
        return FALSE;
    }
}


?>