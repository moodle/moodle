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

namespace dataformat_ods;

use core\dataformat;

/**
 * Tests for the dataformat_ods writer
 *
 * @package    dataformat_ods
 * @copyright  2022 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class writer_test extends \advanced_testcase {

    /**
     * Test writing data whose content contains an image with pluginfile.php source
     */
    public function test_write_data(): void {
        $columns = ['fruit', 'colour', 'animal'];
        $rows = [
            ['banana', 'yellow', 'monkey'],
            ['apple', 'red', 'wolf'],
            ['melon', 'green', 'aardvark'],
        ];

        // Export to file.
        $exportfile = dataformat::write_data('My export', 'ods', $columns, $rows);

        // Read the file.
        $odscells = $this->get_ods_rows_content(file_get_contents($exportfile));

        $this->assertEquals(array_merge([$columns], $rows), $odscells);
    }

    /**
     * Get ods rows from binary content
     * @param string $content
     * @return array
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Reader\Exception\ReaderNotOpenedException
     */
    private function get_ods_rows_content($content) {
        $reader = \Box\Spout\Reader\Common\Creator\ReaderFactory::createFromType(\Box\Spout\Common\Type::ODS);
        $file = tempnam(sys_get_temp_dir(), 'ods_');
        $handle = fopen($file, "w");
        fwrite($handle, $content);
        $reader->open($file);
        /** @var \Box\Spout\Reader\ODS\Sheet[] $sheets */
        $sheets = $reader->getSheetIterator();
        $rowscellsvalues = [];
        foreach ($sheets as $sheet) {
            /** @var \Box\Spout\Common\Entity\Row[] $rows */
            $rows = $sheet->getRowIterator();
            foreach ($rows as $row) {
                $thisvalues = [];
                foreach ($row->getCells() as $cell) {
                    $thisvalues[] = $cell->getValue();
                }
                $rowscellsvalues[] = $thisvalues;
            }
        }

        return $rowscellsvalues;
    }
}
