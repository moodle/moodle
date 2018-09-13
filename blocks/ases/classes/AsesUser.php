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
 * Ases user functions, utilities and class definition
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

require_once ('../managers/user_management/user_management_lib.php');
require_once ('../managers/lib/student_lib.php');

class AsesUser {
    public $id = -1;
    function __construct($id) {
        $this->id = $id;
    }
    /**
     * Return the user profile image URL, if not user profile image exist return empty string.
     * @param int $ases_student_id  Ases student id
     * @param int $context_block_id  Ases block context id
     * @return string Absolute URL of the profile image
     */
    public static function get_URL_profile_image(int $context_block_id,  int $ases_student_id ): string {
        $fs = get_file_storage();
        $files = $fs->get_area_files( $context_block_id, 'block_ases', 'profile_image', $ases_student_id);
        $image_file = array_pop($files);
        if (sizeof($files) == 0 ) {
            return '';
        } else {
           return       $url = moodle_url::make_pluginfile_url($image_file->get_contextid(), $image_file->get_component(), $image_file->get_filearea(), $image_file->get_itemid(), $image_file->get_filepath(), $image_file->get_filename());
        }
    }

    /**
     * Return the user profile image as a HTML <img> element, if not user profile image exist return default image.
     * @param int $ases_student_id  Ases student id
     * @param int $context_block_id  Ases block context id
     * @return string HTML <img> element
     */
    public static function get_HTML_img_profile_image(int $context_block_id,  int $ases_student_id, string $width = '100%', string $height = '', $class = ''): string {
        global $OUTPUT;
        $image_url = AsesUser::get_URL_profile_image($context_block_id,  $ases_student_id );
        if ($image_url != '') {
            return html_writer::empty_tag('img' , array('src' => $image_url, 'alt'=>'profile_image', 'width'=>$width, 'height'=>$height, 'class' => $class));
        } else {
            $mdl_user_id = get_id_user_moodle($ases_student_id);
          
            $mdl_user = \core_user::get_user($mdl_user_id, '*', MUST_EXIST);
            return $OUTPUT->user_picture($mdl_user, array('size'=>200,  'link'=> false));
        }
    }
}

?>