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

namespace core_ai\aiactions;

use core_ai\aiactions\responses\response_base;

/**
 * Generate images class.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generate_image extends base {
    /** @var int The user id requesting the action. */
    protected int $userid;

    /** @var string The prompt text used to generate the image */
    protected string $prompttext;

    /** @var string The aspect ratio of the generated image */
    protected string $aspectratio;

    /** @var string The quality of the generated image */
    protected string $quality;

    /** @var int The number of images to generate */
    protected int $numimages;

    /** @var string The visual style of the generated image */
    protected string $style;

    /**
     * Constructor for the class.
     * It’s responsible for performing any setup tasks,
     * such as getting additional data from the database etc.
     *
     * @param int $contextid The context id the action was created in.
     * @param int $userid The user id making the request.
     * @param string $prompttext The prompt text used to generate the image.
     * @param string $quality The quality of the generated image.
     * @param string $aspectratio The aspect ratio of the generated image.
     * @param int $numimages The number of images to generate.
     * @param string $style The visual style of the generated image.
     * @return void.
     */
    public function __construct(
        int $contextid,
        int $userid,
        string $prompttext,
        string $quality,
        string $aspectratio,
        int $numimages,
        string $style
    ) {
        $this->timecreated = \core\di::get(\core\clock::class)->time();
        $this->contextid = $contextid;
        $this->userid = $userid;
        $this->prompttext = $prompttext;
        $this->quality = $quality;
        $this->aspectratio = $aspectratio;
        $this->numimages = $numimages;
        $this->style = $style;
    }

    /**
     * Store the action in the database.
     *
     * @param response_base $response The result of the action.
     * @return int The id of the stored action.
     * @throws \dml_exception
     */
    public function store(response_base $response): int {
        global $DB;

        $responsearr = $response->get_response();

        $tablename = $this->get_tablename();
        $record = new \stdClass();
        $record->prompt = $this->prompttext;
        $record->numberimages = $this->numimages;
        $record->quality = $this->quality;
        $record->aspectratio = $this->aspectratio;
        $record->style = $this->style;
        $record->sourceurl = $responsearr['sourceurl']; // Can be null.
        $record->revisedprompt = $responsearr['revisedprompt']; // Can be null.

        return $DB->insert_record($tablename, $record);
    }
}
