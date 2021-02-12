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
 * @package   local_iomad
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/local/iomad/lib/user.php');

/**
 * helper class to minimize duplicate code
 */
class blockpage {
    public $blockname;
    public $blocktype;

    protected $page = null;
    protected $output = null;
    protected $pagetitle = null;

    /**
     * Constructor.  Sets up all the class variables
     *
     **/
    public function __construct($page, $output, $blockname, $blocktype, $pagetitlestring) {
        $this->page =& $page;
        $this->OUTPUT =& $output;
        $this->blockname = $blockname;
        $this->blocktype = $blocktype;
        $this->pagetitle = self::get_string($pagetitlestring);
    }

    /**
     * performs a language look up for a a specified key.
     *
     * Paramters -
     *             $key = text thing to be looked in $string array.
     *             $a = text = Any additional text to be added to the get_string() lookup.
     *
     * returns text;
     *
     **/
    public function get_string($key, $a = null) {
        return get_string($key, $this->blocktype ."_". $this->blockname, $a);
    }

    /**
     * Creates a URL using the relative URL path.
     *
     * Paramters -
     *             $urlparams = array of parameters and values.
     *
     * returns text;
     *
     **/
    public function get_relative_url( $urlparams=null) {
        $relpath = $_SERVER['REQUEST_URI'];
        // Create list of any parameters.
        $paramlist = "";
        if (!empty($urlparams)) {
            $paramlist = "?";
            foreach ($urlparams as $parameter => $value) {
                $paramlist .= $parameter .'='. $value ."&";
            }
        }
        // Check $this->blocktype and make sure it works for block/blocks.
        if ($this->blocktype == "block" ) {
            $myblockdir = 'blocks';
        } else {
            $myblockdir = $this->blocktype;
        }
        // Strip the querystring of the URL and anything before /blocks/.
        return preg_replace( "/^.*?\/". $myblockdir ."\/" . $this->blockname . "\//", "/" .
                              $myblockdir . "/" . $this->blockname . "/",
                              preg_replace("/\?.*$/", "", $relpath)) . $paramlist;
    }

    /**
     * Sets up the page
     *
     * Paramters -
     *             $urlparams = array().
     *
     **/
    public function setup($urlparams=null) {
        global $USER;

        // All iomad_company_admin pages require login.
        require_login(null, false); // Adds to $PAGE, creates $OUTPUT.

        // Make sure a company user can not retrieve pages for other companies.
        $companyid = optional_param('companyid', 0, PARAM_INTEGER);
        if ($companyid && !company_user::can_see_company($companyid)) {
            throw new Exception(self::get_string('notallowedtoaccessothercompaniesdata'));
        }

        $this->page->set_url($this->get_relative_url($urlparams));
        $this->page->set_context(context_system::instance());
        $this->page->set_pagelayout('mydashboard');

        $blocktitle = self::get_string('blocktitle');

        /**
         * *Think* this bit is deprecated
        $entryurl = optional_param('entryurl', '', PARAM_LOCALURL);
        $entrytitle = optional_param('entrytitle', '', PARAM_TEXT);

        if ( $entryurl || $entrytitle ) {
            if ( !isset($USER->iomad) ) {
                $USER->iomad = new stdClass();
            }

            if ( $entryurl == (new moodle_url('/')) ) {
                unset($USER->iomad->entrypoint);
                unset($USER->iomad->entrytitle);
            } else {
                $USER->iomad->entrypoint = $entryurl;
                $USER->iomad->entrytitle = $entrytitle;
            }
        }
        */

        $this->page->set_title($this->pagetitle);
        $this->page->set_heading($blocktitle);
    }

    /**
     * Display the page header using the class settings.
     *
     **/
    public function display_header() {

        echo $this->OUTPUT->header();
        //echo $this->OUTPUT->heading($this->pagetitle, 2, 'headingblock header');
    }
}
