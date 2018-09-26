<?php


class Hustler_Item_Pic extends Hustler_Item {

    private $data;

    function __construct($data) {
        parent::__construct(ITEMTYPE_PIC);
        $this->data = $data;
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