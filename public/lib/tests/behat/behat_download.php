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
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
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
     * @param string $element the text of the link.
     * @param TableNode $table the table of assertions to use the check the file contents.
     * @throws ExpectationException if the file cannot be downloaded, or if the download does not pass all the checks.
     */
    public function following_should_download_a_file_that(string $element, TableNode $table): void {
        $this->following_element_in_container_should_download_a_file_that($element, 'link', '', '', $table);
    }

    /**
     * Downloads the file from the given element on the page and verify the type and content.
     *
     * @Then following :link_text :selector_type should download a file that:
     *
     * @param string $element the text/locator of the element to download from.
     * @param string $selectortype the selector type of $element, e.g. 'link', 'button', 'select'.
     * @param TableNode $table the table of assertions to use the check the file contents.
     * @throws ExpectationException if the file cannot be downloaded, or if the download does not pass all the checks.
     */
    public function following_element_should_download_a_file_that(
        string $element,
        string $selectortype,
        TableNode $table
    ): void {
        $this->following_element_in_container_should_download_a_file_that($element, $selectortype, '', '', $table);
    }

    /**
     * Downloads the file from a link on the page and verify the type and content.
     *
     * @Then following :link_text in the :element_container_string :text_selector_string should download a file that:
     *
     * @param string $element the text of the link.
     * @param string $containerlocator the container element.
     * @param string $containertype the container selector type.
     * @param TableNode $table the table of assertions to use the check the file contents.
     * @throws ExpectationException if the file cannot be downloaded, or if the download does not pass all the checks.
     */
    public function following_in_element_should_download_a_file_that(
        string $element,
        string $containerlocator,
        string $containertype,
        TableNode $table
    ): void {
        $this->following_element_in_container_should_download_a_file_that(
            $element,
            'link',
            $containerlocator,
            $containertype,
            $table
        );
    }

    /**
     * Downloads the file from the given element inside a container and verify the type and content.
     *
     * @Then following :link_text :selector_type in the :element_container_string :text_selector_string should download a file that:
     *
     * @param string $element the text/locator of the element to download from.
     * @param string $selectortype the selector type of $element, e.g. 'link', 'button', 'select'.
     * @param string $containerlocator the container element.
     * @param string $containertype the container selector type.
     * @param TableNode $table the table of assertions to use the check the file contents.
     * @throws ExpectationException if the file cannot be downloaded, or if the download does not pass all the checks.
     */
    public function following_element_in_container_should_download_a_file_that(
        string $element,
        string $selectortype,
        string $containerlocator,
        string $containertype,
        TableNode $table
    ): void {

        $filecontent = $this->download_file($element, $containerlocator, $containertype, $selectortype);
        $this->verify_file_content($filecontent, $table);
    }

    /**
     * Download a file from the given element.
     *
     * @param string $element the text/locator of the element to download from.
     * @param string $containerlocator the container element.
     * @param string $containertype the container selector type.
     * @param string $selectortype the selector type of $element, e.g. 'link', 'button', 'select'. Defaults to 'link'.
     * @return string the file contents.
     * @throws ExpectationException if the download fails.
     */
    protected function download_file(
        string $element,
        string $containerlocator,
        string $containertype,
        string $selectortype = 'link'
    ): string {
        if ($selectortype === 'button') {
            return $this->download_file_from_button($element, $containerlocator, $containertype);
        }

        return behat_context_helper::get('behat_general')->download_file_from_link(
            $element,
            $containerlocator,
            $containertype,
            $selectortype
        );
    }

    /**
     * Checks the content of the downloaded file.
     *
     * @param string $filecontent the content of the file.
     * @param TableNode $table the table of assertions to check.
     * @throws ExpectationException if the file content does not pass all the checks.
     */
    public function verify_file_content(string $filecontent, TableNode $table): void {
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
                        'Invalid type of file assertion: ' . $row[0],
                        $this->getSession()
                    );
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

    /**
     * Given the text of a button, download the requested file and return the contents.
     *
     * @param string $element the text of the button.
     * @param string $containerlocator optional container element locator.
     * @param string $containertype optional container element selector type.
     *
     * @return string the content of the downloaded file.
     */
    public function download_file_from_button(string $element, string $containerlocator, string $containertype): string {
        if ($containerlocator !== '' && $containertype !== '') {
            $buttonnode = $this->get_node_in_container('button', $element, $containertype, $containerlocator);
        } else {
            $buttonnode = $this->find('button', $element);
        }

        $this->ensure_node_is_visible($buttonnode);

        // Only check button-related attributes.
        $url = $buttonnode->getAttribute('formaction');

        $form = null;
        // Case 1: button has form="id".
        if ($formid = $buttonnode->getAttribute('form')) {
            try {
                $form = $this->find('css', '#' . $formid);
            } catch (ElementNotFoundException $e) {
                $form = null;
            }
        }
        // Case 2: button is inside a form.
        if (!$form) {
            $form = $buttonnode->getParent();
            while ($form && strtolower($form->getTagName()) !== 'form') {
                $form = $form->getParent();
            }
        }

        // Fallback to form action if formaction is not present.
        if (!$url && $form) {
            $url = $form->getAttribute('action');
        }

        if (!$url) {
            throw new ExpectationException(
                'Button does not have formaction or associated form action',
                $this->getSession()
            );
        }

        $url = trim($url);
        if (!preg_match('~^https?://~', $url)) {
            $url = (new \moodle_url($url))->out(true);
        }

        $session = $this->getSession()->getCookie('MoodleSession');
        $headers = ['Cookie' => 'MoodleSession=' . $session];

        // Determine method (button > form > default GET).
        $method = strtolower($buttonnode->getAttribute('formmethod') ?: ($form ? $form->getAttribute('method') : 'get'));

        // Build form submission data.
        $postdata = $this->get_form_postdata($form, $buttonnode);

        // POST request.
        if ($method === 'post' && !empty($postdata)) {
            return download_file_content($url, $headers, $postdata);
        }

        // GET with query params.
        if (!empty($postdata)) {
            $separator = str_contains($url, '?') ? '&' : '?';
            $url .= $separator . http_build_query($postdata);
        }

        return download_file_content($url, $headers);
    }

    /**
     * Build the form body for a submit button so the download request matches the browser submission.
     *
     * @param NodeElement|null $form the form element if one can be resolved.
     * @param NodeElement $buttonnode the button trigger element.
     * @return array the form data to submit.
     */
    protected function get_form_postdata(?NodeElement $form, NodeElement $buttonnode): array {
        $postdata = [];

        // Include clicked button.
        if ($name = $buttonnode->getAttribute('name')) {
            $postdata[$name] = $buttonnode->getAttribute('value') ?? '1';
        }

        // No form, return only button data.
        if (!$form) {
            return $postdata;
        }

        foreach ($form->findAll('css', 'input, select, textarea') as $field) {
            $name = $field->getAttribute('name');
            if (!$name) {
                continue;
            }

            $type = strtolower($field->getAttribute('type') ?? 'text');
            $tag = strtolower($field->getTagName());

            // Skip irrelevant fields.
            if (in_array($type, ['submit', 'button', 'reset', 'image', 'file'], true)) {
                continue;
            }

            // Checkbox / radio.
            if (in_array($type, ['checkbox', 'radio'], true)) {
                if ($field->isChecked()) {
                    $postdata[$name] = $field->getAttribute('value') ?? '1';
                }
                continue;
            }

            // Select.
            if ($tag === 'select') {
                $value = $field->getValue();

                if (is_array($value)) {
                    foreach ($value as $v) {
                        $postdata[$name][] = $v;
                    }
                } else if ($value !== null && $value !== '') {
                    $postdata[$name] = $value;
                }

                continue;
            }

            // Textarea.
            if ($tag === 'textarea') {
                $postdata[$name] = $field->getValue();
                continue;
            }

            // Default fields.
            if (($value = $field->getAttribute('value')) !== null) {
                $postdata[$name] = $value;
            }
        }

        return $postdata;
    }
}
