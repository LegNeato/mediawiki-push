<?php

function prep_object($obj) {

    $cloned = clone $obj;

    switch( get_class($cloned) ) {

        case 'Article':
            _prep_article($cloned);
            break;

        case 'User':
            _prep_user($cloned);
            break;

        default:
            $cloned = NULL;
    }
    return $cloned;
}

function _filter( &$obj, $list=array() ) {
    foreach( $list as $filter_item ) {
        unset($obj->$filter_item);
    }
}

function _prep_article( &$obj ) {
    $data = array();
    $static = array(
        'mComment',
        'mTouched',
        'mUser',
        'mUserText',
    );

    foreach( $static as $varname ) {
        $data[$varname] = $obj->$varname;
    }

    return $data;
}

function _prep_user( &$obj ) {
    $filter = array(
        'mCacheVars',
        'mDataLoaded',
        'mEmailToken',
        'mFrom',
        'mHash',
        'mNewpassTime',
        'mNewpassword',
        'mSkin',
        'mToken',
    );
    _filter($obj, $filter);
}

?>
