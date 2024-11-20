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

namespace core_analytics;

/**
 * Unit tests for the dataset manager.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dataset_manager_test extends \advanced_testcase {

    /** @var array Store dataset top rows. */
    protected array $sharedtoprows = [];

    /**
     * setUp
     *
     * @return null
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);

        $this->sharedtoprows = array(
            array('var1', 'var2'),
            array('value1', 'value2'),
            array('header1', 'header2')
        );
    }

    /**
     * test_create_dataset
     *
     * @return null
     */
    public function test_create_dataset(): void {

        $dataset1 = new \core_analytics\dataset_manager(1, 1, 'whatever', \core_analytics\dataset_manager::LABELLED_FILEAREA, false);
        $dataset1data = array_merge($this->sharedtoprows, array(array('yeah', 'yeah', 'yeah')));
        $f1 = $dataset1->store($dataset1data);

        $f1contents = $f1->get_content();
        $this->assertStringContainsString('yeah', $f1contents);
        $this->assertStringContainsString('var1', $f1contents);
        $this->assertStringContainsString('value1', $f1contents);
        $this->assertStringContainsString('header1', $f1contents);
    }

    /**
     * test_merge_datasets
     *
     * @return null
     */
    public function test_merge_datasets(): void {

        $dataset1 = new \core_analytics\dataset_manager(1, 1, 'whatever', \core_analytics\dataset_manager::LABELLED_FILEAREA, false);
        $dataset1data = array_merge($this->sharedtoprows, array(array('yeah', 'yeah', 'yeah')));
        $f1 = $dataset1->store($dataset1data);

        $dataset2 = new \core_analytics\dataset_manager(1, 2, 'whatever', \core_analytics\dataset_manager::LABELLED_FILEAREA, false);
        $dataset2data = array_merge($this->sharedtoprows, array(array('no', 'no', 'no')));
        $f2 = $dataset2->store($dataset2data);

        $files = array($f1, $f2);
        $merged = \core_analytics\dataset_manager::merge_datasets($files, 1, 'whatever',
            \core_analytics\dataset_manager::LABELLED_FILEAREA);

        $mergedfilecontents = $merged->get_content();
        $this->assertStringContainsString('yeah', $mergedfilecontents);
        $this->assertStringContainsString('no', $mergedfilecontents);
        $this->assertStringContainsString('var1', $mergedfilecontents);
        $this->assertStringContainsString('value1', $mergedfilecontents);
        $this->assertStringContainsString('header1', $mergedfilecontents);
    }

    /**
     * test_get_pending_files
     *
     * @return null
     */
    public function test_get_pending_files(): void {
        global $DB;

        $this->resetAfterTest();

        $fakemodelid = 123;
        $timesplittingids = array(
            '\core\analytics\time_splitting\quarters',
            '\core\analytics\time_splitting\quarters_accum',
        );

        // No files.
        $this->assertEmpty(\core_analytics\dataset_manager::get_pending_files($fakemodelid, true, $timesplittingids));
        $this->assertEmpty(\core_analytics\dataset_manager::get_pending_files($fakemodelid, false, $timesplittingids));

        // We will reuse this analysable file to create training and prediction datasets (analysable level files are
        // merged into training and prediction files).
        $analysabledataset = new \core_analytics\dataset_manager($fakemodelid, 1, 'whatever',
            \core_analytics\dataset_manager::LABELLED_FILEAREA, false);
        $analysabledatasetdata = array_merge($this->sharedtoprows, array(array('yeah', 'yeah', 'yeah')));
        $file = $analysabledataset->store($analysabledatasetdata);

        // Evaluation files ignored.
        $evaluationdataset = \core_analytics\dataset_manager::merge_datasets(array($file), $fakemodelid,
            '\core\analytics\time_splitting\quarters', \core_analytics\dataset_manager::LABELLED_FILEAREA, true);

        $this->assertEmpty(\core_analytics\dataset_manager::get_pending_files($fakemodelid, true, $timesplittingids));
        $this->assertEmpty(\core_analytics\dataset_manager::get_pending_files($fakemodelid, false, $timesplittingids));

        // Training and prediction files are not mixed up.
        $trainingfile1 = \core_analytics\dataset_manager::merge_datasets(array($file), $fakemodelid,
            '\core\analytics\time_splitting\quarters', \core_analytics\dataset_manager::LABELLED_FILEAREA, false);
        $this->waitForSecond();
        $trainingfile2 = \core_analytics\dataset_manager::merge_datasets(array($file), $fakemodelid,
            '\core\analytics\time_splitting\quarters', \core_analytics\dataset_manager::LABELLED_FILEAREA, false);

        $bytimesplitting = \core_analytics\dataset_manager::get_pending_files($fakemodelid, true, $timesplittingids);
        $this->assertFalse(isset($bytimesplitting['\core\analytics\time_splitting\quarters_accum']));
        $this->assertCount(2, $bytimesplitting['\core\analytics\time_splitting\quarters']);
        $this->assertEmpty(\core_analytics\dataset_manager::get_pending_files($fakemodelid, false, $timesplittingids));

        $predictionfile = \core_analytics\dataset_manager::merge_datasets(array($file), $fakemodelid,
            '\core\analytics\time_splitting\quarters', \core_analytics\dataset_manager::UNLABELLED_FILEAREA, false);
        $bytimesplitting = \core_analytics\dataset_manager::get_pending_files($fakemodelid, false, $timesplittingids);
        $this->assertFalse(isset($bytimesplitting['\core\analytics\time_splitting\quarters_accum']));
        $this->assertCount(1, $bytimesplitting['\core\analytics\time_splitting\quarters']);

        // Already used for training and prediction are discarded.
        $usedfile = (object)['modelid' => $fakemodelid, 'fileid' => $trainingfile1->get_id(), 'action' => 'trained',
            'time' => time()];
        $DB->insert_record('analytics_used_files', $usedfile);
        $bytimesplitting = \core_analytics\dataset_manager::get_pending_files($fakemodelid, true, $timesplittingids);
        $this->assertCount(1, $bytimesplitting['\core\analytics\time_splitting\quarters']);

        $usedfile->fileid = $predictionfile->get_id();
        $usedfile->action = 'predicted';
        $DB->insert_record('analytics_used_files', $usedfile);
        $this->assertEmpty(\core_analytics\dataset_manager::get_pending_files($fakemodelid, false, $timesplittingids));
    }
}
