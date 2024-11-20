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
 * A check result class
 *
 * @package    core
 * @category   check
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\check;

defined('MOODLE_INTERNAL') || die();

/**
 * A check object returns a result object
 *
 * Most checks can use this an instance of this directly but if you have a
 * 'details' which is computationally expensive then extend this and overide
 * the get_details() method so that it is only called when it will be needed.
 *
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class result implements \renderable {

    /**
     * This is used to notify if a check does not apply.
     *
     * In most cases if a check doesn't apply a check object shouldn't be made.
     * This state exists for when you always want visibilty of the check itself.
     * Can be useful for a check which depends on another check and it helps
     * focus on the other check which matters more.
     */
    const NA = 'na';

    /**
     * Ideally all checks should be ok.
     */
    const OK = 'ok';

    /**
     * This is used to show info for a check.
     *
     * This is equivalent to OK but could be used for alerting to potential
     * future warnings such as a deprecation in a service.
     */
    const INFO = 'info';

    /**
     * This means we could not determine the state.
     *
     * An example might be an expensive check done via cron, and it has never run.
     * It would be prudent to consider an unknown check as a warning or error.
     */
    const UNKNOWN = 'unknown';

    /**
     * Warnings
     *
     * Something is not ideal and should be addressed, eg usability or the
     * speed of the site may be affected, but it may self heal (eg a load spike)
     */
    const WARNING = 'warning';

    /**
     * This is used to notify if a check failed.
     *
     * Something is wrong with a component and a feature is not working.
     */
    const ERROR = 'error';

    /**
     * This is used to notify if a check is a major critical issue.
     *
     * An error which is affecting everyone in a major way.
     */
    const CRITICAL = 'critical';

    /**
     * @var string $status - status
     */
    protected $status = self::UNKNOWN;

    /**
     * @var string summary - should be roughly 1 line of plain text and may change depending on the state.
     */
    protected $summary = '';

    /**
     * @var string details about check.
     *
     * This may be a large amount of preformatted html text, possibly describing all the
     * different states and actions to address them.
     */
    protected $details = '';

    /**
     * Get the check reference label
     *
     * @return string must be globally unique
     */
    public function get_ref(): string {
        $ref = $this->get_component();
        if (!empty($ref)) {
            $ref .= '_';
        }
        $ref .= $this->get_id();
        return $ref;
    }

    /**
     * Constructor
     *
     * @param string $status code
     * @param string $summary a 1 liner summary
     * @param string $details as a html chunk
     */
    public function __construct($status, $summary, $details = '') {
        $this->status = $status;
        $this->summary = $summary;
        $this->details = $details;
    }

    /**
     * Get the check status
     *
     * @return string one of the consts eg result::OK
     */
    public function get_status(): string {
        return $this->status;
    }

    /**
     * Summary of the check
     * @return string formatted html
     */
    public function get_summary(): string {
        return $this->summary;
    }

    /**
     * Get the check detailed info
     * @return string formatted html
     */
    public function get_details(): string {
        return $this->details;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return array data context for a mustache template
     */
    public function export_for_template(\renderer_base $output) {
        return array(
            'status'        => clean_text(get_string('status' . $this->status)),
            'isna'          => $this->status === self::NA,
            'isok'          => $this->status === self::OK,
            'isinfo'        => $this->status === self::INFO,
            'isunknown'     => $this->status === self::UNKNOWN,
            'iswarning'     => $this->status === self::WARNING,
            'iserror'       => $this->status === self::ERROR,
            'iscritical'    => $this->status === self::CRITICAL,
        );
    }

    /**
     * Which mustache template?
     *
     * @return string path to mustache template
     */
    public function get_template_name(): string {
        return 'core/check/result';
    }
}

