<?php
//////////////////////////////////////////////////////////////
//  Censorship filtering
//
//  This very simple example of a Text Filter will parse
//  printed text, blacking out words perceived to be bad
//
//  The list of words is in the lang/xx/moodle.php
//
//////////////////////////////////////////////////////////////

/// This is the filtering class. It accepts the courseid and
/// options to be filtered (In HTML form).
class censor_filter extends moodle_text_filter {
    private function _canseecensor() {
        return is_siteadmin(); //TODO: add proper access control
    }
    function hash(){
        $cap = "mod/filter:censor";
        if (is_siteadmin()) {  //TODO: add proper access control
            $cap = "mod/filter:seecensor";
        }
        return $cap;
    }
    function filter($text){
        static $words;
        global $CFG;

        if (!isset($CFG->filter_censor_badwords)) {
            set_config( 'filter_censor_badwords','' );
        }

        if (empty($words)) {
            $words = array();
            if (empty($CFG->filter_censor_badwords)) {
                $badwords = explode(',',get_string('badwords', 'filter_censor'));
            }
            else {
                $badwords = explode(',', $CFG->filter_censor_badwords);
            }
            foreach ($badwords as $badword) {
                $badword = trim($badword);
                if($this->_canseecensor()){
                    $words[] = new filterobject($badword, '<span title="'.$badword.'">', '</span>',
                        false, false, $badword);
                } else {
                    $words[] = new filterobject($badword, '<span class="censoredtext" title="'.$badword.'">',
                        '</span>', false, false, str_pad('',strlen($badword),'*'));
                }
            }
        }
        return filter_phrases($text, $words);
    }
}


