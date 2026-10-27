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
     * @Then following ":element" should download a file that:
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
     * Note the quotes around the placeholders: without them this step would be ambiguous
     * with the one above, because an unquoted placeholder also matches a bare word.
     *
     * @Then following ":element" ":selectortype" should download a file that:
     *
     * @param string $element the text/locator of the element to download from.
     * @param string $selectortype the selector type of $element, e.g. 'link', 'button', 'css_element'.
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
     * @Then following ":element" in the ":containerlocator" ":containertype" should download a file that:
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
     * @Then following ":element" ":selectortype" in the ":containerlocator" ":containertype" should download a file that:
     *
     * @param string $element the text/locator of the element to download from.
     * @param string $selectortype the selector type of $element, e.g. 'link', 'button', 'css_element'.
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
     * Any selector type can be used. What makes the element downloadable is not its
     * selector type but what it does in the browser, which {@see get_download_request()}
     * works out from the element itself.
     *
     * @param string $element the text/locator of the element to download from.
     * @param string $containerlocator the container element.
     * @param string $containertype the container selector type.
     * @param string $selectortype the selector type of $element, e.g. 'link', 'button', 'css_element'.
     * @return string the file contents.
     * @throws ExpectationException if the download fails.
     */
    public function download_file(
        string $element,
        string $containerlocator = '',
        string $containertype = '',
        string $selectortype = 'link'
    ): string {
        if ($containerlocator !== '' && $containertype !== '') {
            $node = $this->get_node_in_container($selectortype, $element, $containertype, $containerlocator);
        } else {
            $node = $this->find($selectortype, $element);
        }

        $this->ensure_node_is_visible($node);

        [$url, $method, $params] = $this->get_download_request($node);

        $headers = ['Cookie' => 'MoodleSession=' . $this->getSession()->getCookie('MoodleSession')];

        if ($method === 'post') {
            $filecontent = download_file_content($url, $headers, $params);
        } else {
            if ($params) {
                // A GET submission replaces any query string already on the action.
                $url = preg_replace('~\?.*$~', '', $url) . '?' . http_build_query($params);
            }
            $filecontent = download_file_content($url, $headers);
        }

        if ($filecontent === false) {
            throw new ExpectationException(
                'Failed to download the file from ' . $url,
                $this->getSession(),
            );
        }

        return $filecontent;
    }

    /**
     * Work out the request the browser would make when the given element is activated.
     *
     * Only two things on a page start a download: a link, which fetches its href, and
     * anything that submits a form, which sends the form action along with the form data.
     * Every element worth passing to these steps is one or the other, so there is no need
     * for a separate code path per selector type.
     *
     * @param NodeElement $node the element to download from.
     * @return array [string $url, string $method, array $params] the request to make.
     * @throws ExpectationException if the element cannot start a download.
     */
    protected function get_download_request(NodeElement $node): array {

        // A link, or anything inside one, downloads its href.
        if ($link = $node->find('xpath', '/ancestor-or-self::a[@href][1]')) {
            return [$this->resolve_url($link->getAttribute('href')), 'get', []];
        }

        // Anything that submits a form downloads the form action, sending the form data with it.
        if ($form = $this->get_associated_form($node)) {
            if ($node->getAttribute('disabled')) {
                throw new ExpectationException(
                    'The "' . $node->getXpath() . '" element is disabled, so it cannot submit its form',
                    $this->getSession(),
                );
            }

            // The element's own form* attributes win over the form's, as they do in a browser.
            $url = $node->getAttribute('formaction')
                ?: $form->getAttribute('action')
                ?: $this->getSession()->getCurrentUrl();
            $method = $node->getAttribute('formmethod') ?: $form->getAttribute('method') ?: 'get';

            return [$this->resolve_url($url), strtolower($method), $this->get_form_postdata($form, $node)];
        }

        throw new ExpectationException(
            'The "' . $node->getXpath() . '" element is neither a link nor something that submits a form, ' .
                'so it cannot be used to download a file',
            $this->getSession(),
        );
    }

    /**
     * Find the form the given element submits, whether it is inside one or associated with one by id.
     *
     * @param NodeElement $node the element to find the form for.
     * @return NodeElement|null the form, or null if the element does not submit one.
     */
    protected function get_associated_form(NodeElement $node): ?NodeElement {

        // An element anywhere on the page can name its form by id.
        if ($formid = $node->getAttribute('form')) {
            return $this->getSession()->getPage()->find('css', '#' . $formid);
        }

        return $node->find('xpath', '/ancestor::form[1]');
    }

    /**
     * Turn the URL taken from the page into one download_file_content() can fetch.
     *
     * @param string $url an href or form action, which may be relative.
     * @return string an absolute URL.
     * @throws ExpectationException if there is no URL to download from.
     */
    protected function resolve_url(string $url): string {
        $url = trim($url);

        if ($url === '') {
            throw new ExpectationException(
                'The element to download from has no URL',
                $this->getSession(),
            );
        }

        if (!preg_match('~^https?://~', $url)) {
            // Note out(false), so the parameter separators are plain & and not &amp;.
            $url = (new moodle_url($url))->out(false);
        }

        return $url;
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
     * Build the form body for a submitter, so the download request matches the browser submission.
     *
     * @param NodeElement $form the form being submitted.
     * @param NodeElement $submitter the element submitting the form.
     * @return array the form data to submit.
     */
    protected function get_form_postdata(NodeElement $form, NodeElement $submitter): array {
        $postdata = [];

        // A submitter with a name contributes its own value, as it does in a browser.
        if ($name = $submitter->getAttribute('name')) {
            $postdata[$name] = $submitter->getAttribute('value') ?? '1';
        }

        foreach ($this->get_form_fields($form) as $field) {
            $name = $field->getAttribute('name');
            if (!$name || $field->getAttribute('disabled')) {
                // A field with no name, or a disabled one, is never submitted.
                continue;
            }

            $type = strtolower($field->getAttribute('type') ?? 'text');
            $tag = strtolower($field->getTagName());

            // Buttons are handled by the submitter above, and we cannot replay a file upload.
            if (in_array($type, ['submit', 'button', 'reset', 'image', 'file'], true)) {
                continue;
            }

            if (in_array($type, ['checkbox', 'radio'], true)) {
                if ($field->isChecked()) {
                    $postdata[$name] = $field->getAttribute('value') ?? '1';
                }
                continue;
            }

            // Note getValue(), not getAttribute('value'): we want what the field holds now,
            // not what it held when the page was first rendered.
            $value = $field->getValue();

            if ($tag === 'select' && is_array($value)) {
                foreach ($value as $selected) {
                    $postdata[$name][] = $selected;
                }
            } else if ($value !== null) {
                $postdata[$name] = $value;
            }
        }

        return $postdata;
    }

    /**
     * Get every field the given form submits, including any associated with it by id from elsewhere on the page.
     *
     * @param NodeElement $form the form.
     * @return NodeElement[] the fields.
     */
    protected function get_form_fields(NodeElement $form): array {
        $fields = $form->findAll('css', 'input, select, textarea');

        if ($formid = $form->getAttribute('id')) {
            $selector = "input[form='{$formid}'], select[form='{$formid}'], textarea[form='{$formid}']";
            $fields = array_merge($fields, $this->getSession()->getPage()->findAll('css', $selector));
        }

        // A field inside the form can also name it, so drop anything we have seen already.
        $unique = [];
        foreach ($fields as $field) {
            $unique[$field->getXpath()] = $field;
        }

        return array_values($unique);
    }
}
