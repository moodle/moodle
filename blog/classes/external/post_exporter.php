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
 * Class for exporting a blog post (entry).
 *
 * @package    core_blog
 * @copyright  2018 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_blog\external;
defined('MOODLE_INTERNAL') || die();

use core\external\exporter;
use external_util;
use external_files;
use renderer_base;
use context_system;

/**
 * Class for exporting a blog post (entry).
 *
 * @copyright  2018 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class post_exporter extends exporter {

    /**
     * Return the list of properties.
     *
     * @return array list of properties
     */
    protected static function define_properties() {
        return array(
            'id' => array(
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'description' => 'Post/entry id.',
            ),
            'module' => array(
                'type' => PARAM_ALPHANUMEXT,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'Where it was published the post (blog, blog_external...).',
            ),
            'userid' => array(
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED,
                'default' => 0,
                'description' => 'Post author.',
            ),
            'courseid' => array(
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED,
                'default' => 0,
                'description' => 'Course where the post was created.',
            ),
            'groupid' => array(
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED,
                'default' => 0,
                'description' => 'Group post was created for.',
            ),
            'moduleid' => array(
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED,
                'default' => 0,
                'description' => 'Module id where the post was created (not used anymore).',
            ),
            'coursemoduleid' => array(
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED,
                'default' => 0,
                'description' => 'Course module id where the post was created.',
            ),
            'subject' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'Post subject.',
            ),
            'summary' => array(
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'description' => 'Post summary.',
            ),
            'content' => array(
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'description' => 'Post content.',
            ),
            'uniquehash' => array(
                'type' => PARAM_RAW,
                'null' => NULL_NOT_ALLOWED,
                'description' => 'Post unique hash.',
            ),
            'rating' => array(
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED,
                'default' => 0,
                'description' => 'Post rating.',
            ),
            'format' => array(
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED,
                'default' => 0,
                'description' => 'Post content format.',
            ),
            'summaryformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_MOODLE,
                'description' => 'Format for the summary field.',
            ),
            'attachment' => array(
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'description' => 'Post atachment.',
            ),
            'publishstate' => array(
                'type' => PARAM_ALPHA,
                'null' => NULL_NOT_ALLOWED,
                'default' => 'draft',
                'description' => 'Post publish state.',
            ),
            'lastmodified' => array(
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED,
                'default' => 0,
                'description' => 'When it was last modified.',
            ),
            'created' => array(
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED,
                'default' => 0,
                'description' => 'When it was created.',
            ),
            'usermodified' => array(
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'description' => 'User that updated the post.',
            ),
        );
    }

    protected static function define_related() {
        return array(
            'context' => 'context'
        );
    }

    protected static function define_other_properties() {
        return array(
            'summaryfiles' => array(
                'type' => external_files::get_properties_for_exporter(),
                'multiple' => true
            ),
            'attachmentfiles' => array(
                'type' => external_files::get_properties_for_exporter(),
                'multiple' => true,
                'optional' => true
            ),
        );
    }

    protected function get_other_values(renderer_base $output) {
        $context = context_system::instance(); // Files always on site context.

        $values['summaryfiles'] = external_util::get_area_files($context->id, 'blog', 'post', $this->data->id);
        $values['attachmentfiles'] = external_util::get_area_files($context->id, 'blog', 'attachment', $this->data->id);

        return $values;
    }
}
