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

namespace core\output;

use core\exception\coding_exception;
use moodle_page;
use moodle_url;
use stdClass;

/**
 * Component representing a paging bar.
 *
 * @copyright 2009 Nicolas Connault
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class paging_bar implements renderable, templatable {
    /**
     * @var int The maximum number of pagelinks to display.
     */
    public $maxdisplay = 18;

    /**
     * @var int The total number of entries to be pages through..
     */
    public $totalcount;

    /**
     * @var int The page you are currently viewing.
     */
    public $page;

    /**
     * @var int The number of entries that should be shown per page.
     */
    public $perpage;

    /**
     * @var string|moodle_url If this  is a string then it is the url which will be appended with $pagevar,
     * an equals sign and the page number.
     * If this is a moodle_url object then the pagevar param will be replaced by
     * the page no, for each page.
     */
    public $baseurl;

    /**
     * @var string This is the variable name that you use for the pagenumber in your
     * code (ie. 'tablepage', 'blogpage', etc)
     */
    public $pagevar;

    /**
     * @var string A HTML link representing the "previous" page.
     */
    public $previouslink = null;

    /**
     * @var string A HTML link representing the "next" page.
     */
    public $nextlink = null;

    /**
     * @var string A HTML link representing the first page.
     */
    public $firstlink = null;

    /**
     * @var string A HTML link representing the last page.
     */
    public $lastlink = null;

    /**
     * @var array An array of strings. One of them is just a string: the current page
     */
    public $pagelinks = [];

    /**
     * Constructor paging_bar with only the required params.
     *
     * @param int $totalcount The total number of entries available to be paged through
     * @param int $page The page you are currently viewing
     * @param int $perpage The number of entries that should be shown per page
     * @param string|moodle_url $baseurl url of the current page, the $pagevar parameter is added
     * @param string $pagevar name of page parameter that holds the page number
     */
    public function __construct($totalcount, $page, $perpage, $baseurl, $pagevar = 'page') {
        $this->totalcount = $totalcount;
        $this->page       = $page;
        $this->perpage    = $perpage;
        $this->baseurl    = $baseurl;
        $this->pagevar    = $pagevar;
    }

    /**
     * Prepares the paging bar for output.
     *
     * This method validates the arguments set up for the paging bar and then
     * produces fragments of HTML to assist display later on.
     *
     * @param renderer_base $output
     * @param moodle_page $page
     * @param string $target
     * @throws coding_exception
     */
    public function prepare(renderer_base $output, moodle_page $page, $target) {
        if (!isset($this->totalcount) || is_null($this->totalcount)) {
            throw new coding_exception('paging_bar requires a totalcount value.');
        }
        if (!isset($this->page) || is_null($this->page)) {
            throw new coding_exception('paging_bar requires a page value.');
        }
        if (empty($this->perpage)) {
            throw new coding_exception('paging_bar requires a perpage value.');
        }
        if (empty($this->baseurl)) {
            throw new coding_exception('paging_bar requires a baseurl value.');
        }

        if ($this->totalcount > $this->perpage) {
            $pagenum = $this->page - 1;

            if ($this->page > 0) {
                $this->previouslink = html_writer::link(
                    new moodle_url($this->baseurl, [$this->pagevar => $pagenum]),
                    get_string('previous'),
                    ['class' => 'previous'],
                );
            }

            if ($this->perpage > 0) {
                $lastpage = ceil($this->totalcount / $this->perpage);
            } else {
                $lastpage = 1;
            }

            if ($this->page > round(($this->maxdisplay / 3) * 2)) {
                $currpage = $this->page - round($this->maxdisplay / 3);

                $this->firstlink = html_writer::link(
                    new moodle_url($this->baseurl, [$this->pagevar => 0]),
                    '1',
                    ['class' => 'first'],
                );
            } else {
                $currpage = 0;
            }

            $displaycount = $displaypage = 0;

            while ($displaycount < $this->maxdisplay && $currpage < $lastpage) {
                $displaypage = $currpage + 1;

                if ($this->page == $currpage) {
                    $this->pagelinks[] = html_writer::span($displaypage, 'current-page');
                } else {
                    $pagelink = html_writer::link(new moodle_url($this->baseurl, [$this->pagevar => $currpage]), $displaypage);
                    $this->pagelinks[] = $pagelink;
                }

                $displaycount++;
                $currpage++;
            }

            if ($currpage < $lastpage) {
                $lastpageactual = $lastpage - 1;
                $this->lastlink = html_writer::link(
                    new moodle_url($this->baseurl, [$this->pagevar => $lastpageactual]),
                    $lastpage,
                    ['class' => 'last'],
                );
            }

            $pagenum = $this->page + 1;

            if ($pagenum != $lastpage) {
                $this->nextlink = html_writer::link(
                    new moodle_url($this->baseurl, [$this->pagevar => $pagenum]),
                    get_string('next'),
                    ['class' => 'next'],
                );
            }
        }
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output The renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->previous = null;
        $data->next = null;
        $data->first = null;
        $data->last = null;
        $data->label = get_string('page');
        $data->pages = [];
        $data->haspages = $this->totalcount > $this->perpage;
        $data->pagesize = $this->perpage;

        if (!$data->haspages) {
            return $data;
        }

        if ($this->page > 0) {
            $data->previous = [
                'page' => $this->page,
                'url' => (new moodle_url($this->baseurl, [$this->pagevar => $this->page - 1]))->out(false),
            ];
        }

        $currpage = 0;
        if ($this->page > round(($this->maxdisplay / 3) * 2)) {
            $currpage = $this->page - round($this->maxdisplay / 3);
            $data->first = [
                'page' => 1,
                'url' => (new moodle_url($this->baseurl, [$this->pagevar => 0]))->out(false),
            ];
        }

        $lastpage = 1;
        if ($this->perpage > 0) {
            $lastpage = ceil($this->totalcount / $this->perpage);
        }

        $displaycount = 0;
        $displaypage = 0;
        while ($displaycount < $this->maxdisplay && $currpage < $lastpage) {
            $displaypage = $currpage + 1;

            $iscurrent = $this->page == $currpage;
            $link = new moodle_url($this->baseurl, [$this->pagevar => $currpage]);

            $data->pages[] = [
                'page' => $displaypage,
                'active' => $iscurrent,
                'url' => $iscurrent ? null : $link->out(false),
            ];

            $displaycount++;
            $currpage++;
        }

        if ($currpage < $lastpage) {
            $data->last = [
                'page' => $lastpage,
                'url' => (new moodle_url($this->baseurl, [$this->pagevar => $lastpage - 1]))->out(false),
            ];
        }

        if ($this->page + 1 != $lastpage) {
            $data->next = [
                'page' => $this->page + 2,
                'url' => (new moodle_url($this->baseurl, [$this->pagevar => $this->page + 1]))->out(false),
            ];
        }

        return $data;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(paging_bar::class, \paging_bar::class);
