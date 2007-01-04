<?php
  
  /* This is the global search shortcut block - a single query can be entered, and
     the user will be redirected to the query page where they can enter more
     advanced queries, and view the results of their search. When searching from
     this block, the broadest possible selection of documents is searched.
     
     Author:  Michael Champanis (mchampan)
     Date:    2006 06 25
  
     Todo: make strings -> get_string()  
  */
     
  class block_search extends block_base {
    
    function init() {
      $this->title = "Global Search"; //get_string()
      $this->version = 2006062500;
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
        return '';
      }

      //cache block contents
      if ($this->content !== NULL) {
        return $this->content;
      } //if
      
      $this->content = new stdClass;
      
      //lazy check for the moment
      if (check_php_version("5.0.0")) {        
        //fetch values if defined in admin, otherwise use defaults
        $label  = (isset($CFG->block_search_text)) ? $CFG->block_search_text : "Search Moodle";
        $button = (isset($CFG->block_search_button)) ? $CFG->block_search_button : "Go";
        
        //basic search form
        $this->content->text =
            '<form name="query" method="get" action="'. $CFG->wwwroot .'/search/query.php"><div>'
          . '<label for="block_search_q">'. $label .'</label>'
          . '<input id="block_search_q" type="text" name="query_string" length="50" />'
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
      
  } //block_search

?>