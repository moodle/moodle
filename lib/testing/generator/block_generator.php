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
 * Block generator base class.
 *
 * @package    core
 * @category   test
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Block generator base class.
 *
 * Extend in blocks/xxxx/tests/generator/lib.php as class block_xxxx_generator.
 *
 * @package    core
 * @category   test
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class testing_block_generator extends component_generator_base {
    /** @var number of created instances */
    protected $instancecount = 0;

    /**
     * To be called from data reset code only,
     * do not use in tests.
     * @return void
     */
    public function reset() {
        $this->instancecount = 0;
    }

    /**
     * Returns block name
     * @return string name of block that this class describes
     * @throws coding_exception if class invalid
     */
    public function get_blockname() {
        $matches = null;
        if (!preg_match('/^block_([a-z0-9_]+)_generator$/', get_class($this), $matches)) {
            throw new coding_exception('Invalid block generator class name: '.get_class($this));
        }

        if (empty($matches[1])) {
            throw new coding_exception('Invalid block generator class name: '.get_class($this));
        }
        return $matches[1];
    }

    /**
     * Fill in record defaults
     * @param stdClass $record
     * @return stdClass
     */
    protected function prepare_record(stdClass $record) {
        $record->blockname = $this->get_blockname();
        if (!isset($record->parentcontextid)) {
            $record->parentcontextid = context_system::instance()->id;
        }
        if (!isset($record->showinsubcontexts)) {
            $record->showinsubcontexts = 1;
        }
        if (!isset($record->pagetypepattern)) {
            $record->pagetypepattern = '';
        }
        if (!isset($record->subpagepattern)) {
            $record->subpagepattern = null;
        }
        if (!isset($record->defaultregion)) {
            $record->defaultregion = '';
        }
        if (!isset($record->defaultweight)) {
            $record->defaultweight = '';
        }
        if (!isset($record->configdata)) {
            $record->configdata = null;
        }
        return $record;
    }

    /**
     * Create a test block
     * @param array|stdClass $record
     * @param array $options
     * @return stdClass activity record
     */
    abstract public function create_instance($record = null, array $options = null);
}

/**
 * Deprecated in favour of testing_block_generator
 *
 * @deprecated since Moodle 2.5 MDL-37457 - please do not use this function any more.
 * @todo       MDL-37517 This will be deleted in Moodle 2.7
 * @see        testing_block_generator
 * @package    core
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class phpunit_block_generator extends testing_block_generator {

    /**
     * Dumb constructor to throw the deprecated notification
     * @param testing_data_generator $datagenerator
     */
    public function __construct(testing_data_generator $datagenerator) {
        debugging('Class phpunit_block_generator is deprecated, please use class testing_block_generator instead', DEBUG_DEVELOPER);
        parent::__construct($datagenerator);
    }
}
