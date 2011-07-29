<?php

// -----------------------------------------------------------------------------
// Generic message base classes
// -----------------------------------------------------------------------------

class BaseMessage {
    function __construct() {
        global $wgPushGlobalMessagePrefix;

        $this->routing_parts = array();

        // Support a global routing key prefix (like "wiki")
        if( !empty($wgPushGlobalMessagePrefix) ) {
            $this->routing_parts[] = $wgPushGlobalMessagePrefix;
        }
    }

    function set_args($args=array()) {
        $this->args = $args;
    }

    function prepare_data() {
        // Stub
        $this->data = "";
    }

    function prepare_routing_key() {
        $this->routing_key = @implode('.', $this->routing_parts);
    }

    function prepare() {
        $this->prepare_data();
        $this->prepare_routing_key();
    }
}

?>
