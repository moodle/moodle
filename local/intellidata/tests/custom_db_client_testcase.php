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
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @codingStandardsIgnoreFile
 */

namespace local_intellidata;

use local_intellidata\helpers\DBHelper;
use local_intellidata\helpers\ParamsHelper;
use local_intellidata\helpers\SettingsHelper;
use local_intellidata\helpers\StorageHelper;
use local_intellidata\helpers\TrackingHelper;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/local/intellidata/tests/setup_helper.php');
require_once($CFG->dirroot . '/local/intellidata/tests/generator.php');
require_once($CFG->dirroot . '/local/intellidata/tests/test_helper.php');

/**
 * Activity migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class custom_db_client_testcase extends \advanced_testcase {
    protected $newexportavailable;
    protected $olddbclient;
    protected $oldstorage;
    protected $release;

    public function setRegisterMockObjectsFromTestArgumentsRecursively(bool $flag): void {
        global $DB;

        $DB->force_transaction_rollback();

        if (ParamsHelper::compare_release('3.8.0')) {
            setup_helper::enable_custom_driver();
        }

        $DB = DBHelper::get_db_client(DBHelper::PENETRATION_TYPE_EXTERNAL);

        parent::setRegisterMockObjectsFromTestArgumentsRecursively($flag);
    }

    public function setUp(): void {
        $this->setAdminUser();

        setup_helper::setup_tests_config();

        $this->oldstorage = SettingsHelper::get_setting('trackingstorage');
        SettingsHelper::set_setting('trackingstorage', StorageHelper::DATABASE_STORAGE);

        $this->release = ParamsHelper::get_release();
        $this->newexportavailable = ParamsHelper::compare_release('3.8.0');
    }

    public function tearDown(): void {
        SettingsHelper::set_setting('trackingstorage', $this->oldstorage);
    }
}
