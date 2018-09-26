<?php


class Hustler_Item_Model extends Hustler_Item {
    private $data;

    function __construct($data) {
        parent::__construct(ITEMTYPE_MODEL);
        $this->data = $data;
//        $this->data['_output'] = array(
//            'meta' => $this->getMeta(),
//            'special' => $this->getSpecial(),
//        );
    }

    private function getMeta() {
        $meta = array();
        return $meta;
    }
    
    private function getSpecial() {
        return array(
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