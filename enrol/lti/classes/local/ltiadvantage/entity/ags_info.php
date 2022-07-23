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

namespace enrol_lti\local\ltiadvantage\entity;

/**
 * The ags_info class, instances of which represent grade service information for a resource_link or context.
 *
 * For information about Assignment and Grade Services 2.0, see https://www.imsglobal.org/spec/lti-ags/v2p0/.
 *
 * @package    enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ags_info {
    /** @var string Scope for lineitem management, used when a platform allows the tool to create lineitems.*/
    private const SCOPES_LINEITEM_MANAGE = 'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem';

    /** @var string Scope for lineitem reads, used when a tool only grants read access to line items.*/
    private const SCOPES_LINEITEM_READONLY = 'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly';

    /** @var string Scope for reading results.*/
    private const SCOPES_RESULT_READONLY = 'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly';

    /** @var string Scope for posting scores.*/
    private const SCOPES_SCORES_POST = 'https://purl.imsglobal.org/spec/lti-ags/scope/score';

    /** @var \moodle_url|null The service URL used to get/put lineitems, if supported*/
    private $lineitemsurl;

    /** @var \moodle_url|null The lineitemurl, which is only present when a single lineitem is supported.*/
    private $lineitemurl;

    /** @var array The array of supported lineitem-related scopes for this service instance.*/
    private $lineitemscopes = [];

    /** @var string|null The supported result scope for this service instance.*/
    private $resultscope = null;

    /** @var string|null The supported score scope for this service instance.*/
    private $scorescope = null;

    /**
     * The ags_info constructor.
     *
     * @param \moodle_url|null $lineitemsurl The service URL used to get/put lineitems, if supported.
     * @param \moodle_url|null $lineitemurl The lineitemurl, which is only present when a single lineitem is supported.
     * @param array $scopes The array of supported scopes for this service instance.
     */
    private function __construct(?\moodle_url $lineitemsurl, ?\moodle_url $lineitemurl, array $scopes) {

        // Platforms may support just lineitemurl, just lineitemsurl or both. At least one of the two is required.
        if (is_null($lineitemsurl) && is_null($lineitemurl)) {
            throw new \coding_exception("Missing lineitem or lineitems URL");
        }

        $this->lineitemsurl = $lineitemsurl;
        $this->lineitemurl = $lineitemurl;
        $this->validate_scopes($scopes);
    }

    /**
     * Factory method to create a new ags_info instance.
     *
     * @param \moodle_url|null $lineitemsurl The service URL used to get/put lineitems, if supported.
     * @param \moodle_url|null $lineitemurl The lineitemurl, which is only present when a single lineitem is supported.
     * @param array $scopes The array of supported scopes for this service instance.
     * @return ags_info the object instance.
     */
    public static function create(?\moodle_url $lineitemsurl = null, ?\moodle_url $lineitemurl = null,
            array $scopes = []): ags_info {
        return new self($lineitemsurl, $lineitemurl, $scopes);
    }

    /**
     * Check the supplied scopes for validity and set instance vars if appropriate.
     *
     * @param array $scopes the array of string scopes to check.
     * @throws \coding_exception if any of the scopes is invalid.
     */
    private function validate_scopes(array $scopes): void {
        $supportedscopes = [
            self::SCOPES_LINEITEM_READONLY,
            self::SCOPES_LINEITEM_MANAGE,
            self::SCOPES_RESULT_READONLY,
            self::SCOPES_SCORES_POST
        ];
        foreach ($scopes as $scope) {
            if (!is_string($scope)) {
                throw new \coding_exception('Scope must be a string value');
            }
            $key = array_search($scope, $supportedscopes);
            if ($key === 0) {
                $this->lineitemscopes[] = self::SCOPES_LINEITEM_READONLY;
            } else if ($key === 1) {
                $this->lineitemscopes[] = self::SCOPES_LINEITEM_MANAGE;
            } else if ($key === 2) {
                $this->resultscope = self::SCOPES_RESULT_READONLY;
            } else if ($key === 3) {
                $this->scorescope = self::SCOPES_SCORES_POST;
            }
        }
    }

    /**
     * Get the url for querying line items, if supported.
     *
     * @return \moodle_url the url.
     */
    public function get_lineitemsurl(): ?\moodle_url {
        return $this->lineitemsurl;
    }

    /**
     * Get the single line item url, in cases where only one line item exists.
     *
     * @return \moodle_url|null the url, or null if not present.
     */
    public function get_lineitemurl(): ?\moodle_url {
        return $this->lineitemurl;
    }

    /**
     * Get the authorization scope for lineitems.
     *
     * @return array|null the scopes, if present, else null.
     */
    public function get_lineitemscope(): ?array {
        return !empty($this->lineitemscopes) ? $this->lineitemscopes : null;
    }

    /**
     * Get the authorization scope for results.
     *
     * @return string|null the scope, if present, else null.
     */
    public function get_resultscope(): ?string {
        return $this->resultscope;
    }

    /**
     * Get the authorization scope for scores.
     *
     * @return string|null the scope, if present, else null.
     */
    public function get_scorescope(): ?string {
        return $this->scorescope;
    }

    /**
     * Get all supported scopes for this service.
     *
     * @return string[] the array of supported scopes.
     */
    public function get_scopes(): array {
        $scopes = [];
        foreach ($this->lineitemscopes as $lineitemscope) {
            $scopes[] = $lineitemscope;
        }
        if (!empty($this->resultscope)) {
            $scopes[] = $this->resultscope;
        }
        if (!empty($this->scorescope)) {
            $scopes[] = $this->scorescope;
        }
        return $scopes;
    }
}
