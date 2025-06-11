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
 * This file contains the LEAP2a writer used by portfolio_format_leap2a
 *
 * @package core_portfolio
 * @copyright 2009 Penny Leach (penny@liip.ch), Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Object to encapsulate the writing of leap2a.
 *
 * Should be used like:
 * $writer = portfolio_format_leap2a::leap2a_writer($USER);
 * $entry = new portfolio_format_leap2a_entry('forumpost6', $title, 'leap2', 'somecontent')
 * $entry->add_link('something', 'has_part')->add_link('somethingelse', 'has_part');
 * .. etc
 * $writer->add_entry($entry);
 * $xmlstr = $writer->to_xml();
 *
 * @todo MDL-31287 - find a way to ensure that all referenced files are included
 * @package core_portfolio
 * @category portfolio
 * @copyright 2009 Penny Leach
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class portfolio_format_leap2a_writer {

    /** @var DomDocument the domdocument object used to create elements */
    private $dom;

    /** @var DOMElement the top level feed element */
    private $feed;

    /** @var stdClass the user exporting data */
    private $user;

    /** @var array the entries for the feed - keyed on id */
    private $entries = array();

    /**
     * Constructor - usually generated from portfolio_format_leap2a::leap2a_writer($USER);
     *
     * @todo MDL-31302 - add exporter and format
     * @param stdclass $user the user exporting (almost always $USER)
     */
    public function __construct(stdclass $user) { // todo something else - exporter, format, etc
        global $CFG;
        $this->user = $user;
        $id = $CFG->wwwroot . '/portfolio/export/leap2a/' . $this->user->id . '/' . time();

        $this->dom = new DomDocument('1.0', 'utf-8');

        $this->feed = $this->dom->createElement('feed');
        $this->feed->setAttribute('xmlns', 'http://www.w3.org/2005/Atom');
        $this->feed->setAttribute('xmlns:rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
        $this->feed->setAttribute('xmlns:leap2', 'http://terms.leapspecs.org/');
        $this->feed->setAttribute('xmlns:categories', 'http://wiki.leapspecs.org/2A/categories');
        $this->feed->setAttribute('xmlns:portfolio', $id); // This is just a ns for ids of elements for convenience.

        $this->dom->appendChild($this->feed);

        $this->feed->appendChild($this->dom->createElement('id', $id));
        $this->feed->appendChild($this->dom->createElement('title', get_string('leap2a_feedtitle', 'portfolio', fullname($this->user))));
        $this->feed->appendChild($this->dom->createElement('leap2:version', 'http://www.leapspecs.org/2010-07/2A/'));


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
     * Adds a entry to the feed ready to be exported
     *
     * @param portfolio_format_leap2a_entry $entry new feed entry to add
     * @return portfolio_format_leap2a_entry
     */
    public function add_entry(portfolio_format_leap2a_entry $entry) {
        if (array_key_exists($entry->id, $this->entries)) {
            if (!($entry instanceof portfolio_format_leap2a_file)) {
                throw new portfolio_format_leap2a_exception('leap2a_entryalreadyexists', 'portfolio', '', $entry->id);
            }
        }
        $this->entries[$entry->id] =  $entry;
        return $entry;
    }

    /**
     * Select an entry that has previously been added into the feed
     *
     * @param portfolio_format_leap2a_entry|string $selectionentry the entry to make a selection (id or entry object)
     * @param array $ids array of ids this selection includes
     * @param string $selectiontype for selection type, see: http://wiki.cetis.ac.uk/2009-03/LEAP2A_categories/selection_type
     */
    public function make_selection($selectionentry, $ids, $selectiontype) {
        $selectionid = null;
        if ($selectionentry instanceof portfolio_format_leap2a_entry) {
            $selectionid = $selectionentry->id;
        } else if (is_string($selectionentry)) {
            $selectionid = $selectionentry;
        }
        if (!array_key_exists($selectionid, $this->entries)) {
            throw new portfolio_format_leap2a_exception('leap2a_invalidentryid', 'portfolio', '', $selectionid);
        }
        foreach ($ids as $entryid) {
            if (!array_key_exists($entryid, $this->entries)) {
                throw new portfolio_format_leap2a_exception('leap2a_invalidentryid', 'portfolio', '', $entryid);
            }
            $this->entries[$selectionid]->add_link($entryid, 'has_part');
            $this->entries[$entryid]->add_link($selectionid, 'is_part_of');
        }
        $this->entries[$selectionid]->add_category($selectiontype, 'selection_type');
        if ($this->entries[$selectionid]->type != 'selection') {
            debugging(get_string('leap2a_overwritingselection', 'portfolio', $this->entries[$selectionid]->type));
            $this->entries[$selectionid]->type = 'selection';
        }
    }

    /**
     * Helper function to link some stored_files into the feed and link them to a particular entry
     *
     * @param portfolio_format_leap2a_entry $entry feed object
     * @param array $files array of stored_files to link
     */
    public function link_files($entry, $files) {
        foreach ($files as $file) {
            $fileentry = new portfolio_format_leap2a_file($file->get_filename(), $file);
            $this->add_entry($fileentry);
            $entry->add_link($fileentry, 'related');
            $fileentry->add_link($entry, 'related');
        }
    }

    /**
     * Validate the feed and all entries
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
                    throw new portfolio_format_leap2a_exception('leap2a_nonexistantlink', 'portfolio', '', $a);
                }
                // and contains a link back to us
                if (!array_key_exists($entry->id, $this->entries[$linkedid]->links)) {

                }
                // we could later check that the reltypes were properly inverse, but nevermind for now.
            }
        }
    }

    /**
     * Return the entire feed as a string.
     * Then, it calls for validation
     *
     * @return string feeds' content in xml
     */
    public function to_xml() {
        $this->validate();
        foreach ($this->entries as $entry) {
            $entry->id = 'portfolio:' . $entry->id;
            $this->feed->appendChild($entry->to_dom($this->dom, $this->user));
        }
        return $this->dom->saveXML();
    }
}

/**
 * This class represents a single leap2a entry.
 *
 * You can create these directly and then add them to the main leap feed object
 *
 * @package core_portfolio
 * @category portfolio
 * @copyright 2009 Penny Leach
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class portfolio_format_leap2a_entry {

    /** @var string entry id  - something like forumpost6, must be unique to the feed */
    public $id;

    /** @var string title of the entry */
    public $title;

    /** @var string leap2a entry type */
    public $type;

    /** @var string optional author (only if different to feed author) */
    public $author;

    /** @var string summary - for split long content */
    public $summary;

    /** @var mixed main content of the entry. can be html,text,or xhtml. for a stored_file, use portfolio_format_leap2a_file **/
    public $content;

    /** @var int updated date - unix timestamp */
    public $updated;

    /** @var int published date (ctime) - unix timestamp */
    public $published;

    /** @var array the required fields for a leap2a entry */
    private $requiredfields = array( 'id', 'title', 'type');

    /** @var array extra fields which usually should be set (except author) but are not required */
    private $optionalfields = array('author', 'updated', 'published', 'content', 'summary');

    /** @var array links from this entry to other entries */
    public $links       = array();

    /** @var array attachments to this entry */
    public $attachments = array();

    /** @var array categories for this entry */
    private $categories = array();

    /**
     * Constructor.  All arguments are required (and will be validated)
     * http://wiki.cetis.ac.uk/2009-03/LEAP2A_types
     *
     * @param string $id unique id of this entry.
     *                   could be something like forumpost6 for example.
     *                   This <b>must</b> be unique to the entire feed.
     * @param string $title title of the entry. This is pure atom.
     * @param string $type the leap type of this entry.
     * @param mixed $content the content of the entry. string (xhtml/html/text)
     */
    public function __construct($id, $title, $type, $content=null) {
        $this->id    = $id;
        $this->title = $title;
        $this->type  = $type;
        $this->content = $this->__set('content', $content);

    }

    /**
     * Override __set to do proper dispatching for different things.
     * Only allows the optional and required leap2a entry fields to be set
     *
     * @param string $field property's name
     * @param mixed $value property's value
     * @return mixed
     */
    public function __set($field, $value) {
        // detect the case where content is being set to be a file directly
        if ($field == 'content' && $value instanceof stored_file) {
            throw new portfolio_format_leap2a_exception('leap2a_filecontent', 'portfolio');
        }
        if (in_array($field, $this->requiredfields) || in_array($field, $this->optionalfields)) {
            return $this->{$field} = $value;
        }
        throw new portfolio_format_leap2a_exception('leap2a_invalidentryfield', 'portfolio', '', $field);
    }


    /**
     * Validate this entry.
     * At the moment this just makes sure required fields exist
     * but it could also check things against a list, for example
     *
     * @todo MDL-31303 - add category with a scheme 'selection_type'
     */
    public function validate() {
        foreach ($this->requiredfields as $key) {
            if (empty($this->{$key})) {
                throw new portfolio_format_leap2a_exception('leap2a_missingfield', 'portfolio', '', $key);
            }
        }
        if ($this->type == 'selection') {
            if (count($this->links) == 0) {
                throw new portfolio_format_leap2a_exception('leap2a_emptyselection', 'portfolio');
            }
            //TODO make sure we have a category with a scheme 'selection_type'
        }
    }

    /**
     * Add a link from this entry to another one.
     * These will be collated at the end of the export (during to_xml)
     * and validated at that point. This function does no validation
     * {@link http://wiki.cetis.ac.uk/2009-03/LEAP2A_relationships}
     *
     * @param portfolio_format_leap2a_entry|string $otherentry portfolio_format_leap2a_entry or its id
     * @param string $reltype (no leap2: ns required)
     * @param string $displayorder (optional)
     * @return portfolio_format_leap2a_entry the current entry object. This is so that these calls can be chained
     *                                       eg $entry->add_link('something6', 'has_part')->add_link('something7',
     *                                       'has_part');
     */
    public function add_link($otherentry, $reltype, $displayorder=null) {
        if ($otherentry instanceof portfolio_format_leap2a_entry) {
            $otherentry = $otherentry->id;
        }
        if ($otherentry == $this->id) {
            throw new portfolio_format_leap2a_exception('leap2a_selflink', 'portfolio', '', (object)array('rel' => $reltype, 'id' => $this->id));
        }
        // add on the leap2: ns if required
        if (!in_array($reltype, array('related', 'alternate', 'enclosure'))) {
            $reltype = 'leap2:' . $reltype;
        }

        $this->links[$otherentry] = (object)array('rel' => $reltype, 'order' => $displayorder);

        return $this;
    }

    /**
     * Add a category to this entry
     * {@link http://wiki.cetis.ac.uk/2009-03/LEAP2A_categories}
     * "tags" should just pass a term here and no scheme or label.
     * They will be automatically normalised if they have spaces.
     *
     * @param string $term eg 'Offline'
     * @param string $scheme (optional) eg resource_type
     * @param string $label (optional) eg File
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
     * @param stdClass $feedauthor object of author(user) info
     * @return DOMDocument
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
        if (empty($this->content)) {
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
        $type->setAttribute('rdf:resource', 'leap2:' . $this->type);
        $entry->appendChild($type);

        foreach ($this->links as $otherentry => $l) {
            $link = $dom->createElement('link');
            $link->setAttribute('rel',  $l->rel);
            $link->setAttribute('href', 'portfolio:' . $otherentry);
            if ($l->order) {
                $link->setAttribute('leap2:display_order', $l->order);
            }
            $entry->appendChild($link);
        }

        $this->add_extra_links($dom, $entry); // hook for subclass

        foreach ($this->categories as $category) {
            $cat = $dom->createElement('category');
            $cat->setAttribute('term', $category->term);
            if ($category->scheme) {
                $cat->setAttribute('scheme', 'categories:' .$category->scheme . '#');
            }
            if ($category->label && $category->label != $category->term) {
                $cat->setAttribute('label', $category->label);
            }
            $entry->appendChild($cat);
        }
        return $entry;
    }

    /**
     * Try to load whatever is in $content into xhtml and add it to the dom.
     * Failing that, load the html, escape it, and set it as the body of the tag.
     * Either way it sets the type attribute of the top level element.
     * Moodle should always provide xhtml content, but user-defined content can't be trusted
     *
     * @todo MDL-31304 - convert <html><body> </body></html> to xml
     * @param DomDocument $dom the dom doc to use
     * @param string $tagname usually 'content' or 'summary'
     * @param string $content the content to use, either xhtml or html.
     * @return DomDocument
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

    /**
     * Hook function for subclasses to add extra links (like for files)
     *
     * @param DomDocument $dom feed object
     * @param DomDocument $entry feed added link
     */
    protected function add_extra_links($dom, $entry) {}
}

/**
 * Subclass of entry, purely for dealing with files
 *
 * @package core_portfolio
 * @category portfolio
 * @copyright 2009 Penny Leach
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class portfolio_format_leap2a_file extends portfolio_format_leap2a_entry {

    /** @var file_stored for the dealing file */
    protected $referencedfile;

    /**
     * Overridden constructor to set up the file.
     *
     * @param string $title title of the entry
     * @param stored_file $file file storage instance
     */
    public function __construct($title, stored_file $file) {
        $id = portfolio_format_leap2a::file_id_prefix() . $file->get_id();
        parent::__construct($id, $title, 'resource');
        $this->referencedfile = $file;
        $this->published = $this->referencedfile->get_timecreated();
        $this->updated = $this->referencedfile->get_timemodified();
        $this->add_category('offline', 'resource_type');
    }

    /**
     * Implement the hook to add extra links to attach the file in an enclosure
     *
     * @param DomDocument $dom feed object
     * @param DomDocument $entry feed added link
     */
    protected function add_extra_links($dom, $entry) {
        $link = $dom->createElement('link');
        $link->setAttribute('rel',  'enclosure');
        $link->setAttribute('href', portfolio_format_leap2a::get_file_directory() . $this->referencedfile->get_filename());
        $link->setAttribute('length', $this->referencedfile->get_filesize());
        $link->setAttribute('type', $this->referencedfile->get_mimetype());
        $entry->appendChild($link);
    }
}

