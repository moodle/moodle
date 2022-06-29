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
 * This file contains the definition for the renderable assign header.
 *
 * @package   mod_assign
 * @copyright 2020 Matt Porritt <mattp@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_assign\output;

/**
 * This file contains the definition for the renderable assign header.
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_header implements \renderable {
    /** @var \stdClass The assign record.  */
    public $assign;
    /** @var mixed \context|null the context record.  */
    public $context;
    /** @var bool $showintro Show or hide the intro. */
    public $showintro;
    /** @var int coursemoduleid The course module id. */
    public $coursemoduleid;
    /** @var string $subpage Optional subpage (extra level in the breadcrumbs). */
    public $subpage;
    /** @var string $preface Optional preface (text to show before the heading). */
    public $preface;
    /** @var string $postfix Optional postfix (text to show after the intro). */
    public $postfix;
    /** @var \moodle_url|null $subpageurl link for the sub page */
    public $subpageurl;
    /** @var bool $activity optional show activity text. */
    public $activity;

    /**
     * Constructor
     *
     * @param \stdClass        $assign          The assign database record.
     * @param \context|null    $context         The course module context.
     * @param bool             $showintro       Show or hide the intro.
     * @param int              $coursemoduleid  The course module id.
     * @param string           $subpage         An optional sub page in the navigation.
     * @param string           $preface         An optional preface to show before the heading.
     * @param string           $postfix         An optional postfix to show after the intro.
     * @param \moodle_url|null $subpageurl      An optional sub page URL link for the subpage.
     * @param bool             $activity        Optional show activity text if true.
     */
    public function __construct(
        \stdClass $assign,
        $context,
        $showintro,
        $coursemoduleid,
        $subpage = '',
        $preface = '',
        $postfix = '',
        \moodle_url $subpageurl = null,
        bool $activity = false
    ) {
        $this->assign = $assign;
        $this->context = $context;
        $this->showintro = $showintro;
        $this->coursemoduleid = $coursemoduleid;
        $this->subpage = $subpage;
        $this->preface = $preface;
        $this->postfix = $postfix;
        $this->subpageurl = $subpageurl;
        $this->activity = $activity;
    }
}
