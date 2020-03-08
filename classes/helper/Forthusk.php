<?php

namespace helper;

use core;

class Forthusk {

  public static function topics($forumID) {

    $topics = core\DB::select("SELECT * FROM forum_topics AS a 
                                      INNER JOIN forum_posts AS b 
                                      ON a.topic_first_post_id = b.post_id 
                                      WHERE a.forum_id = ? 
                                      ORDER BY b.post_time DESC",[$forumID]);

    return $topics;
  }

}