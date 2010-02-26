<?php
/**
 * Moodle - Modular Object-Oriented Dynamic Learning Environment
 *          http://moodle.org
 * Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    moodle
 * @subpackage portfolio
 * @author     Penny Leach <penny@liip.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This file contains the LEAP2a writer used by portfolio_format_leap2a
 */

/**
 * object to encapsulate the writing of leap2a.
 * should be used like:
 *
 * $writer = portfolio_format_leap2a::leap2a_writer($USER);
 * $entry = new portfolio_format_leap2a_entry('forumpost6', $title, 'leaptype', 'somecontent')
 * $entry->add_link('something', 'has_part')->add_link('somethingelse', 'has_part');
 * .. etc
 * $writer->add_entry($entry);
 * $xmlstr = $writer->to_xml();
 *
 * @TODO find a way to ensure that all referenced files are included
 */
class portfolio_format_leap2a_writer {

    /** the domdocument object used to create elements */
    private $dom;
    /** the top level feed element */
    private $feed;
    /** the user exporting data */
    private $user;
    /** the id of the feed - this is unique to the user and date and used for portfolio ns as well as feed id */
    private $id;
    /** the entries for the feed - keyed on id */
    private $entries = array();

    /**
     * constructor - usually generated from portfolio_format_leap2a::leap2a_writer($USER);
     *
     * @param stdclass $user the user exporting (almost always $USER)
     *
     */
    public function __construct(stdclass $user) { // todo something else - exporter, format, etc
        global $CFG;
        $this->user = $user;
        $this->exporttime = time();
        $this->id = $CFG->wwwroot . '/portfolio/export/leap2a/' . $this->user->id . '/' . $this->exporttime;

        $this->dom = new DomDocument('1.0', 'utf-8');

        $this->feed = $this->dom->createElement('feed');
        $this->feed->setAttribute('xmlns', 'http://www.w3.org/2005/Atom');
        $this->feed->setAttribute('xmlns:rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
        $this->feed->setAttribute('xmlns:leap', 'http://wiki.cetis.ac.uk/2009-03/LEAP2A_predicates#');
        $this->feed->setAttribute('xmlns:leaptype', 'http://wiki.cetis.ac.uk/2009-03/LEAP2A_types#');
        $this->feed->setAttribute('xmlns:categories', 'http://wiki.cetis.ac.uk/2009-03/LEAP2A_categories/');
        $this->feed->setAttribute('xmlns:portfolio', $this->id); // this is just a ns for ids of elements for convenience

        $this->dom->appendChild($this->feed);

        $this->feed->appendChild($this->dom->createElement('id', $this->id));
        $this->feed->appendChild($this->dom->createElement('title', get_string('feedtitle', 'portfolio_format_leap2a', fullname($this->user))));

        $generator = $this->dom->createElement('generator', 'Moodle');
        $generator->setAttribute('uri', $CFG->wwwroot);
        $generator->setAttribute('version', $CFG->version);

        $this->feed->appendChild($generator);

        $author = $this->dom->createElement('author');
        $author->appendChild($this->dom->createElement('name', fullname($this->user)));
        $author->appendChild($this->dom->createElement('email', $this->user->email));
        $author->appendChild($this->dom->CreateElement('uri', $CFG->wwwroot . '/user/view.php?id=' . $this->user->id));

        $this->feed->appendChild($author);
        // header done, we can start appending entry elements now
    }

    /**
     * adds a entry to the feed ready to be exported
     *
     * @param portfolio_format_leap2a_entry $entry the entry to add
     */
    public function add_entry(portfolio_format_leap2a_entry $entry) {
        if (array_key_exists($entry->id, $this->entries)) {
            throw new portfolio_format_leap2a_exception('entryalreadyexists', 'portfolio_format_leap2a', '', $entry->id);
        }
        $this->entries[$entry->id] =  $entry;
        return $entry;
    }

    /**
     * make an entry that has previously been added into the feed into a selection.
     *
     * @param mixed $selectionentry the entry to make a selection (id or entry object)
     * @param array $ids array of ids this selection includes
     * @param string $selectiontype http://wiki.cetis.ac.uk/2009-03/LEAP2A_categories/selection_type
     */
    public function make_selection($selectionentry, $ids, $selectiontype) {
        $selectionid = null;
        if ($selectionentry instanceof portfolio_format_leap2a_entry) {
            $selectionid = $selectionentry->id;
        } else if (is_string($selectionentry)) {
            $selectionid = $selectionentry;
        }
        if (!array_key_exists($selectionid, $this->entries)) {
            throw new portfolio_format_leap2a_exception('invalidentryid', 'portfolio_format_leap2a', '', $selectionid);
        }
        foreach ($ids as $entryid) {
            if (!array_key_exists($entryid, $this->entries)) {
                throw new portfolio_format_leap2a_exception('invalidentryid', 'portfolio_format_leap2a', '', $entryid);
            }
            $this->entries[$selectionid]->add_link($entryid, 'has_part');
            $this->entries[$entryid]->add_link($selectionid, 'is_part_of');
        }
        $this->entries[$selectionid]->add_category($selectiontype, 'selection_type');
        if ($this->entries[$selectionid]->type != 'selection') {
            debugging(get_string('overwritingselection', 'portfolio_format_leap2a', $this->entries[$selectionid]->type));
            $this->entries[$selectionid]->type = 'selection';
        }
    }

    /**
     * validate the feed and all entries
     */
    private function validate() {
        foreach ($this->entries as $entry) {
            // first call the entry's own validation method
            // which will throw an exception if there's anything wrong
            $entry->validate();
            // now make sure that all links are in place
            foreach ($entry->links as $linkedid => $rel) {
                // the linked to entry exists
                if (!array_key_exists($linkedid, $this->entries)) {
                    $a = (object)array('rel' => $rel->type, 'to' => $linkedid, 'from' => $entry->id);
                    throw new portfolio_format_leap2a_exception('nonexistantlink', 'portfolio_format_leap2a', '', $a);
                }
                // and contains a link back to us
                if (!array_key_exists($entry->id, $this->entries[$linkedid]->links)) {

                }
                // we could later check that the reltypes were properly inverse, but nevermind for now.
            }
        }
    }

    /**
     * return the entire feed as a string
     * calls validate() first on everything
     *
     * @return string
     */
    public function to_xml() {
        $this->validate();
        foreach ($this->entries as $entry) {
            $this->feed->appendChild($entry->to_dom($this->dom, $this->user));
        }
        return $this->dom->saveXML();
    }
}

/**
 * this class represents a single leap2a entry.
 * you can create these directly and then add them to the main leap feed object
 */
class portfolio_format_leap2a_entry {

    /** entry id  - something like forumpost6, must be unique to the feed **/
    public $id;
    /** title of the entry **/
    public $title;
    /** leap2a entry type **/
    public $type;
    /** optional author (only if different to feed author) **/
    public $author;
    /** summary - for split long content **/
    public $summary;
    /** main content of the entry. can be html,text,xhtml or a stored_file **/
    public $content;
    /** updated date - unix timestamp */
    public $updated;
    /** published date (ctime) - unix timestamp */
    public $published;

    /** used internally for file content **/
    private $contentsrc;
    /** used internally for file content **/
    private $referencedfile;

    /** the required fields for a leap2a entry */
    private $requiredfields = array( 'id', 'title', 'type');

    /** extra fields which usually should be set (except author) but are not required */
    private $optionalfields = array('author', 'updated', 'published', 'content', 'summary');

    /** links from this entry to other entries */
    public $links       = array();

    /** attachments to this entry */
    public $attachments = array();

    /** categories for this entry */
    private $categories = array();

    /**
     * constructor.  All arguments are required (and will be validated)
     * http://wiki.cetis.ac.uk/2009-03/LEAP2A_types
     *
     * @param string $id unique id of this entry.
     *                   could be something like forumpost6 for example.
     *                   This <b>must</b> be unique to the entire feed.
     * @param string $title title of the entry. This is pure atom.
     * @param string $type the leap type of this entry.
     * @param mixed $content the content of the entry. string (xhtml/html/text) or stored_file
     */
    public function __construct($id, $title, $type, $content=null) {
        $this->id    = $id;
        $this->title = $title;
        $this->type  = $type;
        $this->content = $this->__set('content', $content);

    }

    /**
     * override __set to do proper dispatching for different things
     * only allows the optional and required leap2a entry fields to be set
     */
    public function __set($field, $value) {
        // detect the case where content is being set to be a file directly
        if ($field == 'content' && $value instanceof stored_file) {
            return $this->set_content_file($value);
        }
        if (in_array($field, $this->requiredfields) || in_array($field, $this->optionalfields)) {
            return $this->{$field} = $value;
        }
        throw new portfolio_format_leap2a_exception('invalidentryfield', 'portfolio_format_leap2a', '', $field);
    }

    /**
     * sets the content of this entry to have a source
     * this will take care of namespacing the filepath
     * to the final path in the resulting zip file.
     *
     * @param stored_file $file the file to link to
     * @param boolean $overridetype (default true) will set the entry rdf type to resource,
     *                               overriding what was previously set.
     *                               will be ignored if type is empty already
     */
    public function set_content_file(stored_file $file, $overridetype=true) {
        $this->contentsrc = portfolio_format_leap2a::get_file_directory() . $file->get_filename();
        if (empty($overridetype) || empty($this->type)) {
            $this->type = 'resource';
        }
        $this->referencedfile = $file;
    }

    /**
     * validate this entry.
     * at the moment this just makes sure required fields exist
     * but it could also check things against a list, for example
     */
    public function validate() {
        foreach ($this->requiredfields as $key) {
            if (empty($this->{$key})) {
                throw new portfolio_format_leap2a_exception('missingfield', 'portfolio_format_leap2a', '', $key);
            }
        }
        if ($this->type == 'selection') {
            if (count($this->links) == 0) {
                throw new portfolio_format_leap2a_exception('emptyselection', 'portfolio_format_leap2a');
            }
            //TODO make sure we have a category with a scheme 'selection_type'
        }
    }

    /**
     * add a link from this entry to another one
     * these will be collated at the end of the export (during to_xml)
     * and validated at that point. This function does no validation
     * http://wiki.cetis.ac.uk/2009-03/LEAP2A_relationships
     *
     * @param mixed $otherentry portfolio_format_leap2a_entry or its id
     * @param string $reltype (no leap: ns required)
     *
     * @return the current entry object. This is so that these calls can be chained
     * eg $entry->add_link('something6', 'has_part')->add_link('something7', 'has_part');
     *
     */
    public function add_link($otherentry, $reltype, $displayorder=null) {
        if ($otherentry instanceof portfolio_format_leap2a_entry) {
            $otherentry = $otherentry->id;
        }
        if ($otherentry == $this->id) {
            throw new portfolio_format_leap2a_exception('selflink', 'portfolio_format_leap2a', '', (object)array('rel' => $reltype, 'id' => $this->id));
        }
        // add on the leap: ns if required
        if (!in_array($reltype, array('related', 'alternate', 'enclosure'))) {
            $reltype = 'leap:' . $reltype;
        }

        $this->links[$otherentry] = (object)array('rel' => $reltype, 'order' => $displayorder);

        return $this;
    }

    /**
     * add an attachment to the feed.
     * adding the file to the files area has to be handled outside this class separately.
     *
     * @param stored_file $file the file to add
     */
    public function add_attachment(stored_file $file) {
        $this->attachments[$file->get_id()] = $file;
    }

    /**
     * helper function to add a bunch of files at the same time
     * useful for passing $this->multifiles straight through from the portfolio_caller
     */
    public function add_attachments(array $files) {
        foreach ($files as $file) {
            $this->add_attachment($file);
        }
    }

    /**
     * add a category to this entry
     * http://wiki.cetis.ac.uk/2009-03/LEAP2A_categories
     *
     * @param string $term eg 'Offline'
     * @param string $scheme (optional) eg resource_type
     * @param string $label (optional) eg File
     *
     * "tags" should just pass a term here and no scheme or label.
     * they will be automatically normalised if they have spaces.
     */
    public function add_category($term, $scheme=null, $label=null) {
        // "normalise" terms and set their label if they have spaces
        // see http://wiki.cetis.ac.uk/2009-03/LEAP2A_categories#Plain_tags for more information
        if (empty($scheme) && strpos($term, ' ') !== false) {
            $label = $term;
            $term = str_replace(' ', '-', $term);
        }
        $this->categories[] = (object)array(
            'term'   => $term,
            'scheme' => $scheme,
            'label'  => $label,
        );
    }

    /**
     * Create an entry element and append all the children
     * And return it rather than adding it to the dom.
     * This is handled by the main writer object.
     *
     * @param DomDocument $dom use this to create elements
     *
     * @return DomElement
     */
    public function to_dom(DomDocument $dom, $feedauthor) {
        $entry = $dom->createElement('entry');
        $entry->appendChild($dom->createElement('id', $this->id));
        $entry->appendChild($dom->createElement('title', $this->title));
        if ($this->author && $this->author->id != $feedauthor->id) {
            $author = $dom->createElement('author');
            $author->appendChild($dom->createElement('name', fullname($this->author)));
            $entry->appendChild($author);
        }
        // selectively add uncomplicated optional elements
        foreach (array('updated', 'published') as $field) {
            if ($this->{$field}) {
                $date = date(DATE_ATOM, $this->{$field});
                $entry->appendChild($dom->createElement($field, $date));
            }
        }
        // deal with referenced files first since it's simple
        if ($this->contentsrc) {
            $content = $dom->createElement('content');
            $content->setAttribute('src', $this->contentsrc);
            $content->setAttribute('type', $this->referencedfile->get_mimetype());
            $entry->appendChild($content);
        } else if (empty($this->content)) {
            $entry->appendChild($dom->createElement('content'));
        } else {
            $content = $this->create_xhtmlish_element($dom, 'content', $this->content);
            $entry->appendChild($content);
        }

        if (!empty($this->summary)) {
            $summary = $this->create_xhtmlish_element($dom, 'summary', $this->summary);
            $entry->appendChild($summary);
        }

        $type = $dom->createElement('rdf:type');
        $type->setAttribute('rdf:resource', 'leaptype:' . $this->type);
        $entry->appendChild($type);

        foreach ($this->links as $otherentry => $l) {
            $link = $dom->createElement('link');
            $link->setAttribute('rel',  $l->rel);
            $link->setAttribute('href', $otherentry);
            if ($l->order) {
                $link->setAttribute('leap:display_order', $l->order);
            }
            $entry->appendChild($link);
        }
        foreach ($this->attachments as $id => $file) {
            $link = $dom->createElement('link');
            $link->setAttribute('rel',  'enclosure');
            $link->setAttribute('href', portfolio_format_leap2a::get_file_directory() . $file->get_filename());
            $link->setAttribute('length', $file->get_filesize());
            $entry->appendChild($link);
        }
        foreach ($this->categories as $category) {
            $cat = $dom->createElement('category');
            $cat->setAttribute('term', $category->term);
            if ($category->scheme) {
                $cat->setAttribute('scheme', $category->scheme);
            }
            if ($category->label && $category->label != $category->term) {
                $cat->setAttribute('label', $category->label);
            }
            $entry->appendChild($cat);
        }
        return $entry;
    }

    /**
     * try to load whatever is in $content into xhtml and add it to the dom.
     * failing that, load the html, escape it, and set it as the body of the tag
     * either way it sets the type attribute of the top level element
     * moodle should always provide xhtml content, but user-defined content can't be trusted
     *
     * @param DomDocument $dom the dom doc to use
     * @param string $tagname usually 'content' or 'summary'
     * @param string $content the content to use, either xhtml or html.
     *
     * @return DomElement
     */
    private function create_xhtmlish_element(DomDocument $dom, $tagname, $content) {
        $topel = $dom->createElement($tagname);
        $maybexml = true;
        if (strpos($content, '<') === false && strpos($content, '>') === false) {
            $maybexml = false;
        }
        // try to load content as xml
        $tmp = new DomDocument();
        if ($maybexml && @$tmp->loadXML('<div>' . $content . '</div>')) {
            $topel->setAttribute('type', 'xhtml');
            $content = $dom->importNode($tmp->documentElement, true);
            $content->setAttribute('xmlns', 'http://www.w3.org/1999/xhtml');
            $topel->appendChild($content);
        // if that fails, it could still be html
        } else if ($maybexml && @$tmp->loadHTML($content)) {
            $topel->setAttribute('type', 'html');
            $topel->nodeValue = $content;
            // TODO figure out how to convert this to xml
            // TODO because we end up with <html><body> </body></html> wrapped around it
            // which is annoying
        // either we already know it's text from the first check
        // or nothing else has worked anyway
        } else {
            $topel->nodeValue = $content;
            $topel->setAttribute('type', 'text');
            return $topel;
        }
        return $topel;
    }
}
