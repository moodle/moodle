<?php

define('BGR_RANDOMLY',     '0');
define('BGR_LASTMODIFIED', '1');
define('BGR_NEXTONE',      '2');
define('BGR_NEXTALPHA',    '3');

class block_glossary_random extends block_base {

    function init() {
        $this->title = get_string('pluginname','block_glossary_random');
    }

    function specialization() {
        global $CFG, $DB;

        require_once($CFG->libdir . '/filelib.php');

        $this->course = $this->page->course;

        // load userdefined title and make sure it's never empty
        if (empty($this->config->title)) {
            $this->title = get_string('pluginname','block_glossary_random');
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
            if (!$numberofentries = $DB->count_records('glossary_entries',
                                                       array('glossaryid'=>$this->config->glossary, 'approved'=>1))) {
                $this->config->cache = get_string('noentriesyet','block_glossary_random');
                $this->instance_config_commit();
            }

            // Get glossary instance, if not found then return without error, as this will be handled in get_content.
            if (!$glossary = $DB->get_record('glossary', array('id' => $this->config->glossary))) {
                return false;
            }

            $this->config->globalglossary = $glossary->globalglossary;

            // Save course id in config, so we can get correct course module.
            $this->config->courseid = $glossary->course;

            // Get module and context, to be able to rewrite urls
            if (! $cm = get_coursemodule_from_instance('glossary', $glossary->id, $this->config->courseid)) {
                return false;
            }
            $glossaryctx = context_module::instance($cm->id);

            $limitfrom = 0;
            $limitnum = 1;

            $BROWSE = 'timemodified';

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

                case BGR_NEXTALPHA:
                    $BROWSE = 'concept';
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

            if ($entry = $DB->get_records_sql("SELECT id, concept, definition, definitionformat, definitiontrust
                                                 FROM {glossary_entries}
                                                WHERE glossaryid = ? AND approved = 1
                                             ORDER BY $BROWSE $SORT", array($this->config->glossary), $limitfrom, $limitnum)) {

                $entry = reset($entry);

                if (empty($this->config->showconcept)) {
                    $text = '';
                } else {
                    $text = "<h3>".format_string($entry->concept,true)."</h3>";
                }

                $options = new stdClass();
                $options->trusted = $entry->definitiontrust;
                $options->overflowdiv = true;
                $entry->definition = file_rewrite_pluginfile_urls($entry->definition, 'pluginfile.php', $glossaryctx->id, 'mod_glossary', 'entry', $entry->id);
                $text .= format_text($entry->definition, $entry->definitionformat, $options);

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

    function get_content() {
        global $USER, $CFG, $DB;

        if (empty($this->config->glossary)) {
            $this->content = new stdClass();
            $this->content->text   = get_string('notyetconfigured','block_glossary_random');
            $this->content->footer = '';
            return $this->content;
        }

        require_once($CFG->dirroot.'/course/lib.php');

        // If $this->config->globalglossary is not set then get glossary info from db.
        if (!isset($this->config->globalglossary)) {
            if (!$glossary = $DB->get_record('glossary', array('id' => $this->config->glossary))) {
                return '';
            } else {
                $this->config->courseid = $glossary->course;
                $this->config->globalglossary = $glossary->globalglossary;
                $this->instance_config_commit();
            }
        }

        $modinfo = get_fast_modinfo($this->config->courseid);
        // If deleted glossary or non-global glossary on different course page, then reset.
        if (!isset($modinfo->instances['glossary'][$this->config->glossary])
                || ((empty($this->config->globalglossary) && ($this->config->courseid != $this->page->course->id)))) {
            $this->config->glossary = 0;
            $this->config->cache = '';
            $this->instance_config_commit();

            $this->content = new stdClass();
            $this->content->text   = get_string('notyetconfigured','block_glossary_random');
            $this->content->footer = '';
            return $this->content;
        }

        $cm = $modinfo->instances['glossary'][$this->config->glossary];
        if (!has_capability('mod/glossary:view', context_module::instance($cm->id))) {
            return '';
        }

        if (empty($this->config->cache)) {
            $this->config->cache = '';
        }

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass();

        // Show glossary if visible and place links in footer.
        if ($cm->visible) {
            $this->content->text = $this->config->cache;
            if (has_capability('mod/glossary:write', context_module::instance($cm->id))) {
                $this->content->footer = '<a href="'.$CFG->wwwroot.'/mod/glossary/edit.php?cmid='.$cm->id
                .'" title="'.$this->config->addentry.'">'.$this->config->addentry.'</a><br />';
            } else {
                $this->content->footer = '';
            }

            $this->content->footer .= '<a href="'.$CFG->wwwroot.'/mod/glossary/view.php?id='.$cm->id
                .'" title="'.$this->config->viewglossary.'">'.$this->config->viewglossary.'</a>';

        // Otherwise just place some text, no link.
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
}

