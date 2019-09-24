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
 * Generator for the gradingforum_guide plugin.
 *
 * @package    gradingform_guide
 * @category   test
 * @copyright  2018 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tests\gradingform_guide\generator;

/**
 * Convenience class to create guide criterion.
 *
 * @package    gradingform_guide
 * @copyright  2018 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class criterion {
    /** @var string $shortname A shortened name of the criterion. */
    private $shortname;

    /** @var string $description A description of the criterion. */
    private $description;

    /** @var string $descriptionmarkers A description of the criterion for markers. */
    private $descriptionmarkers;

    /** @var float Maximum score */
    private $maxscore = 0;

    /**
     * Constructor for this test_criterion object
     *
     * @param string $shortname The shortname for the criterion
     * @param string $description The description for the criterion
     * @param string $descriptionmarkers The description for the marker for this criterion
     * @param float $maxscore The maximum score possible for this criterion
     */
    public function __construct(string $shortname, string $description, string $descriptionmarkers, float $maxscore) {
        $this->shortname = $shortname;
        $this->description = $description;
        $this->descriptionmarkers = $descriptionmarkers;
        $this->maxscore = $maxscore;
    }

    /**
     * Get the description for this criterion.
     *
     * @return string
     */
    public function get_description(): string {
        return $this->description;
    }

    /**
     * Get the description for markers of this criterion.
     *
     * @return string
     */
    public function get_descriptionmarkers(): string {
        return $this->descriptionmarkers;
    }

    /**
     * Get the shortname for this criterion.
     *
     * @return string
     */
    public function get_shortname(): string {
        return $this->shortname;
    }

    /**
     * Get the maxscore for this criterion.
     *
     * @return float
     */
    public function get_maxscore(): float {
        return $this->maxscore;
    }

    /**
     * Get all values in an array for use when creating a new guide.
     *
     * @param int $sortorder
     * @return array
     */
    public function get_all_values(int $sortorder): array {
        return [
            'sortorder' => $sortorder,
            'shortname' => $this->get_shortname(),
            'description' => $this->get_description(),
            'descriptionmarkers' => $this->get_descriptionmarkers(),
            'maxscore' => $this->get_maxscore(),
        ];
    }
}
