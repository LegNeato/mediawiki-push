<?php

// -----------------------------------------------------------------------------
// Article message base classes
// -----------------------------------------------------------------------------

class ArticleMessage extends BaseMessage {
    function __construct() {
        parent::__construct();
        global $wgPushArticleLabel;
        $this->routing_parts[] = $wgPushArticleLabel;
    }
}

class ArticleRevisionMessage extends ArticleMessage {
    function __construct() {
        parent::__construct();
        global $wgPushRevisionLabel;
        $this->routing_parts[] = $wgPushRevisionLabel;
    }
}

// -----------------------------------------------------------------------------
// Article message classes
// -----------------------------------------------------------------------------

class ArticleDeleteCompleteMessage extends ArticleMessage {
    function __construct() {
        parent::__construct();
        $this->routing_parts[] = "deleted";
    }

    function prepare_data() {
        parent::prepare_data();
        global $wgPushArticleLabel;
        $tmp = ArticleStub($this->args[0], $this->args[1]);
        print "\nHERE\n";
        print json_encode($tmp);
        $this->data = array(
            $wgPushArticleLabel => $this->args[0],
            'who'               => $this->args[1],
            'reason'            => $this->args[2],
            'id'                => $this->args[3],
        );
    }

}

class ArticleUndeleteMessage extends ArticleMessage {
    function __construct() {
        parent::__construct();
        $this->routing_parts[] = "restored";
    }
}

class ArticleRevisionUndeletedMessage extends ArticleRevisionMessage {
    function __construct() {
        parent::__construct();
        $this->routing_parts[] = "restored";
    }
}

class ArticleProtectCompleteMessage extends ArticleMessage {
    function __construct() {
        parent::__construct();
        $this->routing_parts[] = "protected";
    }
}

class ArticleInsertCompleteMessage extends ArticleMessage {
    function __construct() {
        parent::__construct();
        $this->routing_parts[] = "created";
    }

    function prepare_data() {
        parent::prepare_data();
        global $wgPushArticleLabel;

        $inArticle = $this->args[0];
        $inUser    = $this->args[1];

        $this->data = array(
            $wgPushArticleLabel => ArticleStubFactory::create($inArticle,
                                                              $inUser),
            'who'               => UserStubFactory::create($inUser),
        );
    }
}

class ArticleSaveCompleteMessage extends ArticleMessage {
    function __construct() {
        parent::__construct();
        // Note that before sending we will check if $revision is 
        // NULL, so we will only send this when something actually changes
        $this->routing_parts[] = "changed";
    }
}

class TitleMoveCompleteMessage extends ArticleMessage {
    function __construct() {
        parent::__construct();
        $this->routing_parts[] = "moved";
    }
}

class ArticleRollbackCompleteMessage extends ArticleMessage {
    function __construct() {
        parent::__construct();
        $this->routing_parts[] = "reverted";
    }
}

class ArticleMergeCompleteMessage extends ArticleMessage {
    function __construct() {
        parent::__construct();
        $this->routing_parts[] = "merged";
    }
}

?>
