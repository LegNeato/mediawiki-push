<?php

// -----------------------------------------------------------------------------
// Extension credits / metadata
// -----------------------------------------------------------------------------

$wgExtensionCredits['other'][] = array(
    'name'        => 'Push',
    'author'      => 'Christian Legnitto', 
    'url'         => 'http://christian.legnitto.com', 
    'description' => 'This extension allows integration with a message '.
                     'broker via AMQP or STOMP',
);

// -----------------------------------------------------------------------------
// General setup
// -----------------------------------------------------------------------------

// Register the classes to autoload
$wgAutoloadClasses['Push']     = dirname(__FILE__) . '/Push.class.php';
$wgAutoloadClasses['Base']     = dirname(__FILE__) . '/backends/Base.class.php';
$wgAutoloadClasses['Messages'] = dirname(__FILE__) . '/messages/All.class.php';

// -----------------------------------------------------------------------------
// Handle details of sending messages
// -----------------------------------------------------------------------------

function do_push($hook) {

    global $wgPushHost;
    global $wgPushPort;
    global $wgPushVhost;
    global $wgPushUser;
    global $wgPushPass;
    global $wgPushBackend;

    global $wgPushDefaultExchange;
    global $wgPushUseMessageEnvelope;

    // Get the args, but throw away out hook
    $args = func_get_args();
    array_shift($args);
        
    // Get the message and routing key ready
    $msgclass = $hook . 'Message';
    $msg = new $msgclass();
    $msg->set_args($args);
    $msg->prepare();

    // Bail if we aren't supposed to send the message based on the data
    if( empty($msg->data) ) {
        return TRUE;
    }
    
    // Create our push object
    $push = new Push($wgPushHost,
                     $wgPushPort,
                     $wgPushVhost,
                     $wgPushUser,
                     $wgPushPass,
                     $wgPushBackend
    );

    // Connect to the broker
    $push->connect();

    // Publish the message
    $push->publish($wgPushDefaultExchange,
                   $msg->routing_key,
                   $msg->data,
                   $wgPushUseMessageEnvelope);

    // Close the broker connection
    $push->close();

    // Tell MediaWiki everything worked
    return TRUE;
}

// -----------------------------------------------------------------------------
// Register for MediaWiki hooks
// -----------------------------------------------------------------------------

// Hooks we want to send messages for
$supported_hooks = array(
    'ArticleDeleteComplete',
    'ArticleUndelete',
    'ArticleRevisionUndeleted',
    'ArticleProtectComplete',
    'ArticleInsertComplete',
    'ArticleSaveComplete',
    'TitleMoveComplete',
    'ArticleRollbackComplete',
    'ArticleMergeComplete',
);

// Hook our 'do_push' function into MediaWiki
foreach( $supported_hooks as $hook_name ) {
    $wgHooks[$hook_name][] = array('do_push', $hook_name);
}

// -----------------------------------------------------------------------------
// Configuration validation
// -----------------------------------------------------------------------------

// Register a validation function that will be executed after MediaWiki is ready
$wgExtensionFunctions[] = 'validate_settings';
 
// Define the validation function
function validate_settings() {
    $needed = array(
        'wgPushBackend',
        'wgPushHost',
        'wgPushPort',
        'wgPushDefaultExchange',
    );

    foreach( $needed as $check ) {
        global $$check;
        if( empty($$check) ) {
            throw new MWException('$' . "$check must be set");
        }
    }

    // No error, return empty error string
    return "";

}

// -----------------------------------------------------------------------------
// Default configuration
// -----------------------------------------------------------------------------

$wgPushBackend            = "AMQP";
$wgPushHost               = "localhost";
$wgPushPort               = 5672;
$wgPushVhost              = "/";
$wgPushUser               = "guest";
$wgPushPass               = "guest";
$wgPushDefaultExchange    = "";
$wgPushUseSSL             = FALSE;

$wgPushIncludeFullObjects = FALSE;
$wgPushUseMessageEnvelope = TRUE;

$wgPushArticleLabel     = "page";
$wgPushRevisionLabel    = "revision";

?>
