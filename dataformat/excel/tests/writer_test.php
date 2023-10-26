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

namespace dataformat_excel;

use core\dataformat;

/**
 * Tests for the dataformat_excel writer
 *
 * @package    dataformat_excel
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
        $exportfile = dataformat::write_data('My export', 'excel', $columns, $rows);

        // Read the file.
        $excelcells = $this->get_excel(file_get_contents($exportfile));

        $this->assertEquals(array_merge([$columns], $rows), $excelcells);
    }

    /**
     * Get an Excel object to check the content
     *
     * @param string $content
     * @return array two-dimensional array with cell values
     */
    private function get_excel(string $content) {
        $file = tempnam(sys_get_temp_dir(), 'excel_');
        $handle = fopen($file, "w");
        fwrite($handle, $content);
        /** @var \Box\Spout\Reader\XLSX\Reader $reader */
        $reader = \Box\Spout\Reader\Common\Creator\ReaderFactory::createFromType(\Box\Spout\Common\Type::XLSX);
        $reader->open($file);

        /** @var \Box\Spout\Reader\XLSX\Sheet[] $sheets */
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
