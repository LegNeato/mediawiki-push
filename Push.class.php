<?php


# This class exposses a backend-independent interface for sending messages
class Push {

    // Set variables and initialize the backend
    function __construct($host, $port, $vhost, $user, $pass, $backend) {
        $this->host  = $host;
        $this->port  = $port;
        $this->vhost = $vhost;
        $this->user  = $user;
        $this->pass  = $pass;

        # Set up the backend
        include_once dirname(__FILE__) . "/backends/${backend}.class.php";
        $this->backend = new $backend();
    }

    // Tell the backend to connect
    public function connect() {
        if( $this->backend->is_connected() ) {
            return;
        }
        $this->backend->connect(
            $this->host,
            $this->port,
            $this->vhost,
            $this->user,
            $this->pass
        );
    }

    // Publish a message
    public function publish($exchange, $routing_key, $raw_msg, $envelope) {

        if( $envelope ) {
            $raw_msg = array(
                '_meta' => array(
                    'exchange'    => $exchange,
                    'routing_key' => $routing_key,
                    'sent'        => date('c'),
                    'serializer'  => 'json',
                ),
                'payload' => $raw_msg,
            );
        }

        $msg = $this->backend->prepare_message($raw_msg);

        $this->backend->publish($exchange, $routing_key, $msg);
    }

    // Tell the backend to disconnect
    public function close() {
        if( !$this->backend->is_connected() ) {
            return;
        }
        $this->backend->close();
    }

}


?>
