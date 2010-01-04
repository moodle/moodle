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
 * All workshop module renderers are defined here
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Workshop module renderer class 
 * 
 * @package 
 * @version $Id$
 * @copyright 
 * @author David Mudrak <david.mudrak@gmail.com> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_mod_workshop_renderer  {

    /** the underlying renderer to use */
    protected $output;

    /** the page we are doing output for */
    protected $page;


    /**
     * Workshop renderer constructor
     * 
     * @param mixed $page the page we are doing output for
     * @param mixed $output lower-level renderer, typically moodle_core_renderer
     * @access public
     * @return void
     */
    public function __construct($page, $output) {
        $this->page   = $page;
        $this->output = $output;
    }



}
