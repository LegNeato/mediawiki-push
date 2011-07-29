<?php


class Stub {

    // Support empty stub objects
    function __construct() {
    }

    function _set_defaults($list=array()) {
        foreach( $list as $field ) {
            $this->$field = null;
        }
    }

    function encode($encoding='json') {
        $func = "to_$encoding";
        return $this->$func();
    }

    function to_json() {
        return json_encode($this->data);
    }

}

class UserStub extends Stub {

    function __construct() {
        $args = func_get_args();
        parent::__construct($args);

        $this->_set_defaults(array(
            'id',
            'name',
            'real_name',
            'email',
            'ref',
            'is_anonymous',
        ));
    }
}

class RegisteredUserStub extends UserStub {

    function __construct() {
        $args = func_get_args();
        parent::__construct($args);

        // Get the internal mediawiki user object
        $inUser = $args[0];

        // Set our stub values
        $this->id           = $inUser->mId;
        $this->name         = $inUser->mName;
        $this->real_name    = $inUser->mRealName;
        $this->email        = $inUser->mEmail;
        $this->ref          = 'TODO:index.php/User:' . $this->name;
        $this->is_anonymous = false;
    }
}

class AnonymousUserStub extends UserStub {

    function __construct() {
        $args = func_get_args();
        parent::__construct($args);

        // Set our stub values
        $this->ip_address   = $args[0];
        $this->is_anonymous = TRUE;
    }
}

class UserStubFactory {

    public static function create($data) {
        
        // Support anonymous users
        if( !$data || is_string($data) || !$data->mId ) {
            if( !is_string($data) ) {
                $data = $data->mName;
            }
            // Return an anonymous user
            return new AnonymousUserStub($data);
        }

        // Otherwise return a registered user
        return new RegisteredUserStub($data);
    }

}


class ArticleStub extends Stub {

    function __construct() {
        $args = func_get_args();
        parent::__construct($args);

        $this->_set_defaults(array(
            'id',
            'content',
            'state',
            'comment',
            'is_minor',
            'ref',
        ));
    }

}

class GeneralArticleStub extends ArticleStub {

    function __construct($inArticle, $inUser=null) {
        parent::__construct($inArticle, $inUser);

        // Store the author if given
        // (we don't parse this out of the article because it doesn't have all
        // the info we need and we don't want to have a partial author object)
        if( $inUser ) {
            $this->author = UserStubFactory::create($inUser);
        }else{
            if( $inArticle->mUser ) {
                // Don't have an author object, salvage whatever info we can
                // from the article
                $generated = new RegisteredUserStub();
                $generated->is_anonymous = false;
                $generated->id           = $inArticle->mUser;
                $generated->name         = $inArticle->mUserText;
                $this->author = $generated;
            }else {
                // Anonymous user
                $this->author = AnonymousUserStub($inArticle->mUserText);
            }
        }

        if( $inArticle ) {
            $this->id       = $inArticle->mId || $inArticle->mTitle->mArticleID;
            $this->content  = array(
                'html' => $inArticle->mText,
                'wiki' => $inArticle->mContent,
            );
            $this->state    = "new";
            $this->title = array(
                'text'     => $inArticle->mTitle->mTextform,
                'url'      => $inArticle->mTitle->mUrlform,
                'database' => $inArticle->mTitle->mDbkeyform,
            );
            $this->comment  = $inArticle->mComment;
            $this->is_minor = $inArticle->mMinorEdit;
            $this->ref      = "TODO://";
            $this->raw = $inArticle;
        }
    }
}

class ArticleStubFactory {

    public static function create($inArticle, $inUser=null) {
        return new GeneralArticleStub($inArticle,
                                      $inUser);
    }

}

class RevisionStub extends Stub {


}

?>
