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
 * Contains the navigation renderable for user data exports.
 *
 * @package    core_privacy
 * @copyright  2018 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_privacy\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;

/**
 * Class containing the navigation renderable
 *
 * @copyright  2018 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class exported_html_page implements renderable, templatable {

    /** @var string $navigationdata navigation html to be displayed about the system. */
    protected $navigationdata;

    /** @var string $systemname systemname for the page. */
    protected $systemname;

    /** @var string $username The full name of the user. */
    protected $username;

    /** @var bool $rtl The direction to show the page (right to left) */
    protected $rtl;

    /** @var string $siteurl The url back to the site that created this export. */
    protected $siteurl;

    /**
     * Constructor.
     *
     * @param string $navigationdata Navigation html to be displayed about the system.
     * @param string $systemname systemname for the page.
     * @param string $username The full name of the user.
     * @param bool $righttoleft Is the language used right to left?
     * @param string $siteurl The url to the site that created this export.
     */
    public function __construct(string $navigationdata, string $systemname, string $username, bool $righttoleft, string $siteurl) {
        $this->navigationdata = $navigationdata;
        $this->systemname = $systemname;
        $this->username = $username;
        $this->rtl = $righttoleft;
        $this->siteurl = $siteurl;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output) : Array {
        return [
            'navigation' => $this->navigationdata,
            'systemname' => $this->systemname,
            'timegenerated' => time(),
            'username' => $this->username,
            'righttoleft' => $this->rtl,
            'siteurl' => $this->siteurl
        ];
    }
}