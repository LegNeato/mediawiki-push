<?php

class Base {

    public function connect() {
        $this->_not_implemented(__FUNCTION__);
    }

    public function is_connected() {
        $this->_not_implemented(__FUNCTION__);
    }

    public function prepare_message($raw=false) {
        $this->_not_implemented(__FUNCTION__);
    }

    public function publish() {
        $this->_not_implemented(__FUNCTION__);
    }

    public function close() {
        $this->_not_implemented(__FUNCTION__);
    }

    private function _not_implemented($what) {
        throw new MWException('Selected backend' .
                              ' "' . get_class($this) . '" ' .
                              'does not implement ' . $what . '()');
    }

}

?>
