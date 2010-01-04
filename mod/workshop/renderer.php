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
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_mod_workshop_renderer extends moodle_renderer_base {

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

    /**
     * Returns html code for a status message
     *
     * This should be replaced by a core system of displaying messages, as for example Mahara has.
     *
     * @param string $message to display
     * @return string html
     */
    public function status_message(stdClass $message) {
        if (empty($message->text)) {
            return '';
        }
        $sty = $message->sty ? $message->sty : 'info';

        $o = $this->output->output_tag('span', array(), $message->text);
        $closer = $this->output->output_tag('a', array('href' => $this->page->url->out()),
                    get_string('messageclose', 'workshop'));
        $o .= $this->output->container($closer, 'status-message-closer');
        if (isset($message->extra)) {
            $o .= $message->extra;
        }
        return $this->output->container($o, array('status-message', $sty));
    }

    /**
     * Wraps html code returned by the allocator init() method
     *
     * Supplied argument can be either integer status code or an array of string messages. Messages
     * in a array can have optional prefix or prefixes, using '::' as delimiter. Prefixes determine
     * the type of the message and may influence its visualisation.
     *
     * @param mixed $result int|array returned by init()
     * @return string $html to be echoed
     */
    public function allocation_init_result($result='') {
        $msg = new stdClass();
        if ($result === 'WORKSHOP_ALLOCATION_RANDOM_ERROR') {
            $msg = (object)array('text' => get_string('randomallocationerror', 'workshop'), 'sty' => 'error');
        } else {
            $msg = (object)array('text' => get_string('randomallocationdone', 'workshop'), 'sty' => 'ok');
        }
        $o = $this->status_message($msg);
        if (is_array($result)) {
            $o .= $this->output->output_start_tag('ul', array('class' => 'allocation-init-results'));
            foreach ($result as $message) {
                $parts  = explode('::', $message);
                $text   = array_pop($parts);
                $class  = implode(' ', $parts);
                $o .= $this->output->output_tag('li', array('class' => $class), $text);
            }
            $o .= $this->output->output_end_tag('ul');
            $o .= $this->output->continue_button($this->page->url->out());
        }
        return $o;
    }

}
