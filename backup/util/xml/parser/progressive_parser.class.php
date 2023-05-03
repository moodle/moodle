<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package moodlecore
 * @subpackage backup-xml
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class implementing one SAX progressive push parser.
 *
 * SAX parser able to process XML content from files/variables. It supports
 * attributes and case folding and works only with UTF-8 content. It's one
 * progressive push parser because, intead of loading big crunchs of information
 * in memory, it "publishes" (pushes) small information in a "propietary array format" througt
 * the corresponding @progressive_parser_processor, that will be the responsibe for
 * returning information into handy formats to higher levels.
 *
 * Note that, while this progressive parser is able to process any XML file, it is
 * 100% progressive so it publishes the information in the original order it's parsed (that's
 * the expected behaviour) so information belonging to the same path can be returned in
 * different chunks if there are inner levels/paths in the middle. Be warned!
 *
 * The "propietary array format" that the parser publishes to the @progressive_parser_processor
 * is this:
 *    array (
 *        'path' => path where the tags belong to,
 *        'level'=> level (1-based) of the tags
 *        'tags  => array (
 *            'name' => name of the tag,
 *            'attrs'=> array( name of the attr => value of the attr),
 *            'cdata => cdata of the tag
 *        )
 *    )
 *
 * TODO: Finish phpdocs
 */
class progressive_parser {

    protected $xml_parser; // PHP's low level XML SAX parser
    protected $file;       // full path to file being progressively parsed | => mutually exclusive
    protected $contents;   // contents being progressively parsed          |

    /**
     * @var progressive_parser_processor to be used to publish processed information
     */
    protected $processor;

    protected $level;      // level of the current tag
    protected $path;       // path of the current tag
    protected $accum;      // accumulated char data of the current tag
    protected $attrs;      // attributes of the current tag

    protected $topush;     // array containing current level information being parsed to be "pushed"
    protected $prevlevel;  // level of the previous tag processed - to detect pushing places
    protected $currtag;    // name/value/attributes of the tag being processed

    /**
     * @var \core\progress\base Progress tracker called for each action
     */
    protected $progress;

    public function __construct($case_folding = false) {
        $this->xml_parser = xml_parser_create('UTF-8');
        xml_parser_set_option($this->xml_parser, XML_OPTION_CASE_FOLDING, $case_folding);
        xml_set_object($this->xml_parser, $this);
        xml_set_element_handler($this->xml_parser, array($this, 'start_tag'), array($this, 'end_tag'));
        xml_set_character_data_handler($this->xml_parser, array($this, 'char_data'));

        $this->file     = null;
        $this->contents = null;
        $this->level    = 0;
        $this->path     = '';
        $this->accum    = '';
        $this->attrs    = array();
        $this->topush  = array();
        $this->prevlevel = 0;
        $this->currtag   = array();
    }

    /*
     * Sets the XML file to be processed by the parser
     */
    public function set_file($file) {
        if (!file_exists($file) || (!is_readable($file))) {
            throw new progressive_parser_exception('invalid_file_to_parse');
        }
        $this->file = $file;
        $this->contents = null;
    }

    /*
     * Sets the XML contents to be processed by the parser
     */
    public function set_contents($contents) {
        if (empty($contents)) {
            throw new progressive_parser_exception('invalid_contents_to_parse');
        }
        $this->contents = $contents;
        $this->file = null;
    }

    /*
     * Define the @progressive_parser_processor in charge of processing the parsed chunks
     */
    public function set_processor($processor) {
        if (!$processor instanceof progressive_parser_processor) {
            throw new progressive_parser_exception('invalid_parser_processor');
        }
        $this->processor = $processor;
    }

    /**
     * Sets the progress tracker for the parser. If set, the tracker will be
     * called to report indeterminate progress for each chunk of XML.
     *
     * The caller should have already called start_progress on the progress tracker.
     *
     * @param \core\progress\base $progress Progress tracker
     */
    public function set_progress(\core\progress\base $progress) {
        $this->progress = $progress;
    }

    /*
     * Process the XML, delegating found chunks to the @progressive_parser_processor
     */
    public function process() {
        if (empty($this->processor)) {
            throw new progressive_parser_exception('undefined_parser_processor');
        }
        if (empty($this->file) && empty($this->contents)) {
            throw new progressive_parser_exception('undefined_xml_to_parse');
        }
        if (is_null($this->xml_parser)) {
            throw new progressive_parser_exception('progressive_parser_already_used');
        }
        if ($this->file) {
            $fh = fopen($this->file, 'r');
            while ($buffer = fread($fh, 8192)) {
                $this->parse($buffer, feof($fh));
            }
            fclose($fh);
        } else {
            $this->parse($this->contents, true);
        }
        xml_parser_free($this->xml_parser);
        $this->xml_parser = null;
    }

    /**
     * Provides one cross-platform dirname function for
     * handling parser paths, see MDL-24381
     */
    public static function dirname($path) {
        return str_replace('\\', '/', dirname($path));
    }

// Protected API starts here

    protected function parse($data, $eof) {
        if (!xml_parse($this->xml_parser, $data, $eof)) {
            throw new progressive_parser_exception(
                'xml_parsing_error', null,
                sprintf('XML error: %s at line %d, column %d',
                        xml_error_string(xml_get_error_code($this->xml_parser)),
                        xml_get_current_line_number($this->xml_parser),
                        xml_get_current_column_number($this->xml_parser)));
        }
    }

    protected function publish($data) {
        $this->processor->receive_chunk($data);
        if (!empty($this->progress)) {
            // Report indeterminate progress.
            $this->progress->progress();
        }
    }

    /**
     * Inform to the processor that we have started parsing one path
     */
    protected function inform_start($path) {
        $this->processor->before_path($path);
    }

    /**
     * Inform to the processor that we have finished parsing one path
     */
    protected function inform_end($path) {
        $this->processor->after_path($path);
    }

    protected function postprocess_cdata($data) {
        return $this->processor->process_cdata($data);
    }

    protected function start_tag($parser, $tag, $attributes) {

        // Normal update of parser internals
        $this->level++;
        $this->path .= '/' . $tag;
        $this->accum = '';
        $this->attrs = !empty($attributes) ? $attributes : array();

        // Inform processor we are about to start one tag
        $this->inform_start($this->path);

        // Entering a new inner level, publish all the information available
        if ($this->level > $this->prevlevel) {
            if (!empty($this->currtag) && (!empty($this->currtag['attrs']) || !empty($this->currtag['cdata']))) {
                // We always add the last not-empty repetition. Empty ones are ignored.
                if (isset($this->topush['tags'][$this->currtag['name']]) && trim($this->currtag['cdata']) === '') {
                    // Do nothing, the tag already exists and the repetition is empty
                } else {
                    $this->topush['tags'][$this->currtag['name']] = $this->currtag;
                }
            }
            if (!empty($this->topush['tags'])) {
                $this->publish($this->topush);
            }
            $this->currtag = array();
            $this->topush = array();
        }

        // If not set, build to push common header
        if (empty($this->topush)) {
            $this->topush['path']  = progressive_parser::dirname($this->path);
            $this->topush['level'] = $this->level;
            $this->topush['tags']  = array();
        }

        // Handling a new tag, create it
        $this->currtag['name'] = $tag;
        // And add attributes if present
        if ($this->attrs) {
            $this->currtag['attrs'] = $this->attrs;
        }

        // For the records
        $this->prevlevel = $this->level;
    }

    protected function end_tag($parser, $tag) {

        // Ending rencently started tag, add value to current tag
        if ($this->level == $this->prevlevel) {
            $this->currtag['cdata'] = $this->postprocess_cdata($this->accum);
            // We always add the last not-empty repetition. Empty ones are ignored.
            if (isset($this->topush['tags'][$this->currtag['name']]) && trim($this->currtag['cdata']) === '') {
                // Do nothing, the tag already exists and the repetition is empty
            } else {
                $this->topush['tags'][$this->currtag['name']] = $this->currtag;
            }
            $this->currtag = array();
        }

        // Leaving one level, publish all the information available
        if ($this->level < $this->prevlevel) {
            if (!empty($this->topush['tags'])) {
                $this->publish($this->topush);
            }
            $this->currtag = array();
            $this->topush = array();
        }

        // For the records
        $this->prevlevel = $this->level;

        // Inform processor we have finished one tag
        $this->inform_end($this->path);

        // Normal update of parser internals
        $this->level--;
        $this->path = progressive_parser::dirname($this->path);
    }

    protected function char_data($parser, $data) {
        $this->accum .= $data;
    }
}

/*
 * Exception class used by all the @progressive_parser stuff
 */
class progressive_parser_exception extends moodle_exception {

    public function __construct($errorcode, $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, 'error', '', $a, $debuginfo);
    }
}
