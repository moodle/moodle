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
 * Custom PHP_CodeSniffer XML Report for local_codechecker.
 *
 * @package    local_codechecker
 * @copyright  2020 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_codechecker;

// phpcs:disable moodle.NamingConventions

/**
 * Custom PHP_CodeSniffer XML Report for local_codechecker.
 *
 * This custom report is exactly the upstream XML one, but
 * with a few modifications when there aren't errors and warnings.
 *
 * @copyright  2020 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report extends \PHP_CodeSniffer\Reports\Xml {

    /**
     * Generate a partial report for a single processed file.
     *
     * For files with violations delegate processing to parent class. For files
     * without violations, just return the plain <file> element, without any err/warn.
     *
     * @param array $report Prepared report data.
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being reported on.
     * @param boolean $showSources Show sources?
     * @param int $width Maximum allowed line width.
     *
     * @return boolean
     */
    public function generateFileReport($report, \PHP_CodeSniffer\Files\File $phpcsFile, $showSources = false, $width = 80) {

        // Report has violations, delegate to parent standard processing.
        if ($report['errors'] !== 0 || $report['warnings'] !== 0) {
            return parent::generateFileReport($report, $phpcsFile, $showSources, $width);
        }

        // Here we are, with a file with 0 errors and warnings.
        $out = new \XMLWriter;
        $out->openMemory();
        $out->setIndent(true);

        $out->startElement('file');
        $out->writeAttribute('name', $report['filename']);
        $out->writeAttribute('errors', $report['errors']);
        $out->writeAttribute('warnings', $report['warnings']);

        $out->endElement();
        echo $out->flush();

        return true;

    }
}
