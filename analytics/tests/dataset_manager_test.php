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
 * Unit tests for the dataset manager.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for the dataset manager.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dataset_manager_testcase extends advanced_testcase {

    /**
     * test_create_dataset
     *
     * @return
     */
    public function test_create_dataset() {
        $this->resetAfterTest(true);

        $sharedtoprows = array(
            array('var1', 'var2'),
            array('value1', 'value2'),
            array('header1', 'header2')
        );

        $dataset1 = new \core_analytics\dataset_manager(1, 1, 'whatever', \core_analytics\dataset_manager::LABELLED_FILEAREA, false);
        $dataset1->init_process();
        $dataset1data = array_merge($sharedtoprows, array(array('yeah', 'yeah', 'yeah')));
        $f1 = $dataset1->store($dataset1data);
        $dataset1->close_process();

        $f1contents = $f1->get_content();
        $this->assertContains('yeah', $f1contents);
        $this->assertContains('var1', $f1contents);
        $this->assertContains('value1', $f1contents);
        $this->assertContains('header1', $f1contents);
    }

    /**
     * test_merge_datasets
     *
     * @return
     */
    public function test_merge_datasets() {
        $this->resetAfterTest(true);

        $sharedtoprows = array(
            array('var1', 'var2'),
            array('value1', 'value2'),
            array('header1', 'header2')
        );

        $dataset1 = new \core_analytics\dataset_manager(1, 1, 'whatever', \core_analytics\dataset_manager::LABELLED_FILEAREA, false);
        $dataset1->init_process();
        $dataset1data = array_merge($sharedtoprows, array(array('yeah', 'yeah', 'yeah')));
        $f1 = $dataset1->store($dataset1data);
        $dataset1->close_process();

        $dataset2 = new \core_analytics\dataset_manager(1, 2, 'whatever', \core_analytics\dataset_manager::LABELLED_FILEAREA, false);
        $dataset2->init_process();
        $dataset2data = array_merge($sharedtoprows, array(array('no', 'no', 'no')));
        $f2 = $dataset2->store($dataset2data);
        $dataset2->close_process();

        $files = array($f1, $f2);
        $merged = \core_analytics\dataset_manager::merge_datasets($files, 1, 'whatever',
            \core_analytics\dataset_manager::LABELLED_FILEAREA);

        $mergedfilecontents = $merged->get_content();
        $this->assertContains('yeah', $mergedfilecontents);
        $this->assertContains('no', $mergedfilecontents);
        $this->assertContains('var1', $mergedfilecontents);
        $this->assertContains('value1', $mergedfilecontents);
        $this->assertContains('header1', $mergedfilecontents);
    }
}
