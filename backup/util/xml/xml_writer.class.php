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
 * Class implementing one (more or less complete) UTF-8 XML writer
 *
 * General purpose class used to output UTF-8 XML contents easily. Can be customized
 * using implementations of @xml_output (to define where to send the xml) and
 * and @xml_contenttransformer (to perform any transformation in contents before
 * outputting the XML).
 *
 * Has support for attributes, basic w3c xml schemas declaration,
 * and performs some content cleaning to avoid potential incorret UTF-8
 * mess and has complete exception support.
 *
 * TODO: Provide UTF-8 safe strtoupper() function if using casefolding and non-ascii tags/attrs names
 * TODO: Finish phpdocs
 */
class xml_writer {

    protected $output;     // @xml_output that defines how to output XML
    protected $contenttransformer; // @xml_contenttransformer to modify contents before output

    protected $prologue;   // Complete string prologue we want to use
    protected $xmlschema;  // URI to nonamespaceschema to be added to main tag

    protected $casefolding; // To define if xml tags must be uppercase (true) or not (false)

    protected $level;      // current number of open tags, useful for indent text
    protected $opentags;   // open tags accumulator, to check for errors
    protected $lastwastext;// to know when we are writing after text content
    protected $nullcontent;// to know if we are going to write one tag with null content

    protected $running; // To know if writer is running

    public function __construct($output, $contenttransformer = null, $casefolding = false) {
        if (!$output instanceof xml_output) {
            throw new xml_writer_exception('invalid_xml_output');
        }
        if (!is_null($contenttransformer) && !$contenttransformer instanceof xml_contenttransformer) {
            throw new xml_writer_exception('invalid_xml_contenttransformer');
        }

        $this->output = $output;
        $this->contenttransformer = $contenttransformer;

        $this->prologue  = null;
        $this->xmlschema = null;

        $this->casefolding = $casefolding;

        $this->level    = 0;
        $this->opentags = array();
        $this->lastwastext = false;
        $this->nullcontent = false;

        $this->running = null;
    }

    /**
     * Initializes the XML writer, preparing it to accept instructions, also
     * invoking the underlying @xml_output init method to be ready for operation
     */
    public function start() {
        if ($this->running === true) {
            throw new xml_writer_exception('xml_writer_already_started');
        }
        if ($this->running === false) {
            throw new xml_writer_exception('xml_writer_already_stopped');
        }
        $this->output->start(); // Initialize whatever we need in output
        if (!is_null($this->prologue)) { // Output prologue
            $this->write($this->prologue);
        } else {
            $this->write($this->get_default_prologue());
        }
        $this->running = true;
    }

    /**
     * Finishes the XML writer, not accepting instructions any more, also
     * invoking the underlying @xml_output finish method to close/flush everything as needed
     */
    public function stop() {
        if (is_null($this->running)) {
            throw new xml_writer_exception('xml_writer_not_started');
        }
        if ($this->running === false) {
            throw new xml_writer_exception('xml_writer_already_stopped');
        }
        if ($this->level > 0) { // Cannot stop if not at level 0, remaining open tags
            throw new xml_writer_exception('xml_writer_open_tags_remaining');
        }
        $this->output->stop();
        $this->running = false;
    }

    /**
     * Set the URI location for the *nonamespace* schema to be used by the (whole) XML document
     */
    public function set_nonamespace_schema($uri) {
        if ($this->running) {
            throw new xml_writer_exception('xml_writer_already_started');
        }
        $this->xmlschema = $uri;
    }

    /**
     * Define the complete prologue to be used, replacing the simple, default one
     */
    public function set_prologue($prologue) {
        if ($this->running) {
            throw new xml_writer_exception('xml_writer_already_started');
        }
        $this->prologue = $prologue;
    }

    /**
     * Outputs one XML start tag with optional attributes (name => value array)
     */
    public function begin_tag($tag, $attributes = null) {
        // TODO: chek the tag name is valid
        $pre = $this->level ? "\n" . str_repeat(' ', $this->level * 2) : ''; // Indent
        $tag = $this->casefolding ? strtoupper($tag) : $tag; // Follow casefolding
        $end = $this->nullcontent ? ' /' : ''; // Tag without content, close it

        // Build attributes output
        $attrstring = '';
        if (!empty($attributes) && is_array($attributes)) {
            // TODO: check the attr name is valid
            foreach ($attributes as $name => $value) {
                $name = $this->casefolding ? strtoupper($name) : $name; // Follow casefolding
                $attrstring .= ' ' . $name . '="'.
                    $this->xml_safe_attr_content($value) . '"';
            }
        }

        // Optional xml schema definition (level 0 only)
        $schemastring = '';
        if ($this->level == 0 && !empty($this->xmlschema)) {
            $schemastring .= "\n    " . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' .
                             "\n    " . 'xsi:noNamespaceSchemaLocation="' . $this->xml_safe_attr_content($this->xmlschema) . '"';
        }

        // Send to xml_output
        $this->write($pre . '<' . $tag . $attrstring . $schemastring . $end . '>');

        // Acumulate the tag and inc level
        if (!$this->nullcontent) {
            array_push($this->opentags, $tag);
            $this->level++;
        }
        $this->lastwastext = false;
    }

    /**
     * Outputs one XML end tag
     */
    public function end_tag($tag) {
        // TODO: check the tag name is valid

        if ($this->level == 0) { // Nothing to end, already at level 0
            throw new xml_writer_exception('xml_writer_end_tag_no_match');
        }

        $pre = $this->lastwastext ? '' : "\n" . str_repeat(' ', ($this->level - 1) * 2); // Indent
        $tag = $this->casefolding ? strtoupper($tag) : $tag; // Follow casefolding

        $lastopentag = array_pop($this->opentags);

        if ($tag != $lastopentag) {
            $a = new stdclass();
            $a->lastopen = $lastopentag;
            $a->tag = $tag;
            throw new xml_writer_exception('xml_writer_end_tag_no_match', $a);
        }

        // Send to xml_output
        $this->write($pre . '</' . $tag . '>');

        $this->level--;
        $this->lastwastext = false;
    }


    /**
     * Outputs one tag completely (open, contents and close)
     */
    public function full_tag($tag, $content = null, $attributes = null) {
        $content = $this->text_content($content); // First of all, apply transformations
        $this->nullcontent = is_null($content) ? true : false; // Is it null content
        $this->begin_tag($tag, $attributes);
        if (!$this->nullcontent) {
            $this->write($content);
            $this->lastwastext = true;
            $this->end_tag($tag);
        }
    }


// Protected API starts here

    /**
     * Send some XML formatted chunk to output.
     */
    protected function write($output) {
        $this->output->write($output);
    }

    /**
     * Get default prologue contents for this writer if there isn't a custom one
     */
    protected function get_default_prologue() {
        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    }

    /**
     * Clean attribute content and encode needed chars
     * (&, <, >, ") - single quotes not needed in this class
     * as far as we are enclosing with "
     */
    protected function xml_safe_attr_content($content) {
        return htmlspecialchars($this->xml_safe_utf8($content), ENT_COMPAT);
    }

    /**
     * Clean text content and encode needed chars
     * (&, <, >)
     */
    protected function xml_safe_text_content($content) {
        return htmlspecialchars($this->xml_safe_utf8($content), ENT_NOQUOTES);
    }

    /**
     * Perform some UTF-8 cleaning, stripping the control chars (\x0-\x1f)
     * but tabs (\x9), newlines (\xa) and returns (\xd). The delete control
     * char (\x7f) is also included. All them are forbiden in XML 1.0 specs.
     * The expression below seems to be UTF-8 safe too because it simply
     * ignores the rest of characters. Also normalize linefeeds and return chars.
     */
    protected function xml_safe_utf8($content) {
        $content = preg_replace('/[\x-\x8\xb-\xc\xe-\x1f\x7f]/is','', $content); // clean CTRL chars
        $content = preg_replace("/\r\n|\r/", "\n", $content); // Normalize line&return=>line
        return $content;
    }

    /**
     * Returns text contents processed by the corresponding @xml_contenttransformer
     */
    protected function text_content($content) {
        if (!is_null($this->contenttransformer)) { // Apply content transformation
            $content = $this->contenttransformer->process($content);
        }
        return is_null($content) ? null : $this->xml_safe_text_content($content); // Safe UTF-8 and encode
    }
}

/*
 * Exception class used by all the @xml_writer stuff
 */
class xml_writer_exception extends moodle_exception {

    public function __construct($errorcode, $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, 'error', '', $a, $debuginfo);
    }
}
