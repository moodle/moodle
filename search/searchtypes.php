<?php

/** 
* Global Search Engine for Moodle
*
* @package search
* @category core
* @subpackage search_engine
* @author Michael Champanis (mchampan) [cynnical@gmail.com], Valery Fremaux [valery.fremaux@club-internet.fr] > 1.8
* @date 2008/03/31
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
*
* Searcheable types
* to disable a type, just comment the two declaration lines for that type
*
*/

//document types that can be searched
//define('SEARCH_TYPE_NONE', 'none');
define('SEARCH_TYPE_WIKI', 'wiki');
define('SEARCH_TYPE_FORUM', 'forum');
define('SEARCH_TYPE_GLOSSARY', 'glossary');
define('SEARCH_TYPE_RESOURCE', 'resource');
define('SEARCH_TYPE_DATA', 'data');
define('SEARCH_TYPE_CHAT', 'chat');
define('SEARCH_TYPE_LESSON', 'lesson');
define('SEARCH_TYPE_ASSIGNMENT', 'assignment');
define('SEARCH_TYPE_LABEL', 'label');

define('SEARCH_EXTRAS', 'user');
define('SEARCH_TYPE_USER', 'user');
define('PATH_FOR_SEARCH_TYPE_USER', 'user');

?>