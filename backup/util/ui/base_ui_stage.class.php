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
 * Backup user interface stages
 *
 * This file contains the classes required to manage the stages that make up the
 * backup user interface.
 * These will be primarily operated a {@see backup_ui} instance.
 *
 * @package   moodlecore
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Abstract stage class
 *
 * This class should be extended by all backup stages (a requirement of many backup ui functions).
 * Each stage must then define two abstract methods
 *  - process : To process the stage
 *  - initialise_stage_form : To get a backup_moodleform instance for the stage
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base_ui_stage {
    /**
     * The current stage
     * @var int
     */
    protected $stage = 1;
    /**
     * The backuck UI object
     * @var base_ui
     */
    protected $ui;
    /**
     * The moodleform for this stage
     * @var base_moodleform
     */
    protected $stageform = null;
    /**
     * Custom form params that will be added as hidden inputs
     */
    protected $params = null;
    /**
     *
     * @param base_ui $ui
     */
    public function __construct(base_ui $ui, array $params=null) {
        $this->ui = $ui;
        $this->params = $params;
    }
    /**
     * Returns the custom params for this stage
     * @return array|null
     */
    final public function get_params() {
        return $this->params;
    }
    /**
     * The current stage
     * @return int
     */
    final public function get_stage() {
        return $this->stage;
    }
    /**
     * The next stage
     * @return int
     */
    final public function get_next_stage() {
        return floor($this->stage*2);
    }
    /**
     * The previous stage
     * @return int
     */
    final public function get_prev_stage() {
        return floor($this->stage/2);
    }
    /**
     * The name of this stage
     * @return string
     */
    public function get_name() {
        return get_string('currentstage'.$this->stage,'backup');
    }
    /**
     * The backup id from the backup controller
     * @return string
     */
    final public function get_uniqueid() {
        return $this->ui->get_uniqueid();
    }

    /**
     * Displays the stage.
     *
     * By default this involves instantiating the form for the stage and the calling
     * it to display.
     *
     * @param core_backup_renderer $renderer
     * @return string HTML code to echo
     */
    public function display(core_backup_renderer $renderer) {

        $form = $this->initialise_stage_form();
        // a nasty hack follows to work around the sad fact that moodle quickforms
        // do not allow to actually return the HTML content, just to echo it
        flush();
        ob_start();
        $form->display();
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    /**
     * Processes the stage.
     *
     * This must be overridden by every stage as it will be different for every stage
     *
     * @abstract
     * @param backup_moodleform|null $form
     */
    abstract public function process(base_moodleform $form=null);
    /**
     * Creates an instance of the correct moodleform properly populated and all
     * dependencies instantiated
     *
     * @abstract
     * @return backup_moodleform
     */
    abstract protected function initialise_stage_form();

    final public function get_ui() {
        return $this->ui;
    }

    public function is_first_stage() {
        return $this->stage == 1;
    }
}
