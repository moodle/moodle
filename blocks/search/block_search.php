<?php
  
  /* This is the global search shortcut block - a single query can be entered, and
  * the user will be redirected to the query page where they can enter more
  *  advanced queries, and view the results of their search. When searching from
  *  this block, the broadest possible selection of documents is searched.
  *  
  *
  *  Todo: make strings -> get_string()  
  * 
  * @package search
  * @subpackage search block
  * @author: Michael Champanis (mchampan), reengineered by Valery Fremaux 
  * @date: 2006 06 25
  */
     
  class block_search extends block_base {
    
    function init() {
      $this->title = get_string('blockname', 'block_search');
      $this->cron = 1;
      $this->version = 2008031500;
    } //init  
    
    // only one instance of this block is required
    function instance_allow_multiple() {
      return false;
    } //instance_allow_multiple
    
    // label and button values can be set in admin
    function has_config() {
      return true;
    } //has_config
      
    function get_content() {
      global $CFG;

      if (empty($CFG->enableglobalsearch)) {
        return get_string('disabledsearch', 'search');
      }

      //cache block contents
      if ($this->content !== NULL) {
        return $this->content;
      } //if
      
      $this->content = new stdClass;
      
      //lazy check for the moment
      if (check_php_version("5.0.0")) {        
        //fetch values if defined in admin, otherwise use defaults
        $label  = (!empty($CFG->block_search_text)) ? $CFG->block_search_text : get_string('searchmoodle', 'block_search');
        $button = (!empty($CFG->block_search_button)) ? $CFG->block_search_button : get_string('go', 'block_search');
        
        //basic search form
        $this->content->text =
            '<form id="searchquery" method="get" action="'. $CFG->wwwroot .'/search/query.php"><div>'
          . '<label for="block_search_q">'. $label .'</label>'
          . '<input id="block_search_q" type="text" name="query_string" />'
          . '<input type="submit" value="'.$button.'" />'
          . '</div></form>';
      } else {
        $this->content->text = "Sorry folks, PHP 5 is needed for the new search module.";
      } //else
        
      //no footer, thanks
      $this->content->footer = '';
      
      return $this->content;      
    } //get_content
    
    function specialisation() {
      //empty!
    } //specialisation
    
    /**
    * wraps up to search engine cron
    *
    */
    function cron(){
        global $CFG;
        
        include($CFG->dirroot.'/search/cron.php');
    }
      
  } //block_search

?>
