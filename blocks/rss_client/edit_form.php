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
 * Form for editing RSS client block instances.
 *
 * @package   block_rss_client
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir .'/simplepie/moodle_simplepie.php');

/**
 * Form for editing RSS client block instances.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_rss_client_edit_form extends block_edit_form {

    /** @var stdClass|null The new RSS feed URL object. */
    private ?stdClass $newrss = null;

    protected function specific_definition($mform) {
        global $CFG, $DB, $USER;

        // Fields for editing block contents.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $radiogroup = [
            $mform->createElement('radio', 'config_method',
                get_string('configmethodexisting', 'block_rss_client'), null, 'existing'),
            $mform->createElement('radio', 'config_method',
                get_string('configmethodnew', 'block_rss_client'), null, 'new'),
        ];
        $mform->addGroup(
            elements: $radiogroup,
            name: 'config_method_group',
            separator: ['&nbsp;&nbsp;&nbsp;'],
            appendName: false,
        );
        $mform->setDefault('config_method', 'existing');

        // Add new RSS feed.
        $mform->addElement('text', 'config_feedurl', get_string('feedurl', 'block_rss_client'));
        $mform->setType('config_feedurl', PARAM_URL);
        $mform->hideIf('config_feedurl', 'config_method', 'ne', 'new');

        // Select existing RSS feed.
        $insql = '';
        $params = array('userid' => $USER->id);
        if (!empty($this->block->config) && !empty($this->block->config->rssid)) {
            list($insql, $inparams) = $DB->get_in_or_equal($this->block->config->rssid, SQL_PARAMS_NAMED);
            $insql = "OR id $insql ";
            $params += $inparams;
        }

        $titlesql = "CASE WHEN {$DB->sql_isempty('block_rss_client','preferredtitle', false, false)}
                      THEN {$DB->sql_compare_text('title', 64)} ELSE preferredtitle END";

        $rssfeeds = $DB->get_records_sql_menu("
                SELECT id, $titlesql
                  FROM {block_rss_client}
                 WHERE userid = :userid OR shared = 1 $insql
                 ORDER BY $titlesql",
                $params);

        if ($rssfeeds) {
            $select = $mform->addElement('select', 'config_rssid', get_string('choosefeedlabel', 'block_rss_client'), $rssfeeds);
            $select->setMultiple(true);
            $mform->hideIf('config_rssid', 'config_method', 'ne', 'existing');
        } else {
            $mform->addElement('static', 'config_rssid_no_feeds', get_string('choosefeedlabel', 'block_rss_client'),
                    get_string('nofeeds', 'block_rss_client'));
            $mform->hideIf('config_rssid_no_feeds', 'config_method', 'ne', 'existing');
        }

        // Subheading: Display settings for RSS feed.
        $startsubheading = '<div class="row"><h4 class="col-md-12 col-form-label d-flex">';
        $endsubheading = '</h4></div>';
        $mform->addElement('html', $startsubheading . get_string('displaysettings', 'block_rss_client') . $endsubheading);

        $mform->addElement('text', 'config_title', get_string('uploadlabel'));
        $mform->setType('config_title', PARAM_NOTAGS);

        $mform->addElement('selectyesno', 'config_display_description', get_string('displaydescriptionlabel', 'block_rss_client'));
        $mform->setDefault('config_display_description', 0);

        $mform->addElement('text', 'config_shownumentries', get_string('shownumentrieslabel', 'block_rss_client'), ['size' => 5]);
        $mform->setType('config_shownumentries', PARAM_INT);
        $mform->addRule('config_shownumentries', null, 'numeric', null, 'client');
        if (!empty($CFG->block_rss_client_num_entries)) {
            $mform->setDefault('config_shownumentries', $CFG->block_rss_client_num_entries);
        } else {
            $mform->setDefault('config_shownumentries', 5);
        }

        $mform->addElement('selectyesno', 'config_block_rss_client_show_channel_link', get_string('clientshowchannellinklabel', 'block_rss_client'));
        $mform->setDefault('config_block_rss_client_show_channel_link', 0);

        $mform->addElement('selectyesno', 'config_block_rss_client_show_channel_image', get_string('clientshowimagelabel', 'block_rss_client'));
        $mform->setDefault('config_block_rss_client_show_channel_image', 0);
    }

    /**
     * Overriding the get_data function to insert a new RSS ID.
     */
    public function get_data(): ?stdClass {
        $data = parent::get_data();
        // Force the 'existing` method as a default.
        $data->config_method = 'existing';
        // Sanitize the title to prevent XSS (Cross-Site Scripting) attacks by encoding special characters into HTML entities.
        $data->config_title = htmlspecialchars($data->config_title, ENT_QUOTES, 'utf-8');
        // If the new RSS is not empty then add the ID to the config_rssid.
        if ($data && $this->newrss) {
            $data->config_rssid[] = $this->newrss->id;
        }
        return $data;
    }

    /**
     * Overriding the definition_after_data to empty the input.
     */
    public function definition_after_data(): void {
        parent::definition_after_data();
        $mform =& $this->_form;
        // If form is not submitted then empty the feed URL.
        if (!$this->is_submitted()) {
            $mform->getElement('config_feedurl')->setValue('');
        }
    }

    /**
     * Overriding the validation to validate the RSS URL and store it to the database.
     *
     * If there are no errors, insert the new feed to the database and store the object in
     * the private property so it can be saved to the RSS block config.
     *
     * @param array $data Data from the form.
     * @param array $files Files form the form.
     * @return array of errors from validation.
     */
    public function validation($data, $files): array {
        global $USER, $DB;
        $errors = parent::validation($data, $files);

        if ($data['config_method'] === "new") {
            // If the "New" method is selected and the feed URL is not empty, then proceed.
            if ($data['config_feedurl']) {
                if (!filter_var($data['config_feedurl'], FILTER_VALIDATE_URL)) {
                    $errors['config_feedurl'] = get_string('couldnotfindloadrssfeed', 'block_rss_client');
                    return $errors;
                }
                try {
                    $rss = new moodle_simplepie();
                    // Set timeout for longer than normal to try and grab the feed.
                    $rss->set_timeout(10);
                    $rss->set_feed_url($data['config_feedurl']);
                    $rss->set_autodiscovery_cache_duration(0);
                    $rss->set_autodiscovery_level(moodle_simplepie::LOCATOR_ALL);
                    $rss->init();
                    if ($rss->error()) {
                        $errors['config_feedurl'] = get_string('couldnotfindloadrssfeed', 'block_rss_client');
                    } else {
                        // Return URL without quoting.
                        $discoveredurl = new moodle_url($rss->subscribe_url());
                        $theurl = $discoveredurl->out(false);
                        // Save the RSS to the database.
                        $this->newrss = new stdClass;
                        $this->newrss->userid = $USER->id;
                        $this->newrss->title = $rss->get_title();
                        $this->newrss->description = $rss->get_description();
                        $this->newrss->url = $theurl;
                        $newrssid = $DB->insert_record('block_rss_client', $this->newrss);
                        $this->newrss->id = $newrssid;
                    }
                } catch (Exception $e) {
                    $errors['config_feedurl'] = get_string('couldnotfindloadrssfeed', 'block_rss_client');
                }
            } else {
                // If the "New" method is selected, but the feed URL is empty, then raise error.
                $errors['config_feedurl'] = get_string('err_required', 'form');
            }

        }

        return $errors;
    }

    /**
     * Display the configuration form when block is being added to the page
     *
     * @return bool
     */
    public static function display_form_when_adding(): bool {
        return true;
    }
}
