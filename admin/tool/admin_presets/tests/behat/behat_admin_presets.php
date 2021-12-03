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
 * Steps definitions related with admin presets.
 *
 * @package   tool_admin_presets
 * @category  test
 * @copyright 2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author    Sylvain Revenu | Pimenko
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');
require_once(__DIR__ . '/../../../../../lib/behat/behat_field_manager.php');

use Behat\Mink\Exception\ExpectationException as ExpectationException;

/**
 * Steps definitions related with admin presets.
 *
 * @package   tool_admin_presets
 * @category  test
 * @copyright 2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author    Sylvain Revenu | Pimenko
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_admin_presets extends behat_base {

    /**
     * Downloads the file from a specific link on the page and checks the size is in a given range.
     *
     * Only works if the link has an href attribute. Javascript downloads are
     * not supported. Currently, the href must be an absolute URL.
     *
     * The range includes the endpoints. That is, a 10 byte file in considered to
     * be between "5" and "10" bytes, and between "10" and "20" bytes.
     *
     * @Then /^following "(?P<link_string>[^"]*)" "(?P<selector_string>[^"]*)" in the "(?P<element_container_string>(?:[^"]|\\")*)" "(?P<text_selector_string>[^"]*)" should download between "(?P<min_bytes>\d+)" and "(?P<max_bytes>\d+)" bytes$/
     * @param string $link the text of the link.
     * @param string $selectortype The type of what we look for
     * @param string $nodeelement Element we look in
     * @param string $nodeselectortype The type of selector where we look in
     * @param int $minexpectedsize the minimum expected file size in bytes.
     * @param int $maxexpectedsize the maximum expected file size in bytes.
     * @return void
     * @throws ExpectationException
     */
    final public function following_in_the_should_download_between_and_bytes(string $link, string $selectortype,
        string $nodeelement, string $nodeselectortype, int $minexpectedsize, int $maxexpectedsize): void {
        // If the minimum is greater than the maximum then swap the values.
        if ((int) $minexpectedsize > (int) $maxexpectedsize) {
            list($minexpectedsize, $maxexpectedsize) = [$maxexpectedsize, $minexpectedsize];
        }

        $exception = new ExpectationException('Error while downloading data from ' . $link, $this->getSession());

        // It will stop spinning once file is downloaded or time out.
        $result = $this->spin(
            function($context, $args) {
                return $this->download_file_from_link_within_node($args['selectortype'], $args['link'],
                    $args['nodeselectortype'], $args['nodeelement']);
            },
            [
                'selectortype' => $selectortype,
                'link' => $link,
                'nodeselectortype' => $nodeselectortype,
                'nodeelement' => $nodeelement
            ],
            behat_base::get_extended_timeout(),
            $exception
        );

        // Check download size.
        $actualsize = (int) strlen($result);
        if ($actualsize < $minexpectedsize || $actualsize > $maxexpectedsize) {
            throw new ExpectationException('Downloaded data was ' . $actualsize .
                ' bytes, expecting between ' . $minexpectedsize . ' and ' .
                $maxexpectedsize, $this->getSession());
        }
    }

    /**
     * Given the text of a link, download the linked file and return the contents.
     *
     * This is a helper method used by {@see following_in_the_should_download_between_and_bytes()}
     *
     * @param string $selectortype The type of what we look for
     * @param string $link the text of the link.
     * @param string $nodeselectortype The type of selector where we look in
     * @param string $nodeelement Element we look in
     * @return string the content of the downloaded file.
     */
    final public function download_file_from_link_within_node(string $selectortype, string $link,
        string $nodeselectortype, string $nodeelement): string {
        // Find the link from ur specific node.
        $linknode = $this->get_node_in_container($selectortype, $link, $nodeselectortype, $nodeelement);
        $this->ensure_node_is_visible($linknode);

        // Get the href and check it.
        $url = $linknode->getAttribute('href');
        if (!$url) {
            throw new ExpectationException('Download link does not have href attribute',
                $this->getSession());
        }
        if (!preg_match('~^https?://~', $url)) {
            throw new ExpectationException('Download link not an absolute URL: ' . $url,
                $this->getSession());
        }

        // Download the URL and check the size.
        $session = $this->getSession()->getCookie('MoodleSession');
        return download_file_content($url, ['Cookie' => 'MoodleSession=' . $session]);
    }
}
