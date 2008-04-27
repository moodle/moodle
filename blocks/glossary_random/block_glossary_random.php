<?php // $Id$

define('BGR_RANDOMLY',     '0');
define('BGR_LASTMODIFIED', '1');
define('BGR_NEXTONE',      '2');

class block_glossary_random extends block_base {
    function init() {

        $this->title = get_string('blockname','block_glossary_random');
        $this->version = 2007101509;

    }

    function specialization() {
        global $CFG, $COURSE;
        $this->course = $COURSE;

        // load userdefined title and make sure it's never empty
        if (empty($this->config->title)) {
            $this->title = get_string('blockname','block_glossary_random');
        } else {
            $this->title = $this->config->title;
        }

        if (empty($this->config->glossary)) {
            return false;
        }

        if (!isset($this->config->nexttime)) {
            $this->config->nexttime = 0;
        }

        //check if it's time to put a new entry in cache
        if (time() > $this->config->nexttime) {

            // place glossary concept and definition in $pref->cache
            if (!$numberofentries = count_records('glossary_entries','glossaryid',$this->config->glossary,
                                                  'approved',1)) {
                $this->config->cache = get_string('noentriesyet','block_glossary_random');
                $this->instance_config_commit();
            }

            $limitfrom = 0;
            $limitnum = 1;

            switch ($this->config->type) {

                case BGR_RANDOMLY:
                    $i = rand(1,$numberofentries);
                    $limitfrom = $i-1;
                    $SORT = 'ASC';
                    break;

                case BGR_NEXTONE:
                    if (isset($this->config->previous)) {
                        $i = $this->config->previous + 1;
                    } else {
                        $i = 1;
                    }
                    if ($i > $numberofentries) {  // Loop back to beginning
                        $i = 1;
                    }
                    $limitfrom = $i-1;
                    $SORT = 'ASC';
                    break;

                default:  // BGR_LASTMODIFIED
                    $i = $numberofentries;
                    $limitfrom = 0;
                    $SORT = 'DESC';
                    break;
            }

            if ($entry = get_records_sql('  SELECT concept, definition, format '.
                                         '    FROM '.$CFG->prefix.'glossary_entries'.
                                         '   WHERE glossaryid = '.$this->config->glossary.
                                         '     AND approved = 1 '.
                                         'ORDER BY timemodified '.$SORT, $limitfrom, $limitnum)) {

                $entry = reset($entry);

                if (empty($this->config->showconcept)) {
                    $text = '';
                } else {
                    $text = "<h2>".format_string($entry->concept,true)."</h2>";
                }  

                $options = new object;
                $options->trusttext = true;
                $text .= format_text($entry->definition, $entry->format, $options);

                $this->config->nexttime = usergetmidnight(time()) + DAYSECS * $this->config->refresh;
                $this->config->previous = $i;

            } else {
                $text = get_string('noentriesyet','block_glossary_random');
            }
            // store the text
            $this->config->cache = $text;
            $this->instance_config_commit();
        }
    }

    function instance_allow_multiple() {
    // Are you going to allow multiple instances of each block?
    // If yes, then it is assumed that the block WILL USE per-instance configuration
        return true;
    }

    function instance_config_print() {
        global $CFG;

        if (!isset($this->config)) {
            // ... teacher has not yet configured the block, let's put some default values here to explain things
            $this->config->title = get_string('blockname','block_glossary_random');
            $this->config->refresh = 0;
            $this->config->showconcept = 1;
            $this->config->cache= get_string('notyetconfigured','block_glossary_random');
            $this->config->addentry=get_string('addentry', 'block_glossary_random');
            $this->config->viewglossary=get_string('viewglossary', 'block_glossary_random');
            $this->config->invisible=get_string('invisible', 'block_glossary_random');
        }

        // select glossaries to put in dropdown box ...
        $glossaries = get_records_select_menu('glossary', 'course='.$this->course->id,'name','id,name');

        //format menu texts to avoid html and to filter multilang values
        if(!empty($glossaries)) {
            foreach($glossaries as $key => $value) {
                $glossaries[$key] = strip_tags(format_string($value,true));
            }
        }

        // and select quotetypes to put in dropdown box
        $type[0] = get_string('random','block_glossary_random');
        $type[1] = get_string('lastmodified','block_glossary_random');
        $type[2] = get_string('nextone','block_glossary_random');

        $this->config->nexttime = usergetmidnight(time()) + DAYSECS * $this->config->refresh;

        // display the form

        if (is_file($CFG->dirroot .'/blocks/'. $this->name() .'/config_instance.html')) {
            print_simple_box_start('center', '', '', 5, 'blockconfigglobal');
            include($CFG->dirroot .'/blocks/'. $this->name() .'/config_instance.html');
            print_simple_box_end();
        } else {
            notice(get_string('blockconfigbad'), str_replace('blockaction=', 'dummy=', qualified_me()));
        }

        return true;
    }

    function get_content() {
        global $USER, $CFG, $COURSE;

        if (empty($this->config->glossary)) {
            $this->content->text   = get_string('notyetconfigured','block_glossary_random');
            $this->content->footer = '';
            return $this->content;
        }

        $glossaryid = $this->config->glossary;

        if ($this->course->id == $COURSE->id) {
            $course = $COURSE;
        } else {
            $course = get_record('course', 'id', $this->course->id); 
        }

        require_once($CFG->dirroot.'/course/lib.php');
        $modinfo = get_fast_modinfo($course);

        if (!isset($modinfo->instances['glossary'][$glossaryid])) {
            // we can get here if the glossary has been deleted, so
            // unconfigure the glossary from the block..
            $this->config->glossary = 0;
            $this->config->cache = '';
            $this->instance_config_commit();

            $this->content->text   = get_string('notyetconfigured','block_glossary_random');
            $this->content->footer = '';
            return $this->content;
        }

        $cm = $modinfo->instances['glossary'][$glossaryid];

        if (empty($this->config->cache)) {
            $this->config->cache = '';
        }

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = $this->config->cache;

        // place link to glossary in the footer if the glossary is visible

        //Obtain the visible property from the instance
        if ($cm->uservisible) {
            if (has_capability('mod/glossary:write', get_context_instance(CONTEXT_MODULE, $cm->id))) {
                $this->content->footer = '<a href="'.$CFG->wwwroot.'/mod/glossary/edit.php?id='.$cm->id
                .'" title="'.$this->config->addentry.'">'.$this->config->addentry.'</a><br />';
            } else {
                $this->content->footer = '';
            }     
            
            $this->content->footer .= '<a href="'.$CFG->wwwroot.'/mod/glossary/view.php?id='.$cm->id
                .'" title="'.$this->config->viewglossary.'">'.$this->config->viewglossary.'</a>';

        // otherwise just place some text, no link
        } else {
            $this->content->footer = $this->config->invisible;
        }

        return $this->content;
    }

    function hide_header() {
        if (empty($this->config->title)) {
            return true;
        }
        return false;
    }

    /**
     * Executed after block instance has been created, we use it to recode
     * the glossary config setting to point to the new (restored) one
     */
    function after_restore($restore) {
    /// We need to transform the glossary->id from the original one to the restored one
        if ($rec = backup_getid($restore->backup_unique_code, 'glossary', $this->config->glossary)) {
            $this->config->glossary = $rec->new_id;
            $this->instance_config_commit();
        }
    }

}
?>
