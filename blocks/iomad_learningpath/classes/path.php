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
 * Utility class for learning path block
 *
 * @package    block_iomad_learningpath
 * @copyright  2018 Howard Miller (howardsmiller@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_iomad_learningpath;

defined('MOODLE_INTERNAL') || die();

class path {

    protected $companyid;

    protected $context;

    public function __construct($companyid, $context) {
        $this->companyid = $companyid;
        $this->context = $context;
    }

    /**
     * Get available learning paths for user
     * @param int $userid
     * @return array
     */
    public function get_user_paths($userid) {
        global $DB;

        $sql = 'SELECT lp.* FROM {iomad_learningpath} lp
            JOIN {iomad_learningpathuser} lpu ON lpu.pathid = lp.id
            WHERE lp.company = :companyid
            AND lpu.userid = :userid
            ORDER BY lp.name ASC';
        $paths = $DB->get_records_sql($sql, ['userid' => $userid, 'companyid' => $this->companyid]);

        // Add url for image
        foreach ($paths as $path) {
            $path->imageurl = $this->get_path_image_url($path->id);
        }

        return $paths; 
    }

    /**
     * Get path image url
     * @param int $pathid
     * @return mixed url or false if no image
     */
    public function get_path_image_url($pathid) {

        $fs = get_file_storage();
        $pic = $fs->get_file(
            $this->context->id,
            'local_iomad_learningpath',
            'mainpicture',
            $pathid,
            '/',
            'picture.png'
        );

        return \moodle_url::make_pluginfile_url($pic->get_contextid(), $pic->get_component(), $pic->get_filearea(),
                    $pic->get_itemid(), $pic->get_filepath(), $pic->get_filename());
    }

}
