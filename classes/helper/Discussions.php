<?php

namespace helper;

use core;

class Discussions {

  public static $tags = array();
  public static $discussions = array();
  public static $discussion = array();
  public static $errors = array();

	public static function submitDiscussion() {

    self::$errors['error'] = false;

	  // TITLE
    if(!isset($_POST['discuss_title']) || empty($_POST['discuss_title'])) {
      self::$errors['error'] = true;
      self::$errors['discuss_title'] = "true";
    }

	  // TEXT
    if(!isset($_POST['discuss_text']) || empty($_POST['discuss_text'])) {
      self::$errors['error'] = true;
      self::$errors['discuss_text'] = "true";
    }

	  // TAGS
    if(!isset($_POST['discuss_tags']) || empty($_POST['discuss_tags'])) {
      self::$errors['error'] = true;
      self::$errors['discuss_tags'] = "true";
    }

    // SUBMIT PASSED ERRORS
    if(!self::$errors['error']) {
      // INSERT INTO DB
      $discussID = core\DB::execute("INSERT INTO `discussions` SET 
                                `author_id` = ?, 
                                `created_at` = ?, 
                                `title` = ?, 
                                `text` = ? 
                              ",[
                                Account::user('id'),
                                time(),
                                $_POST['discuss_title'],
                                $_POST['discuss_text']
      ]);

      return $discussID;
    }
    else {
      return false;
    }

	}

  public static function getDiscussion($id = 0) {
	  return (empty(self::$discussion) && $id > 0) ? self::$discussion = core\DB::selectFirst("SELECT * FROM `discussions` WHERE `id` = ?", [$id]) : self::$discussion;
  }

  public static function getDiscussions() {
	  return (empty(self::$discussions)) ? self::$discussions = core\DB::select("SELECT * FROM `discussions` ORDER BY `created_at` DESC") : self::$discussions;
  }

  public static function getTags() {
	  return (empty(self::$tags)) ? self::$tags = core\DB::select("SELECT * FROM `discussions_tags` ORDER BY `tag_name` ASC") : self::$tags;
  }

  public static function tohtml($text,$advanced=FALSE,$charset='utf8'){
    //special chars
    $text  = htmlspecialchars($text, ENT_QUOTES,$charset);
    /**
     * This array contains the main static bbcode
     * @var array $basic_bbcode
     */
    $basic_bbcode = array(
        '[b]', '[/b]',
        '[i]', '[/i]',
        '[u]', '[/u]',
        '[s]','[/s]',
        '[ul]','[/ul]',
        '[li]', '[/li]',
        '[ol]', '[/ol]',
        '[center]', '[/center]',
        '[left]', '[/left]',
        '[right]', '[/right]',
    );
    /**
     * This array contains the main static bbcode's html
     * @var array $basic_html
     */
    $basic_html = array(
        '<b>', '</b>',
        '<i>', '</i>',
        '<u>', '</u>',
        '<s>', '</s>',
        '<ul>','</ul>',
        '<li>','</li>',
        '<ol>','</ol>',
        '<div style="text-align: center;">', '</div>',
        '<div style="text-align: left;">',   '</div>',
        '<div style="text-align: right;">',  '</div>',
    );
    /**
     *
     * Parses basic bbcode, used str_replace since seems to be the fastest
     */
    $text = str_replace($basic_bbcode, $basic_html, $text);
    //advanced BBCODE
    if ($advanced)
    {
      /**
       * This array contains the advanced static bbcode
       * @var array $advanced_bbcode
       */
      $advanced_bbcode = array(
          '#\[color=([a-zA-Z]*|\#?[0-9a-fA-F]{6})](.+)\[/color\]#Usi',
          '#\[size=([0-9][0-9]?)](.+)\[/size\]#Usi',
          '#\[quote](\r\n)?(.+?)\[/quote]#si',
          '#\[quote=(.*?)](\r\n)?(.+?)\[/quote]#si',
          '#\[url](.+)\[/url]#Usi',
          '#\[url=(.+)](.+)\[/url\]#Usi',
          '#\[email]([\w\.\-]+@[a-zA-Z0-9\-]+\.?[a-zA-Z0-9\-]*\.\w{1,4})\[/email]#Usi',
          '#\[email=([\w\.\-]+@[a-zA-Z0-9\-]+\.?[a-zA-Z0-9\-]*\.\w{1,4})](.+)\[/email]#Usi',
          '#\[img](.+)\[/img]#Usi',
          '#\[img=(.+)](.+)\[/img]#Usi',
          '#\[code](\r\n)?(.+?)(\r\n)?\[/code]#si',
          '#\[youtube]http://[a-z]{0,3}.youtube.com/watch\?v=([0-9a-zA-Z]{1,11})\[/youtube]#Usi',
          '#\[youtube]([0-9a-zA-Z]{1,11})\[/youtube]#Usi'
      );
      /**
       * This array contains the advanced static bbcode's html
       * @var array $advanced_html
       */
      $advanced_html = array(
          '<span style="color: $1">$2</span>',
          '<span style="font-size: $1px">$2</span>',
          "<div class=\"quote\"><span class=\"quoteby\">Disse:</span>\r\n$2</div>",
          "<div class=\"quote\"><span class=\"quoteby\">Disse <b>$1</b>:</span>\r\n$3</div>",
          '<a rel="nofollow" target="_blank" href="$1">$1</a>',
          '<a rel="nofollow" target="_blank" href="$1">$2</a>',
          '<a href="mailto: $1">$1</a>',
          '<a href="mailto: $1">$2</a>',
          '<img src="$1" alt="$1" />',
          '<img src="$1" alt="$2" />',
          '<div class="code">$2</div>',
          '<object type="application/x-shockwave-flash" style="width: 450px; height: 366px;" data="http://www.youtube.com/v/$1"><param name="movie" value="http://www.youtube.com/v/$1" /><param name="wmode" value="transparent" /></object>',
          '<object type="application/x-shockwave-flash" style="width: 450px; height: 366px;" data="http://www.youtube.com/v/$1"><param name="movie" value="http://www.youtube.com/v/$1" /><param name="wmode" value="transparent" /></object>'
      );
      $text = preg_replace($advanced_bbcode, $advanced_html,$text);
    }
    //before return convert line breaks to HTML
    return self::nl2br($text);
  }
  /**
   *
   * removes bbcode from text
   * @param string $text
   * @return string text cleaned
   */
  public static function remove($text)
  {
    return strip_tags(str_replace(array('[',']'), array('<','>'), $text));
  }

  public static function nl2br($var)
  {
    return str_replace(array('\\r\\n','\r\\n','r\\n','\r\n', '\n', '\r'), '<br />', nl2br($var));
  }
}