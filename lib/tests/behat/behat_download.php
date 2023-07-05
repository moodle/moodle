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
 * Steps definitions to verify a downloaded file.
 *
 * @package    core
 * @category   test
 * @copyright  2024 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ExpectationException;

require_once(__DIR__ . '/../../behat/behat_base.php');

/**
 * Steps definitions to verify a downloaded file.
 *
 * @package    core
 * @category   test
 * @copyright  2024 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_download extends behat_base {

    /**
     * Downloads the file from a link on the page and verify the type and content.
     *
     * @Then following :link_text should download a file that:
     *
     * @param string $linktext the text of the link.
     * @param TableNode $table the table of assertions to use the check the file contents.
     * @throws ExpectationException if the file cannot be downloaded, or if the download does not pass all the checks.
     */
    public function following_should_download_a_file_that(string $linktext, TableNode $table): void {
        $this->following_in_element_should_download_a_file_that($linktext, '', '', $table);
    }

    /**
     * Downloads the file from a link on the page and verify the type and content.
     *
     * @Then following :link_text in the :element_container_string :text_selector_string should download a file that:
     *
     * @param string $linktext the text of the link.
     * @param string $containerlocator the container element.
     * @param string $containertype the container selector type.
     * @param TableNode $table the table of assertions to use the check the file contents.
     * @throws ExpectationException if the file cannot be downloaded, or if the download does not pass all the checks.
     */
    public function following_in_element_should_download_a_file_that(string $linktext, string $containerlocator,
            string $containertype, TableNode $table): void {

        $filecontent = $this->download_file($linktext, $containerlocator, $containertype);
        $this->verify_file_content($filecontent, $table);
    }

    /**
     * Download a file from the given link.
     *
     * @param string $linktext the text of the link.
     * @param string $containerlocator the container element.
     * @param string $containertype the container selector type.
     * @return string the file contents.
     * @throws ExpectationException if the download fails.
     */
    protected function download_file(string $linktext, string $containerlocator, string $containertype): string {
        return behat_context_helper::get('behat_general')->download_file_from_link($linktext, $containerlocator, $containertype);
    }

    /**
     * Checks the content of the downloaded file.
     *
     * @param string $filecontent the content of the file.
     * @param TableNode $table the table of assertions to check.
     * @throws ExpectationException if the file content does not pass all the checks.
     */
    private function verify_file_content(string $filecontent, TableNode $table): void {
        foreach ($table->getRows() as $row) {
            switch (strtolower(trim($row[0]))) {
                case 'contains text':
                    $this->verify_file_contains_text($filecontent, $row[1]);
                    break;
                case 'contains text in xml element':
                    $this->verify_xml_element_contains($filecontent, $row[1]);
                    break;
                case 'has mimetype':
                    $this->verify_file_mimetype($filecontent, $row[1]);
                    break;
                case 'contains file in zip':
                    $this->verify_zip_file_content($filecontent, $row[1]);
                    break;
                default:
                    throw new ExpectationException(
                        'Invalid type of file assertion: ' . $row[0], $this->getSession());
            }
        }
    }

    /**
     * Validates the downloaded file appears to be of the mimetype.
     *
     * @param string $filecontent the content of the file.
     * @param string $expectedmimetype the expected file mimetype e.g. 'application/xml'.
     * @throws ExpectationException if the file does not appear to be of the expected type.
     */
    protected function verify_file_mimetype(string $filecontent, string $expectedmimetype): void {

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $actualmimetype = $finfo->buffer($filecontent);

        if ($actualmimetype !== $expectedmimetype) {
            throw new ExpectationException(
                "The file downloaded should have been a $expectedmimetype file, " .
                "but got $actualmimetype instead.",
                $this->getSession(),
            );
        }
    }

    /**
     * Asserts that the given string is present in the file content.
     *
     * @param string $filecontent the content of the file.
     * @param string $expectedcontent the string to search for.
     * @throws ExpectationException if verification fails.
     */
    protected function verify_file_contains_text(string $filecontent, string $expectedcontent): void {
        if (!str_contains($filecontent, $expectedcontent)) {
            throw new ExpectationException(
                "The string '$expectedcontent' was not found in the file content.",
                $this->getSession(),
            );
        }
    }

    /**
     * Asserts that the given XML file is valid and contains the expected string.
     *
     * @param string $filecontent the content of the file.
     * @param string $expectedcontent the string to search for.
     * @throws ExpectationException
     */
    protected function verify_xml_element_contains(string $filecontent, string $expectedcontent): void {
        $xml = new SimpleXMLElement($filecontent);
        $result = $xml->xpath("//*[contains(text(), '$expectedcontent')]");

        if (empty($result)) {
            throw new ExpectationException(
                "The string '$expectedcontent' was not found in the content of any element in this XML file.",
                $this->getSession(),
            );
        }
    }

    /**
     * Save the downloaded file to tempdir and return the path.
     *
     * @param string $filecontent the content of the file.
     * @param string $fileextension the expected file type, given as a file extension, e.g. 'txt', 'xml'.
     * @return string path where the file was saved temporarily.
     */
    protected function save_to_temp_file(string $filecontent, string $fileextension): string {
        // Then perform additional image-specific validations.
        $tempdir = make_request_directory();
        $filepath = $tempdir . '/downloaded.' . $fileextension;
        file_put_contents($filepath, $filecontent);
        return $filepath;
    }

    /**
     * Asserts that the given zip archive contains the expected file(s).
     *
     * @param string $filecontent the content of the file.
     * @param string $expectedfile the name of the file to search for.
     * @throws ExpectationException if the zip file does not contain the expected files.
     */
    protected function verify_zip_file_content(string $filecontent, string $expectedfile): void {

        $zip = new ZipArchive();
        $res = $zip->open($this->save_to_temp_file($filecontent, 'zip'));

        if ($res !== true) {
            throw new ExpectationException(
                "Failed to open zip file.",
                $this->getSession(),
            );
        }

        if ($zip->locateName($expectedfile) === false) {
            throw new ExpectationException(
                "The file '$expectedfile' was not found in the downloaded zip archive.",
                $this->getSession(),
            );
        }
    }
}
