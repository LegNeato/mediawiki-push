<?php

// Include our 3rd party AMQP library
include_once dirname(__FILE__) . '/../external_libs/php-amqplib/amqp.inc';

class AMQP extends Base {

    public function connect($host, $port, $vhost, $user, $pass) {
        $this->connection = new AMQPConnection($host, $port, $user, $pass);
        $this->channel = $this->connection->channel();
        $this->channel->access_request($vhost, false, false, true, true);
    }

    public function is_connected() {
        if( isset($this->connection) && $this->connection ) {
            return true;
        }
        return false;
    }

    public function prepare_message($raw, $type='json') {
        switch( strtolower($type) ) {
            default:
                $func = 'json_encode';
                $ct   = 'application/json';
        }

        return new AMQPMessage($func($raw),
                               array(
                                    'content_type' => $ct,
                               )
        );
    }

    public function publish($exchange, $routing_key, $message) {
        if( !$this->is_connected() ) {
            throw new MWException('Cannot publish AMQP message: not connected');
        }
        $this->channel->basic_publish($message, $exchange, $routing_key);
    }

    public function close() {
        if( isset($this->connection) && $this->connection ) {
            $this->channel->close();
            $this->connection->close();
        }
        unset($this->channel);
        unset($this->connection);
    }

    function __destruct() {
        $this->close();
    }

}


?>
