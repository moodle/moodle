<?PHP

class hotpot_xml_quiz_template extends hotpot_xml_template_default {
    // left and right items for JMatch
    var $l_items = array();
    var $r_items = array();

    // constructor function for this class
    function hotpot_xml_quiz_template(&$parent) {

        $this->parent = &$parent;

        $get_js = optional_param('js', false);
        $get_css = optional_param('css', false);

        if (!empty($get_css)) {
            // set $this->css
            $this->v6_expand_StyleSheet();

        } else if (!empty($get_js)) {
            // set $this->js
            $this->read_template($this->parent->draganddrop.$this->parent->quiztype.'6.js_', 'js');

        } else {
            // set $this->html
            $this->read_template($this->parent->draganddrop.$this->parent->quiztype.'6.ht_', 'html');
        }

        // expand special strings, if any
        $pattern = '';
        switch ($this->parent->quiztype) {
            case 'jcloze':
                $pattern = '/\[(PreloadImageList)\]/';
                break;
            case 'jcross':
                $pattern = '/\[(PreloadImageList|ShowHideClueList)\]/';
                break;
            case 'jmatch':
                $pattern = '/\[(PreloadImageList|QsToShow|FixedArray|DragArray)\]/';
                break;
            case 'jmix':
                $pattern = '/\[(PreloadImageList|SegmentArray|AnswerArray)\]/';
                break;
            case 'jquiz':
                $pattern = '/\[(PreloadImageList|QsToShow)\]/';
                break;
        }
        if (!empty($pattern)) {
            $this->expand_strings('html', $pattern);
        }
        // fix doctype (convert short dtd to long dtd)
        $this->html = preg_replace(
            '/<!DOCTYPE[^>]*>/',
            '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
            $this->html, 1
        );
    }

    // captions and messages

    function v6_expand_AlsoCorrect() {
        return $this->parent->xml_value('hotpot-config-file,'.$this->parent->quiztype.',also-correct');
    }
    function v6_expand_CapitalizeFirst() {
        return $this->bool_value('hotpot-config-file,'.$this->parent->quiztype.',capitalize-first-letter');
    }
    function v6_expand_CheckCaption() {
        return $this->parent->xml_value('hotpot-config-file,global,check-caption');
    }
    function v6_expand_CorrectIndicator() {
        return $this->js_value('hotpot-config-file,global,correct-indicator');
    }
    function v6_expand_Back() {
        return $this->int_value('hotpot-config-file,global,include-back');
    }
    function v6_expand_BackCaption() {
        return str_replace('<=', '&lt;=', $this->parent->xml_value('hotpot-config-file,global,back-caption'));
    }
    function v6_expand_ClickToAdd() {
        return $this->parent->xml_value('hotpot-config-file,'.$this->parent->quiztype.',click-to-add');
    }
    function v6_expand_ClueCaption() {
        return $this->parent->xml_value('hotpot-config-file,global,clue-caption');
    }
    function v6_expand_Clues() {
        return $this->int_value('hotpot-config-file,'.$this->parent->quiztype.',include-clues');
    }
    function v6_expand_Contents() {
        return $this->int_value('hotpot-config-file,global,include-contents');
    }
    function v6_expand_ContentsCaption() {
        return $this->parent->xml_value('hotpot-config-file,global,contents-caption');
    }
    function v6_expand_GuessCorrect() {
        return $this->js_value('hotpot-config-file,'.$this->parent->quiztype.',guess-correct');
    }
    function v6_expand_GuessIncorrect() {
        return $this->js_value('hotpot-config-file,'.$this->parent->quiztype.',guess-incorrect');
    }
    function v6_expand_Hint() {
        return $this->int_value('hotpot-config-file,'.$this->parent->quiztype.',include-hint');
    }
    function v6_expand_HintCaption() {
        return $this->parent->xml_value('hotpot-config-file,global,hint-caption');
    }
    function v6_expand_IncorrectIndicator() {
        return $this->js_value('hotpot-config-file,global,incorrect-indicator');
    }
    function v6_expand_LastQCaption() {
        $caption = $this->parent->xml_value('hotpot-config-file,global,last-q-caption');
        return ($caption=='<=' ? '&lt;=' : $caption);
    }
    function v6_expand_NextCorrect() {
        $value = $this->js_value('hotpot-config-file,'.$this->parent->quiztype.',next-correct-part');
        if (empty($value)) { // jquiz
            $value = $this->js_value('hotpot-config-file,'.$this->parent->quiztype.',next-correct-letter');
        }
        return $value;
    }
    function v6_expand_NextEx() {
        return $this->int_value('hotpot-config-file,global,include-next-ex');
    }
    function v6_expand_NextExCaption() {
        return str_replace('=>', '=&gt;', $this->parent->xml_value('hotpot-config-file,global,next-ex-caption'));
    }
    function v6_expand_NextQCaption() {
        return $this->parent->xml_value('hotpot-config-file,global,next-q-caption');
    }
    function v6_expand_OKCaption() {
        return $this->parent->xml_value('hotpot-config-file,global,ok-caption');
    }
    function v6_expand_Restart() {
        return $this->int_value('hotpot-config-file,'.$this->parent->quiztype.',include-restart');
    }
    function v6_expand_RestartCaption() {
        return $this->parent->xml_value('hotpot-config-file,global,restart-caption');
    }
    function v6_expand_ShowAllQuestionsCaption() {
        return $this->js_value('hotpot-config-file,global,show-all-questions-caption');
    }
    function v6_expand_ShowOneByOneCaption() {
        return $this->js_value('hotpot-config-file,global,show-one-by-one-caption');
    }
    function v6_expand_TheseAnswersToo() {
        return $this->js_value('hotpot-config-file,'.$this->parent->quiztype.',also-correct');
    }
    function v6_expand_ThisMuch() {
        return $this->js_value('hotpot-config-file,'.$this->parent->quiztype.',this-much-correct');
    }
    function v6_expand_Undo() {
        return $this->int_value('hotpot-config-file,'.$this->parent->quiztype.',include-undo');
    }
    function v6_expand_UndoCaption() {
        return $this->parent->xml_value('hotpot-config-file,global,undo-caption');
    }
    function v6_expand_YourScoreIs() {
        return $this->js_value('hotpot-config-file,global,your-score-is');
    }

    // reading

    function v6_expand_Reading() {
        return $this->int_value('data,reading,include-reading');
    }
    function v6_expand_ReadingText() {
        $title = $this->v6_expand_ReadingTitle();
        $value = $this->parent->xml_value('data,reading,reading-text');
        $value = empty($value) ? '' : ('<div class="ReadingText">'.$value.'</div>');
        return $title.$value;
    }
    function v6_expand_ReadingTitle() {
        $value = $this->parent->xml_value('data,reading,reading-title');
        return empty($value) ? '' : ('<h3 class="ExerciseSubtitle">'.$value.'</h3>');
    }

    // timer

    function v6_expand_Timer() {
        return $this->int_value('data,timer,include-timer');
    }
    function v6_expand_JSTimer() {
        return $this->read_template('hp6timer.js_');
    }
    function v6_expand_Seconds() {
        return $this->parent->xml_value('data,timer,seconds');
    }

    // send results

    function v6_expand_SendResults() {
        return $this->parent->xml_value('hotpot-config-file,'.$this->parent->quiztype.',send-email');
    }
    function v6_expand_JSSendResults() {
        return $this->read_template('hp6sendresults.js_');
    }
    function v6_expand_FormMailURL() {
        return $this->parent->xml_value('hotpot-config-file,global,formmail-url');
    }
    function v6_expand_EMail() {
        return $this->parent->xml_value('hotpot-config-file,global,email');
    }
    function v6_expand_NamePlease() {
        return $this->js_value('hotpot-config-file,global,name-please');
    }

    // preload images

    function v6_expand_PreloadImages() {
        $value = $this->v6_expand_PreloadImageList();
        return empty($value) ? false : true;
    }
    function v6_expand_PreloadImageList() {

        // check it has not been set already
        if (!isset($this->PreloadImageList)) {

            // the list of image urls
            $list = array();

            // extract <img> tags
            $img_tag = htmlspecialchars('|&#x003C;img.*?src="(.*?)".*?&#x003E;|is');
            if (preg_match_all($img_tag, $this->parent->source, $matches)) {
                $list = $matches[1];

                // remove duplicates
                $list = array_unique($list);
            }

            // convert to comma delimited string
            $this->PreloadImageList = empty($list) ? '' : "'".implode("','", $list)."'";
        }
        return $this->PreloadImageList;
    }

    // html files (all quiz types)

    function v6_expand_PlainTitle() {
        return $this->parent->xml_value('data,title');
    }
    function v6_expand_ExerciseSubtitle() {
        return $this->parent->xml_value('hotpot-config-file,'.$this->parent->quiztype.',exercise-subtitle');
    }
    function v6_expand_Instructions() {
        return $this->parent->xml_value('hotpot-config-file,'.$this->parent->quiztype.',instructions');
    }
    function v6_expand_DublinCoreMetadata() {
        $dc = '<link rel="schema.DC" href="'.$this->parent->xml_value('', "['rdf:RDF'][0]['@']['xmlns:dc']").'" />'."\n";
        if (is_string($this->parent->xml_value('rdf:RDF,rdf:Description'))) {
            // do nothing (there is no more dc info)
        } else {
            $dc .= '<meta name="DC:Creator" content="'.$this->parent->xml_value('rdf:RDF,rdf:Description,dc:creator').'" />'."\n";
            $dc .= '<meta name="DC:Title" content="'.strip_tags($this->parent->xml_value('rdf:RDF,rdf:Description,dc:title')).'" />'."\n";
        }
        return $dc;
    }
    function v6_expand_FullVersionInfo() {
        global $CFG;
        require_once($CFG->hotpotroot.DIRECTORY_SEPARATOR.'version.php'); // set $module
        return $this->parent->xml_value('version').'.x (Moodle '.$CFG->release.', hotpot-module '.$this->parent->obj_value($module, 'release').')';
    }
    function v6_expand_HeaderCode() {
        return $this->parent->xml_value('hotpot-config-file,global,header-code');
    }
    function v6_expand_StyleSheet() {
        $this->read_template('hp6.cs_', 'css');
        $this->css = hotpot_convert_stylesheets_urls($this->parent->get_baseurl(), $this->parent->reference, $this->css);
        return $this->css;
    }

    // stylesheet (hp6.cs_)

    function v6_expand_PageBGColor() {
        return $this->parent->xml_value('hotpot-config-file,global,page-bg-color');
    }
    function v6_expand_GraphicURL() {
        return $this->parent->xml_value('hotpot-config-file,global,graphic-url');
    }
    function v6_expand_ExBGColor() {
        return $this->parent->xml_value('hotpot-config-file,global,ex-bg-color');
    }

    function v6_expand_FontFace() {
        return $this->parent->xml_value('hotpot-config-file,global,font-face');
    }
    function v6_expand_FontSize() {
        $value = $this->parent->xml_value('hotpot-config-file,global,font-size');
        return (empty($value) ? 'small' : $value);
    }
    function v6_expand_TextColor() {
        return $this->parent->xml_value('hotpot-config-file,global,text-color');
    }
    function v6_expand_TitleColor() {
        return $this->parent->xml_value('hotpot-config-file,global,title-color');
    }
    function v6_expand_LinkColor() {
        return $this->parent->xml_value('hotpot-config-file,global,link-color');
    }
    function v6_expand_VLinkColor() {
        return $this->parent->xml_value('hotpot-config-file,global,vlink-color');
    }

    function v6_expand_NavTextColor() {
        return $this->parent->xml_value('hotpot-config-file,global,page-bg-color');
    }
    function v6_expand_NavBarColor() {
        return $this->parent->xml_value('hotpot-config-file,global,nav-bar-color');
    }
    function v6_expand_NavLightColor() {
        $color = $this->parent->xml_value('hotpot-config-file,global,nav-bar-color');
        return $this->get_halfway_color($color, '#ffffff');
    }
    function v6_expand_NavShadeColor() {
        $color = $this->parent->xml_value('hotpot-config-file,global,nav-bar-color');
        return $this->get_halfway_color($color, '#000000');
    }

    function v6_expand_FuncLightColor() { // top-left of buttons
        $color = $this->parent->xml_value('hotpot-config-file,global,ex-bg-color');
        return $this->get_halfway_color($color, '#ffffff');
    }
    function v6_expand_FuncShadeColor() { // bottom right of buttons
        $color = $this->parent->xml_value('hotpot-config-file,global,ex-bg-color');
        return $this->get_halfway_color($color, '#000000');
    }

    // navigation buttons

    function v6_expand_NavButtons() {
        $back = $this->v6_expand_Back();
        $next_ex = $this->v6_expand_NextEx();
        $contents = $this->v6_expand_Contents();
        return (empty($back) && empty($next_ex) && empty($contents) ? false : true);
    }
    function v6_expand_NavBarJS() {
        return $this->v6_expand_NavButtons();
    }

    // switch off scorm
    function v6_expand_Scorm12() {
        return false;
    }

    // js files (all quiz types)

    function v6_expand_JSBrowserCheck() {
        return $this->read_template('hp6browsercheck.js_');
    }
    function v6_expand_JSButtons() {
        return $this->read_template('hp6buttons.js_');
    }
    function v6_expand_JSCard() {
        return $this->read_template('hp6card.js_');
    }
    function v6_expand_JSCheckShortAnswer() {
        return $this->read_template('hp6checkshortanswer.js_');
    }
    function v6_expand_JSHotPotNet() {
        return $this->read_template('hp6hotpotnet.js_');
    }
    function v6_expand_JSShowMessage() {
        return $this->read_template('hp6showmessage.js_');
    }
    function v6_expand_JSUtilities() {
        return $this->read_template('hp6utilities.js_');
    }

    // js files

    function v6_expand_JSJCloze6() {
        return $this->read_template('jcloze6.js_');
    }
    function v6_expand_JSJCross6() {
        return $this->read_template('jcross6.js_');
    }
    function v6_expand_JSJMatch6() {
        return $this->read_template('jmatch6.js_');
    }
    function v6_expand_JSJMix6() {
        return $this->read_template('jmix6.js_');
    }
    function v6_expand_JSJQuiz6() {
        return $this->read_template('jquiz6.js_');
    }

    // drag and drop

    function v6_expand_JSDJMatch6() {
        return $this->read_template('djmatch6.js_');
    }
    function v6_expand_JSDJMix6() {
        return $this->read_template('djmix6.js_');
    }

    // what are these for?

    function v6_expand_JSFJMatch6() {
        return $this->read_template('fjmatch6.js_');
    }
    function v6_expand_JSFJMix6() {
        return $this->read_template('fjmix6.js_');
    }

    // jmatch6.js_

    function v6_expand_ShuffleQs() {
        return $this->bool_value('hotpot-config-file,'.$this->parent->quiztype.',shuffle-questions');
    }
    function v6_expand_QsToShow() {
        $i = $this->parent->xml_value('hotpot-config-file,'.$this->parent->quiztype.',show-limited-questions');
        if ($i) {
            $i = $this->parent->xml_value('hotpot-config-file,'.$this->parent->quiztype.',questions-to-show');
        }
        if (empty($i)) {
            $i = 0;
            switch ($this->parent->quiztype) {
                case 'jmatch':
                    $values = $this->parent->xml_values('data,matching-exercise,pair');
                    $i = count($values);
                    break;
                case 'jquiz':
                    $tags = 'data,questions,question-record';
                    while (($question="[$i]['#']") && $this->parent->xml_value($tags, $question)) {
                        $i++;
                    }
                    break;
            } // end switch
        }
        return $i;
    }
    function v6_expand_MatchDivItems() {
        $this->set_jmatch_items();

        $l_keys = $this->shuffle_jmatch_items($this->l_items);
        $r_keys = $this->shuffle_jmatch_items($this->r_items);

        $options = '<option value="x">'.$this->parent->xml_value('data,matching-exercise,default-right-item').'</option>';
        foreach ($r_keys as $key) {
            if (! $this->r_items[$key]['fixed']) {
                $options .= '<option value="'.$key.'">'.$this->r_items[$key]['text'].'</option>'."\n";
            }
        }

        $str = '';
        foreach ($l_keys as $key) {
            $str .= '<tr><td class="LeftItem">'.$this->l_items[$key]['text'].'</td>';
            $str .= '<td class="RightItem">';
            if ($this->r_items[$key]['fixed']) {
                $str .= $this->r_items[$key]['text'];
            }  else {
                $str .= '<select id="s'.$key.'_'.$key.'">'.$options.'</select>';
            }
            $str .= '</td><td></td></tr>';
        }
        return $str;
    }

    // jmix6.js_

    function v6_expand_Punctuation() {
        $tags = 'data,jumbled-order-exercise';
        $chars = array_merge(
            $this->jmix_Punctuation("$tags,main-order,segment"),
            $this->jmix_Punctuation("$tags,alternate")
        );
        $chars = array_unique($chars);
        $chars = implode('', $chars);
        $chars = $this->js_safe($chars, true);
        return $chars;
    }
    function jmix_Punctuation($tags) {
        $chars = array();

        // all punctutation except '&#;' (because they are used in html entities)
        $ENTITIES = $this->jmix_encode_punctuation('!"$%'."'".'()*+,-./:<=>?@[\]^_`{|}~');
        $pattern = "/&#x([0-9A-F]+);/i";
        $i = 0;

        // get next segment (or alternate answer)
        while ($value = $this->parent->xml_value($tags, "[$i]['#']")) {

            // convert low-ascii punctuation to entities
            $value = strtr($value, $ENTITIES);

            // extract all hex HTML entities
            if (preg_match_all($pattern, $value, $matches)) {

                // loop through hex entities
                $m_max = count($matches[0]);
                for ($m=0; $m<$m_max; $m++) {

                    // convert to hex number
                    eval('$hex=0x'.$matches[1][$m].';');

                    // is this a punctuation character?
                    if (
                        ($hex>=0x0020 && $hex<=0x00BF) || // ascii punctuation
                        ($hex>=0x2000 && $hex<=0x206F) || // general punctuation
                        ($hex>=0x3000 && $hex<=0x303F) || // CJK punctuation
                        ($hex>=0xFE30 && $hex<=0xFE4F) || // CJK compatability
                        ($hex>=0xFE50 && $hex<=0xFE6F) || // small form variants
                        ($hex>=0xFF00 && $hex<=0xFF40) || // halfwidth and fullwidth forms (1)
                        ($hex>=0xFF5B && $hex<=0xFF65) || // halfwidth and fullwidth forms (2)
                        ($hex>=0xFFE0 && $hex<=0xFFEE)    // halfwidth and fullwidth forms (3)
                    ) {
                        // add this character
                        $chars[] = $matches[0][$m];
                    }
                }
            }
            $i++;
        }

        return $chars;
    }
    function v6_expand_OpenPunctuation() {
        $tags = 'data,jumbled-order-exercise';
        $chars = array_merge(
            $this->jmix_OpenPunctuation("$tags,main-order,segment"),
            $this->jmix_OpenPunctuation("$tags,alternate")
        );
        $chars = array_unique($chars);
        $chars = implode('', $chars);
        $chars = $this->js_safe($chars, true);
        return $chars;
    }
    function jmix_OpenPunctuation($tags) {
        $chars = array();

        // unicode punctuation designations (pi="initial quote", ps="open")
        //  http://www.sql-und-xml.de/unicode-database/pi.html
        //  http://www.sql-und-xml.de/unicode-database/ps.html
        $pi = '0022|0027|00AB|2018|201B|201C|201F|2039';
        $ps = '0028|005B|007B|0F3A|0F3C|169B|201A|201E|2045|207D|208D|2329|23B4|2768|276A|276C|276E|2770|2772|2774|27E6|27E8|27EA|2983|2985|2987|2989|298B|298D|298F|2991|2993|2995|2997|29D8|29DA|29FC|3008|300A|300C|300E|3010|3014|3016|3018|301A|301D|FD3E|FE35|FE37|FE39|FE3B|FE3D|FE3F|FE41|FE43|FE47|FE59|FE5B|FE5D|FF08|FF3B|FF5B|FF5F|FF62';
        $pattern = "/(&#x($pi|$ps);)/i";

        $ENTITIES = $this->jmix_encode_punctuation('"'."'".'(<[{');

        $i = 0;
        while ($value = $this->parent->xml_value($tags, "[$i]['#']")) {
            $value = strtr($value, $ENTITIES);
            if (preg_match_all($pattern, $value, $matches)) {
                $chars = array_merge($chars, $matches[0]);
            }
            $i++;
        }

        return $chars;
    }
    function jmix_encode_punctuation($str) {
        $ENTITIES = array();
        $i_max = strlen($str);
        for ($i=0; $i<$i_max; $i++) {
            $ENTITIES[$str{$i}] = '&#x'.sprintf('%04X', ord($str{$i})).';';
        }
        return $ENTITIES;
    }
    function v6_expand_ExerciseTitle() {
        return $this->parent->xml_value('data,title');
    }

    // Jmix specials

    function v6_expand_SegmentArray() {

        $segments = array();
        $values = array();
        $VALUES = array();

        // XML tags to the start of a segment
        $tags = 'data,jumbled-order-exercise,main-order,segment';

        $i = 0;
        while ($value = $this->parent->xml_value($tags, "[$i]['#']")) {
            $VALUE = strtoupper($value);
            $key = array_search($VALUE, $VALUES);
            if (is_numeric($key)) {
                $segments[] = $key;
            } else {
                $segments[] = $i;
                $values[$i] = $value;
                $VALUES[$i] = $VALUE;
            }
            $i++;
        }

        $this->seed_random_number_generator();
        $keys = array_keys($segments);
        shuffle($keys);

        $str = '';
        for($i=0; $i<count($keys); $i++) {
            $key = $segments[$keys[$i]];
            $str .= "Segments[$i] = new Array();\n";
            $str .= "Segments[$i][0] = '".$this->js_safe($values[$key], true)."';\n";
            $str .= "Segments[$i][1] = ".($key+1).";\n";
            $str .= "Segments[$i][2] = 0;\n";
        }
        return $str;
    }
    function v6_expand_AnswerArray() {

        $segments = array();
        $values = array();
        $VALUES = array();
        $escapedvalues = array();

        // XML tags to the start of a segment
        $tags = 'data,jumbled-order-exercise,main-order,segment';

        $i = 0;
        while ($value = $this->parent->xml_value($tags, "[$i]['#']")) {
            $VALUE = strtoupper($value);
            $key = array_search($VALUE, $VALUES);
            if (is_numeric($key)) {
                $segments[] = $key+1;
            } else {
                $segments[] = $i+1;
                $values[$i] = $value;
                $VALUES[$i] = $VALUE;
                $escapedvalues[] = preg_quote($value, '/');
            }
            $i++;
        }

        // start the answers array
        $a = 0;
        $str = 'Answers['.($a++).'] = new Array('.implode(',', $segments).");\n";

        // pattern to match the next part of an alternate answer
        $pattern = '/^('.implode('|', $escapedvalues).')\\s*/i';

        // XML tags to the start of an alternate answer
        $tags = 'data,jumbled-order-exercise,alternate';

        $i = 0;
        while ($value = $this->parent->xml_value($tags, "[$i]['#']")) {
            $segments = array();
            while (strlen($value) && preg_match($pattern, $value, $matches)) {
                $key = array_search($matches[1], $values);
                if (is_numeric($key)) {
                    $segments[] = $key+1;
                    $value = substr($value, strlen($matches[0]));
                } else {
                    // invalid alternate sequence
                    $segments = array();
                    break;
                }
            }
            if (count($segments)) {
                $str .= 'Answers['.($a++).'] = new Array('.implode(',', $segments).");\n";
            }
            $i++;
        }
        return $str;
    }

    // ===============================================================

    // JMix (jmix6.js_)

    function v6_expand_RemainingWords() {
        return $this->parent->xml_value('hotpot-config-file,'.$this->parent->quiztype.',remaining-words');
    }
    function v6_expand_TimesUp() {
        return $this->js_value('hotpot-config-file,global,times-up');
    }

    // nav bar

    function v6_expand_NavBar($navbarid='') {
        $this->navbarid = $navbarid;

        $tag = 'navbar';
        $this->read_template('hp6navbar.ht_', $tag);

        unset($this->navbarid);

        return $this->$tag;
    }
    function v6_expand_TopNavBar() {
        return $this->v6_expand_NavBar('TopNavBar');
    }
    function v6_expand_BottomNavBar() {
        return $this->v6_expand_NavBar('BottomNavBar');
    }

    // hp6navbar.ht_

    function v6_expand_NavBarID() {
        // $this->navbarid is set in "$this->v6_expand_NavBar"
        return empty($this->navbarid) ? '' : $this->navbarid;
    }
    function v6_expand_ContentsURL() {
        $url = $this->parent->xml_value('hotpot-config-file,global,contents-url');
        if ($url) {
            $url = hotpot_convert_navbutton_url($this->parent->get_baseurl(), $this->parent->reference, $url, $this->parent->course);
        }
        return $url;
    }
    function v6_expand_NextExURL() {
        $url = $this->parent->xml_value('hotpot-config-file,'.$this->parent->quiztype.',next-ex-url');
        if ($url) {
            $url = hotpot_convert_navbutton_url($this->parent->get_baseurl(), $this->parent->reference, $url, $this->parent->course);
        }
        return $url;
    }

    // conditional blocks

    function v6_expand_ShowAnswer() {
        return $this->int_value('hotpot-config-file,'.$this->parent->quiztype.',include-show-answer');
    }
    function v6_expand_Slide() {
        return true; // whats's this (JMatch drag and drop)
    }

    // specials (JMatch)

    function v6_expand_FixedArray() {
        $this->set_jmatch_items();
        $str = '';
        foreach ($this->l_items as $i=>$item) {
            for ($ii=0; $ii<$i; $ii++) {
                if ($this->r_items[$ii]['text']==$this->r_items[$i]['text']) {
                    break;
                }
            }
            $str .= "F[$i] = new Array();\n";
            $str .= "F[$i][0] = '".$this->js_safe($item['text'], true)."';\n";
            $str .= "F[$i][1] = ".($ii+1).";\n";
        }
        return $str;
    }
    function v6_expand_DragArray() {
        $this->set_jmatch_items();
        $str = '';
        foreach ($this->r_items as $i=>$item) {
            for ($ii=0; $ii<$i; $ii++) {
                if ($this->r_items[$ii]['text']==$this->r_items[$i]['text']) {
                    break;
                }
            }
            $str .= "D[$i] = new Array();\n";
            $str .= "D[$i][0] = '".$this->js_safe($item['text'], true)."';\n";
            $str .= "D[$i][1] = ".($ii+1).";\n";
            $str .= "D[$i][2] = ".$item['fixed'].";\n";
        }
        return $str;
    }

    function set_jmatch_items() {
        if (count($this->l_items)) {
            return;
        }
        $tags = 'data,matching-exercise,pair';
        $i = 0;
        while (($item = "[$i]['#']") && $this->parent->xml_value($tags, $item)) {
            $leftitem = $item."['left-item'][0]['#']";
            $lefttext = $this->parent->xml_value($tags, $leftitem."['text'][0]['#']");

            $rightitem = $item."['right-item'][0]['#']";
            $righttext = $this->parent->xml_value($tags, $rightitem."['text'][0]['#']");

            if (strlen($righttext)) {
                $addright = true;
            } else {
                $addright = false;
            }
            if (strlen($lefttext)) {
                $this->l_items[] = array(
                    'text' => $lefttext,
                    'fixed' => $this->int_value($tags, $leftitem."['fixed'][0]['#']")
                );
                $addright = true; // force right item to be added
            }
            if ($addright) {
                $this->r_items[] = array(
                    'text' => $righttext,
                    'fixed' => $this->int_value($tags, $rightitem."['fixed'][0]['#']")
                );
            }
            $i++;
        }
    }
    function shuffle_jmatch_items(&$items) {
        // get moveable items
        $moveable_keys = array();
        for($i=0; $i<count($items); $i++) {
            if(empty($items[$i]['fixed'])) {
                $moveable_keys[] = $i;
            }
        }
        // shuffle moveable items
        $this->seed_random_number_generator();
        shuffle($moveable_keys);

        $keys = array();
        for($i=0, $ii=0; $i<count($items); $i++) {
            if(empty($items[$i]['fixed'])) {
                //  moveable items are inserted in a shuffled order
                $keys[] = $moveable_keys[$ii++];
            } else {
                //  fixed items stay where they are
                $keys[] = $i;
            }
        }
        return $keys;
    }
    function seed_random_number_generator() {
        static $seeded_RNG = FALSE;
        if (!$seeded_RNG) {
            srand((double) microtime() * 1000000);
            $seeded_RNG = TRUE;
        }
    }

    // specials (JMix)


    // specials (JCloze)

    function v6_expand_ItemArray() {
        $q = 0;
        $str = '';
        switch ($this->parent->quiztype) {
            case 'jcloze':
                $tags = 'data,gap-fill,question-record';
                while (($question="[$q]['#']") && $this->parent->xml_value($tags, $question)) {
                    $a = 0;
                    $aa = 0;
                    while (($answer=$question."['answer'][$a]['#']") && $this->parent->xml_value($tags, $answer)) {
                        $text = $this->js_value($tags,  $answer."['text'][0]['#']");
                        if (strlen($text)) {
                            if ($aa==0) { // first time only
                                $str .= "\n";
                                $str .= "I[$q] = new Array();\n";
                                $str .= "I[$q][1] = new Array();\n";
                            }
                            $str .= "I[$q][1][$aa] = new Array();\n";
                            $str .= "I[$q][1][$aa][0] = '$text';\n";
                            $aa++;
                        }
                        $a++;
                    }
                    // add clue, if any answers were found
                    if ($aa) {
                        $clue = $this->js_value($tags, $question."['clue'][0]['#']");
                        $str .= "I[$q][2] = '$clue';\n";
                    }
                    $q++;
                }
                break;
            case 'jquiz':
                $str .= "I=new Array();\n";
                $tags = 'data,questions,question-record';
                while (($question="[$q]['#']") && $this->parent->xml_value($tags, $question)) {

                    $question_type = $this->int_value($tags, $question."['question-type'][0]['#']");
                    $weighting = $this->int_value($tags, $question."['weighting'][0]['#']");
                    $clue = $this->js_value($tags, $question."['clue'][0]['#']");

                    $answers = $question."['answers'][0]['#']";

                    $a = 0;
                    $aa = 0;
                    while (($answer = $answers."['answer'][$a]['#']") && $this->parent->xml_value($tags, $answer)) {
                        $text =     $this->js_value($tags,  $answer."['text'][0]['#']");
                        $feedback = $this->js_value($tags,  $answer."['feedback'][0]['#']");
                        $correct =  $this->int_value($tags, $answer."['correct'][0]['#']");
                        $percent =  $this->int_value($tags, $answer."['percent-correct'][0]['#']");
                        $include =  $this->int_value($tags, $answer."['include-in-mc-options'][0]['#']");
                        if (strlen($text)) {
                            if ($aa==0) { // first time only
                                $str .= "\n";
                                $str .= "I[$q] = new Array();\n";
                                $str .= "I[$q][0] = $weighting;\n";
                                $str .= "I[$q][1] = '$clue';\n";
                                $str .= "I[$q][2] = '".($question_type-1)."';\n";
                                $str .= "I[$q][3] = new Array();\n";
                            }
                            $str .= "I[$q][3][$aa] = new Array('$text','$feedback',$correct,$percent,$include);\n";
                            $aa++;
                        }
                        $a++;
                    }
                    $q++;
                }
                break;
        }
        return $str;
    }

    function v6_expand_ClozeBody() {
        $str = '';

        // get drop down list of words, if required
        $dropdownlist = '';
        if ($this->v6_use_DropDownList()) {
            $this->v6_set_WordList();
            foreach ($this->wordlist as $word) {
                $dropdownlist .= '<option value="'.$word.'">'.$word.'</option>';
            }
        }

        // cache clues flag and caption
        $includeclues = $this->v6_expand_Clues();
        $cluecaption = $this->v6_expand_ClueCaption();

        // detect if cloze starts with gap
        $strpos = strpos($this->parent->source, '<gap-fill><question-record>');
        if (is_numeric($strpos)) {
            $startwithgap = true;
        } else {
            $startwithgap = false;
        }

        // initialize loop values
        $q = 0;
        $tags = 'data,gap-fill';
        $question_record = "$tags,question-record";

        // loop through text and gaps
        do {
            $text = $this->parent->xml_value($tags, "[0]['#'][$q]");
            $gap = '';
            if (($question="[$q]['#']") && $this->parent->xml_value($question_record, $question)) {
                $gap .= '<span class="GapSpan" id="GapSpan'.$q.'">';
                if ($this->v6_use_DropDownList()) {
                    $gap .= '<select id="Gap'.$q.'"><option value=""></option>'.$dropdownlist.'</select>';
                } else {
                    // minimum gap size
                    if (! $gapsize = $this->int_value('hotpot-config-file,'.$this->parent->quiztype.',minimum-gap-size')) {
                        $gapsize = 6;
                    }

                    // increase gap size to length of longest answer for this gap
                    $a = 0;
                    while (($answer=$question."['answer'][$a]['#']") && $this->parent->xml_value($question_record, $answer)) {
                        $answertext = $this->parent->xml_value($question_record,  $answer."['text'][0]['#']");
                        $answertext = preg_replace('|&[#a-zA-Z0-9]+;|', 'x', $answertext);
                        $gapsize = max($gapsize, strlen($answertext));
                        $a++;
                    }

                    $gap .= '<input type="text" id="Gap'.$q.'" onfocus="TrackFocus('.$q.')" onblur="LeaveGap()" class="GapBox" size="'.$gapsize.'"></input>';
                }
                if ($includeclues) {
                    $clue = $this->parent->xml_value($question_record, $question."['clue'][0]['#']");
                    if (strlen($clue)) {
                        $gap .= '<button style="line-height: 1.0" class="FuncButton" onfocus="FuncBtnOver(this)" onmouseover="FuncBtnOver(this)" onblur="FuncBtnOut(this)" onmouseout="FuncBtnOut(this)" onmousedown="FuncBtnDown(this)" onmouseup="FuncBtnOut(this)" onclick="ShowClue('.$q.')">'.$cluecaption.'</button>';
                    }
                }
                $gap .= '</span>';
            }
            if ($startwithgap) {
                $str .= "$gap$text";
            } else {
                $str .= "$text$gap";
            }
            $q++;
        } while (strlen($text) || strlen($gap));

        return $str;
    }

    // JCloze quiztype

    function v6_expand_WordList() {
        $str = '';
        if ($this->v6_include_WordList()) {
            $this->v6_set_WordList();
            $str = implode(' &#160;&#160; ', $this->wordlist);
        }
        return $str;
    }
    function v6_include_WordList() {
        return $this->int_value('hotpot-config-file,'.$this->parent->quiztype.',include-word-list');
    }
    function v6_use_DropDownList() {
        return $this->int_value('hotpot-config-file,'.$this->parent->quiztype.',use-drop-down-list');
    }
    function v6_set_WordList() {

        if (isset($this->wordlist)) {
            // do nothing
        } else {
            $this->wordlist = array();

            // is the wordlist required
            if ($this->v6_include_WordList() || $this->v6_use_DropDownList()) {

                $q = 0;
                $tags = 'data,gap-fill,question-record';
                while (($question="[$q]['#']") && $this->parent->xml_value($tags, $question)) {
                    $a = 0;
                    $aa = 0;
                    while (($answer=$question."['answer'][$a]['#']") && $this->parent->xml_value($tags, $answer)) {
                        $text = $this->parent->xml_value($tags,  $answer."['text'][0]['#']");
                        $correct =  $this->int_value($tags, $answer."['correct'][0]['#']");
                        if ($text && $correct) { // $correct is always true
                            $this->wordlist[] = $text;
                            $aa++;
                        }
                        $a++;
                    }
                    $q++;
                }
                $this->wordlist = array_unique($this->wordlist);
                sort($this->wordlist);
            }
        }
    }
    function v6_expand_Keypad() {
        $str = '';
        if ($this->int_value('hotpot-config-file,'.$this->parent->quiztype.',include-keypad')) {

            // these characters must always be in the keypad
            $chars = array();
            $this->add_keypad_chars($chars, $this->parent->xml_value('hotpot-config-file,global,keypad-characters'));

            // append other characters used in the answers
            $tags = '';
            switch ($this->parent->quiztype) {
                case 'jcloze':
                    $tags = 'data,gap-fill,question-record';
                    break;
                case 'jquiz':
                    $tags = 'data,questions,question-record';
                    break;
            }
            if ($tags) {
                $q = 0;
                while (($question="[$q]['#']") && $this->parent->xml_value($tags, $question)) {

                    if ($this->parent->quiztype=='jquiz') {
                        $answers = $question."['answers'][0]['#']";
                    } else {
                        $answers = $question;
                    }

                    $a = 0;
                    while (($answer=$answers."['answer'][$a]['#']") && $this->parent->xml_value($tags, $answer)) {
                        $this->add_keypad_chars($chars, $this->parent->xml_value($tags,  $answer."['text'][0]['#']"));
                        $a++;
                    }
                    $q++;
                }
            }

            // remove duplicate characters and sort
            $chars = array_unique($chars);
            usort($chars, "hotpot_sort_keypad_chars");

            // create keypad buttons for each character
            foreach ($chars as $char) {
                $str .= "<button onclick=\"TypeChars('".$this->js_safe($char, true)."'); return false;\">$char</button>";
            }
        }
        return $str;
    }
    function add_keypad_chars(&$chars, $text) {
        if (preg_match_all('|&[^;]+;|i', $text, $more_chars)) {
            $chars = array_merge($chars, $more_chars[0]);
        }
    }
    function v6_expand_Correct() {
        if ($this->parent->quiztype=='jcloze') {
            $tag = 'guesses-correct';
        } else {
            $tag = 'guess-correct';
        }
        return $this->js_value('hotpot-config-file,'.$this->parent->quiztype.','.$tag);
    }
    function v6_expand_Incorrect() {
        if ($this->parent->quiztype=='jcloze') {
            $tag = 'guesses-incorrect';
        } else {
            $tag = 'guess-incorrect';
        }
        return $this->js_value('hotpot-config-file,'.$this->parent->quiztype.','.$tag);
    }
    function v6_expand_GiveHint() {
        return $this->js_value('hotpot-config-file,'.$this->parent->quiztype.',next-correct-letter');
    }
    function v6_expand_CaseSensitive() {
        return $this->bool_value('hotpot-config-file,'.$this->parent->quiztype.',case-sensitive');
    }

    // JCross quiztype

    function v6_expand_CluesAcrossLabel() {
        $this->parent->xml_value('hotpot-config-file,'.$this->parent->quiztype.',clues-across');
    }
    function v6_expand_CluesDownLabel() {
        $this->parent->xml_value('hotpot-config-file,'.$this->parent->quiztype.',clues-down');
    }
    function v6_expand_EnterCaption() {
        $this->parent->xml_value('hotpot-config-file,'.$this->parent->quiztype.',enter-caption');
    }
    function v6_expand_ShowHideClueList() {
        $value = $this->int_value('hotpot-config-file,'.$this->parent->quiztype.',include-clue-list');
        return empty($value) ? ' style="display: none;"' : '';
    }

    // JCross specials

    function v6_expand_CluesDown() {
        return $this->v6_expand_jcross_clues('D');
    }
    function v6_expand_CluesAcross() {
        return $this->v6_expand_jcross_clues('A');
    }
    function v6_expand_jcross_clues($direction) {
        // $direction: A(cross) or D(own)
        $row = NULL;
        $r_max = 0;
        $c_max = 0;
        $this->v6_get_jcross_grid($row, $r_max, $c_max);

        $i = 0; // clue index;
        $str = '';
        for($r=0; $r<=$r_max; $r++) {
            for($c=0; $c<=$c_max; $c++) {
                $aword = $this->get_jcross_aword($row, $r, $r_max, $c, $c_max);
                $dword = $this->get_jcross_dword($row, $r, $r_max, $c, $c_max);
                if ($aword || $dword) {
                    $i++; // increment clue index

                    // get the definition for this word
                    $def = '';
                    $word = ($direction=='A') ? $aword : $dword;
                    $clues = $this->parent->xml_values('data,crossword,clues,item');
                    foreach ($clues as $clue) {
                        if ($clue['word'][0]['#']==$word) {
                            $def = $clue['def'][0]['#'];
                            $def = strtr($def, array('&#x003C;'=>'<', '&#x003E;'=>'>', "\n"=>'<br />'));
                            break;
                        }
                    }

                    if (!empty($def)) {
                        $str .= '<tr><td class="ClueNum">'.$i.'. </td><td id="Clue_'.$direction.'_'.$i.'" class="Clue">'.$def.'</td></tr>';
                    }
                }
            }
        }
        return $str;
    }

    // jcross6.js_

    function v6_expand_LetterArray() {
        $row = NULL;
        $r_max = 0;
        $c_max = 0;
        $this->v6_get_jcross_grid($row, $r_max, $c_max);

        $str = '';
        for($r=0; $r<=$r_max; $r++) {
            $str .= "L[$r] = new Array(";
            for($c=0; $c<=$c_max; $c++) {
                $str .= ($c>0 ? ',' : '')."'".$this->js_safe($row[$r]['cell'][$c]['#'], true)."'";
            }
            $str .= ");\n";
        }
        return $str;
    }
    function v6_expand_GuessArray() {
        $row = NULL;
        $r_max = 0;
        $c_max = 0;
        $this->v6_get_jcross_grid($row, $r_max, $c_max);

        $str = '';
        for($r=0; $r<=$r_max; $r++) {
            $str .= "G[$r] = new Array('".str_repeat("','", $c_max)."');\n";
        }
        return $str;
    }
    function v6_expand_ClueNumArray() {
        $row = NULL;
        $r_max = 0;
        $c_max = 0;
        $this->v6_get_jcross_grid($row, $r_max, $c_max);

        $i = 0; // clue index
        $str = '';
        for($r=0; $r<=$r_max; $r++) {
            $str .= "CL[$r] = new Array(";
            for($c=0; $c<=$c_max; $c++) {
                if ($c>0) {
                    $str .= ',';
                }
                $aword = $this->get_jcross_aword($row, $r, $r_max, $c, $c_max);
                $dword = $this->get_jcross_dword($row, $r, $r_max, $c, $c_max);
                if (empty($aword) && empty($dword)) {
                    $str .= 0;
                } else {
                    $i++; // increment the clue index
                    $str .= $i;
                }
            }
            $str .= ");\n";
        }
        return $str;
    }
    function v6_expand_GridBody() {
        $row = NULL;
        $r_max = 0;
        $c_max = 0;
        $this->v6_get_jcross_grid($row, $r_max, $c_max);

        $i = 0; // clue index;
        $str = '';
        for($r=0; $r<=$r_max; $r++) {
            $str .= '<tr id="Row_'.$r.'">';
            for($c=0; $c<=$c_max; $c++) {
                if (empty($row[$r]['cell'][$c]['#'])) {
                    $str .= '<td class="BlankCell">&nbsp;</td>';
                } else {
                    $aword = $this->get_jcross_aword($row, $r, $r_max, $c, $c_max);
                    $dword = $this->get_jcross_dword($row, $r, $r_max, $c, $c_max);
                    if (empty($aword) && empty($dword)) {
                        $str .= '<td class="LetterOnlyCell"><span id="L_'.$r.'_'.$c.'">&nbsp;</span></td>';
                    } else {
                        $i++; // increment clue index
                        $str .= '<td class="NumLetterCell"><a href="javascript:void(0);" class="GridNum" onclick="ShowClue('.$i.','.$r.','.$c.')">'.$i.'</a><span class="NumLetterCellText" id="L_'.$r.'_'.$c.'" onclick="ShowClue('.$i.','.$r.','.$c.')">&nbsp;&nbsp;&nbsp;</span></td>';
                    }
                }
            }
            $str .= '</tr>';
        }
        return $str;
    }
    function v6_get_jcross_grid(&$row, &$r_max, &$c_max) {
        $row = $this->parent->xml_values('data,crossword,grid,row');
        $r_max = 0;
        $c_max = 0;
        if (isset($row) && is_array($row)) {
            for($r=0; $r<count($row); $r++) {
                if (isset($row[$r]['cell']) && is_array($row[$r]['cell'])) {
                    for($c=0; $c<count($row[$r]['cell']); $c++) {
                        if (!empty($row[$r]['cell'][$c]['#'])) {
                            $r_max = max($r, $r_max);
                            $c_max = max($c, $c_max);
                        }
                    } // end for $c
                }
            } // end for $r
        }
    }
    function get_jcross_dword(&$row, $r, $r_max, $c, $c_max) {
        $str = '';
        if (($r==0 || empty($row[$r-1]['cell'][$c]['#'])) && $r<$r_max && !empty($row[$r+1]['cell'][$c]['#'])) {
            $str = $this->get_jcross_word($row, $r, $r_max, $c, $c_max, true);
        }
        return $str;
    }
    function get_jcross_aword(&$row, $r, $r_max, $c, $c_max) {
        $str = '';
        if (($c==0 || empty($row[$r]['cell'][$c-1]['#'])) && $c<$c_max && !empty($row[$r]['cell'][$c+1]['#'])) {
            $str = $this->get_jcross_word($row, $r, $r_max, $c, $c_max, false);
        }
        return $str;
    }
    function get_jcross_word(&$row, $r, $r_max, $c, $c_max, $go_down=false) {
        $str = '';
        while ($r<=$r_max && $c<=$c_max && !empty($row[$r]['cell'][$c]['#'])) {
            $str .= $row[$r]['cell'][$c]['#'];
            if ($go_down) {
                $r++;
            } else {
                $c++;
            }
        }
        return $str;
    }

    // specials (JQuiz)

    function v6_expand_QuestionOutput() {
        $str = '';
        $str .= '<ol class="QuizQuestions" id="Questions">'."\n";

        $q = 0;
        $tags = 'data,questions,question-record';
        while (($question="[$q]['#']") && $this->parent->xml_value($tags, $question)) {

            // get question
            $question_text = $this->parent->xml_value($tags, $question."['question'][0]['#']");
            $question_type = $this->parent->xml_value($tags, $question."['question-type'][0]['#']");

            $first_answer_text = $this->parent->xml_value($tags, $question."['answers'][0]['#']['answer'][0]['#']['text'][0]['#']");

            // check we have a question (or at least one answer)
            if (($question_text || $first_answer_text) && $question_type) {

                $str .= '<li class="QuizQuestion" id="Q_'.$q.'" style="display: none;">';
                $str .= '<p class="QuestionText">'.$question_text.'</p>';

                if (
                    $question_type==HOTPOT_JQUIZ_SHORTANSWER ||
                    $question_type==HOTPOT_JQUIZ_HYBRID
                ) {
                    $size = 9; // default size
                    $a = 0;
                    $answers = $question."['answers'][0]['#']";
                    while (($answer = $answers."['answer'][$a]['#']") && $this->parent->xml_value($tags, $answer)) {
                        $text = $this->parent->xml_value($tags, $answer."['text'][0]['#']");
                        $text = preg_replace('/&[#a-zA-Z0-9]+;/', 'x', $text);
                        $size = max($size, strlen($text));
                        $a++;
                    }

                    $str .= '<div class="ShortAnswer" id="Q_'.$q.'_SA"><form method="post" action="" onsubmit="return false;"><div>';
                    if ($size<=25) { // text box
                        $str .= '<input type="text" id="Q_'.$q.'_Guess" onfocus="TrackFocus('."'".'Q_'.$q.'_Guess'."'".')" onblur="LeaveGap()" class="ShortAnswerBox" size="'.$size.'"></input>';
                    } else { // textarea (29 cols wide)
                        $str .= '<textarea id="Q_'.$q.'_Guess" onfocus="TrackFocus('."'".'Q_'.$q.'_Guess'."'".')" onblur="LeaveGap()" class="ShortAnswerBox" cols="29" rows="'.ceil($size/25).'"></textarea>';
                    }
                    $str .= '<br /><br />';

                    $caption = $this->v6_expand_CheckCaption();
                    $str .= $this->v6_expand_jquiz_button($caption, "CheckShortAnswer($q)");

                    if ($this->v6_expand_Hint()) {
                        $caption = $this->v6_expand_HintCaption();
                        $str .= $this->v6_expand_jquiz_button($caption, "ShowHint($q)");
                    }

                    if ($this->v6_expand_ShowAnswer()) {
                        $caption = $this->v6_expand_ShowAnswerCaption();
                        $str .= $this->v6_expand_jquiz_button($caption, "ShowAnswers($q)");
                    }

                    $str .= '</div></form></div>';
                }

                if (
                    $question_type==HOTPOT_JQUIZ_MULTICHOICE ||
                    $question_type==HOTPOT_JQUIZ_HYBRID ||
                    $question_type==HOTPOT_JQUIZ_MULTISELECT
                ) {

                    switch ($question_type) {
                        case HOTPOT_JQUIZ_MULTICHOICE:
                            $str .= '<ol class="MCAnswers">'."\n";
                        break;
                        case HOTPOT_JQUIZ_HYBRID:
                            $str .= '<ol class="MCAnswers" id="Q_'.$q.'_Hybrid_MC" style="display: none;">'."\n";
                        break;
                        case HOTPOT_JQUIZ_MULTISELECT:
                            $str .= '<ol class="MSelAnswers">'."\n";
                        break;
                    }

                    $a = 0;
                    $aa = 0;
                    $answers = $question."['answers'][0]['#']";
                    while (($answer = $answers."['answer'][$a]['#']") && $this->parent->xml_value($tags, $answer)) {
                        $text = $this->parent->xml_value($tags, $answer."['text'][0]['#']");
                        if ($text) {
                            switch ($question_type) {
                                case HOTPOT_JQUIZ_MULTICHOICE:
                                case HOTPOT_JQUIZ_HYBRID:
                                    $include = $this->int_value($tags, $answer."['include-in-mc-options'][0]['#']");
                                    if ($include) {
                                        $str .= '<li id="Q_'.$q.'_'.$aa.'"><button class="FuncButton" onfocus="FuncBtnOver(this)" onblur="FuncBtnOut(this)" onmouseover="FuncBtnOver(this)" onmouseout="FuncBtnOut(this)" onmousedown="FuncBtnDown(this)" onmouseup="FuncBtnOut(this)" id="Q_'.$q.'_'.$aa.'_Btn" onclick="CheckMCAnswer('.$q.','.$aa.',this)">&nbsp;&nbsp;?&nbsp;&nbsp;</button>&nbsp;&nbsp;'.$text.'</li>'."\n";
                                    }
                                break;
                                case HOTPOT_JQUIZ_MULTISELECT:
                                    $str .= '<li id="Q_'.$q.'_'.$aa.'"><form method="post" action="" onsubmit="return false;"><div><input type="checkbox" id="Q_'.$q.'_'.$aa.'_Chk" class="MSelCheckbox" />'.$text.'</div></form></li>'."\n";
                                break;
                            }
                            $aa++;
                        }
                        $a++;
                    }

                    $str .= '</ol>';

                    if ($question_type==HOTPOT_JQUIZ_MULTISELECT) {
                        $caption = $this->v6_expand_CheckCaption();
                        $str .= $this->v6_expand_jquiz_button($caption, "CheckMultiSelAnswer($q)");
                    }
                }

                $str .= "</li>\n";
            }
            $q++;

        } // end while $question

        $str .= "</ol>\n";
        return $str;
    }

    function v6_expand_jquiz_button($caption, $onclick) {
        return '<button class="FuncButton" onfocus="FuncBtnOver(this)" onblur="FuncBtnOut(this)" onmouseover="FuncBtnOver(this)" onmouseout="FuncBtnOut(this)" onmousedown="FuncBtnDown(this)" onmouseup="FuncBtnOut(this)" onclick="'.$onclick.'">'.$caption.'</button>';
    }

    // jquiz.js_

    function v6_expand_MultiChoice() {
        return $this->v6_jquiz_question_type(HOTPOT_JQUIZ_MULTICHOICE);
    }
    function v6_expand_ShortAnswer() {
        return $this->v6_jquiz_question_type(HOTPOT_JQUIZ_SHORTANSWER);
    }
    function v6_expand_MultiSelect() {
        return $this->v6_jquiz_question_type(HOTPOT_JQUIZ_MULTISELECT);
    }
    function v6_jquiz_question_type($type) {
        // does this quiz have any questions of the given $type?
        $flag = false;

        $q = 0;
        $tags = 'data,questions,question-record';
        while (($question = "[$q]['#']") && $this->parent->xml_value($tags, $question)) {
            $question_type = $this->parent->xml_value($tags, $question."['question-type'][0]['#']");
            if ($question_type==$type || ($question_type==HOTPOT_JQUIZ_HYBRID && ($type==HOTPOT_JQUIZ_MULTICHOICE || $type==HOTPOT_JQUIZ_SHORTANSWER))) {
                $flag = true;
                break;
            }
            $q++;
        }
        return $flag;
    }
    function v6_expand_CorrectFirstTime() {
        return $this->js_value('hotpot-config-file,global,correct-first-time');
    }
    function v6_expand_ContinuousScoring() {
        return $this->bool_value('hotpot-config-file,'.$this->parent->quiztype.',continuous-scoring');
    }
    function v6_expand_ShowCorrectFirstTime() {
        return $this->bool_value('hotpot-config-file,'.$this->parent->quiztype.',show-correct-first-time');
    }
    function v6_expand_ShuffleAs() {
        return $this->bool_value('hotpot-config-file,'.$this->parent->quiztype.',shuffle-answers');
    }

    function v6_expand_DefaultRight() {
        return $this->v6_expand_GuessCorrect();
    }
    function v6_expand_DefaultWrong() {
        return $this->v6_expand_GuessIncorrect();
    }
    function v6_expand_ShowAllQuestionsCaptionJS() {
        return $this->v6_expand_ShowAllQuestionsCaption();
    }
    function v6_expand_ShowOneByOneCaptionJS() {
        return $this->v6_expand_ShowOneByOneCaption();
    }

    // hp6checkshortanswers.js_ (JQuiz)

    function v6_expand_CorrectList() {
        return $this->js_value('hotpot-config-file,'.$this->parent->quiztype.',correct-answers');
    }
    function v6_expand_HybridTries() {
        return $this->parent->xml_value('hotpot-config-file,'.$this->parent->quiztype.',short-answer-tries-on-hybrid-q');
    }
    function v6_expand_PleaseEnter() {
        return $this->js_value('hotpot-config-file,'.$this->parent->quiztype.',enter-a-guess');
    }
    function v6_expand_PartlyIncorrect() {
        return $this->js_value('hotpot-config-file,'.$this->parent->quiztype.',partly-incorrect');
    }
    function v6_expand_ShowAnswerCaption() {
        return $this->parent->xml_value('hotpot-config-file,'.$this->parent->quiztype.',show-answer-caption');
    }
    function v6_expand_ShowAlsoCorrect() {
        return $this->bool_value('hotpot-config-file,global,show-also-correct');
    }

} // end class
function hotpot_sort_keypad_chars($a, $b) {
    $a =  hotpot_keypad_sort_value($a);
    $b =  hotpot_keypad_sort_value($b);
    return ($a<$b) ? -1 : ($a==$b ? 0 : 1);
}
function hotpot_keypad_sort_value($char) {

    // hexadecimal
    if (preg_match('/&#x([0-9A-F]+);/i', $char, $matches)) {
        $ord = hexdec($matches[1]);

    // decimal
    } else if (preg_match('/&#(\d+);/i', $char, $matches)) {
        $ord = intval($matches[1]);

    // other html entity
    } else if (preg_match('/&[^;]+;/', $char, $matches)) {
        $char = html_entity_decode($matches[0]);
        $ord = empty($char) ? 0 : ord($char);

    // not an html entity
    } else {
        $char = trim($char);
        $ord = empty($char) ? 0 : ord($char);
    }

    // lowercase letters (plain or accented)
    if (($ord>=97 && $ord<=122) || ($ord>=224 && $ord<=255)) {
        $sort_value = ($ord-31).'.'.sprintf('%04d', $ord);

    // all other characters
    } else {
        $sort_value = $ord;
    }

    return $sort_value;
}

?>
