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

namespace theme_snap\webservice;

use theme_snap\color_contrast;
use theme_snap\services\course;
use theme_snap\local;
use core_external\external_api;
use core_external\external_value;
use core_external\external_function_parameters;
use core_external\external_single_structure;

defined('MOODLE_INTERNAL') || die();

/**
 * Cover image web service
 * @author    gthomas2
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ws_cover_image extends external_api {
    /**
     * @return external_function_parameters
     */
    public static function service_parameters() {
        $parameters = [
            'params' => new external_single_structure([
                'imagefilename' => new external_value(PARAM_TEXT, 'Image filename', VALUE_OPTIONAL),
                'fileid' => new external_value(PARAM_INT, 'File ID', VALUE_OPTIONAL),
                'categoryid' => new external_value(PARAM_INT, 'Category Id', VALUE_OPTIONAL),
                'courseshortname' => new external_value(PARAM_TEXT, 'Course shortname', VALUE_OPTIONAL),
                'croppedimagedata' => new external_value(PARAM_TEXT, 'Cropped image data', VALUE_OPTIONAL),
                'originalimageurl' => new external_value(PARAM_TEXT, 'Original image URL', VALUE_OPTIONAL),
                'deleteimage' => new external_value(PARAM_BOOL, 'Delete image', VALUE_OPTIONAL),
                'contrastvalidation' => new external_value(PARAM_BOOL, 'Contrast validation option', VALUE_OPTIONAL),
            ], 'Params wrapper - just here to accommodate optional values', VALUE_REQUIRED),
        ];
        return new external_function_parameters($parameters);
    }

    /**
     * @return external_single_structure
     */
    public static function service_returns() {
        $keys = [
            'success' => new external_value(PARAM_BOOL, 'Was the cover image successfully changed', VALUE_REQUIRED),
            'imageurl' => new external_value(PARAM_TEXT, 'URL of the new cover image', VALUE_OPTIONAL),
            'contrast' => new external_value(PARAM_TEXT, 'The color contrast has a warning', VALUE_OPTIONAL),
        ];

        return new external_single_structure($keys, 'coverimage');
    }

    /**
     * @param string $imagedata
     * @param string $imagefilename
     * @param int $categoryid
     * @param string $courseshortname
     * @return array
     */
    public static function service($params) {
        global $USER;

        $service = course::service();
        $params = self::validate_parameters(self::service_parameters(), ['params' => $params])['params'];

        if (!empty($params['courseshortname'])) {
            $course = $service->coursebyshortname($params['courseshortname'], 'id');
            if ($course->id === SITEID) {
                $context = \context_system::instance();
            } else {
                $context = \context_course::instance($course->id);
            }
        } else if (!empty($params['categoryid'])) {
            $context = get_category_or_system_context($params['categoryid']);
        } else {
            throw new \coding_exception('Error - courseshortname OR categoryid must be provided');
        }
        self::validate_context($context);

        if (!empty($params['contrastvalidation']) && !empty($params['fileid']) && !empty($params['imagefilename'])) {
            // Validate the image contrast before saving.
            $fs = get_file_storage();
            $usercontext = \context_user::instance($USER->id);
            $filefromdraft = $fs->get_file($usercontext->id, 'user', 'draft', $params['fileid'], '/', $params['imagefilename']);
            $finfo = $filefromdraft->get_imageinfo();
            $imagemaincolor = color_contrast::calculate_image_main_color($filefromdraft, $finfo);
            $contrast = color_contrast::evaluate_color_contrast($imagemaincolor, "#FFFFFF");
            if ($context->contextlevel === CONTEXT_COURSECAT) {
                $themecolor = get_config('theme_snap', 'themecolor');
                $catconfig = get_config('theme_snap', 'category_color');
                $catscolor = [];
                $catid = $context->instanceid;
                if (!empty($catconfig)) {
                    $catscolor = json_decode($catconfig);
                }
                if (!empty($catscolor) && property_exists($catscolor, $catid)) {
                    $themecolor = $catscolor->$catid;
                }
                $catcontrast = color_contrast::evaluate_color_contrast($imagemaincolor, $themecolor);
                if ($catcontrast < 4.5) {
                    return ['success' => true, 'contrast' => get_string('imageinvalidratiocategory',
                        'theme_snap', number_format((float)$catcontrast, 2))];
                }
            }
            if ($contrast < 4.5) {
                return ['success' => true, 'contrast' => get_string('imageinvalidratio',
                    'theme_snap', number_format((float)$contrast, 2)),];
            }
            return ['success' => true];
        }

        if (!empty($params['deleteimage']) && $params['deleteimage']) {
            return self::deletecoverimage($params);
        }

        if (empty($params['imagefilename']) && empty($params['fileid']) && !empty($params['croppedimagedata'])) {
            return self::savecroppedimage($params);
        }

        $coverimage = $service->setcoverimage($context, $params['imagefilename'], $params['fileid'], $params['croppedimagedata']);

        return $coverimage;
    }

    /**
     * @return array
     */
    public static function savecroppedimage($params) {

        $service = course::service();
        $fs = get_file_storage();
        if (!empty($params['courseshortname'])) {
            $course = $service->coursebyshortname($params['courseshortname'], 'id');
            if ($course->id === SITEID) {
                $context = \context_system::instance();
                $fs->delete_area_files($context->id, 'theme_snap', 'croppedimage');
                $service->savecroppedimage($context, $params['croppedimagedata'], null, $params['originalimageurl']);
                $coverimageurl = local::site_coverimage_url();
                $coverimageurl = "url($coverimageurl);";
            } else {
                $context = \context_course::instance($course->id);
                $fs->delete_area_files($context->id, 'theme_snap', 'croppedimage');
                $service->savecroppedimage($context, $params['croppedimagedata'], null, $params['originalimageurl']);
                $coverimageurl = local::course_coverimage_url($context->instanceid);
                $coverimageurl = "url($coverimageurl);";
            }
        } else if (!empty($params['categoryid'])) {
            $context = get_category_or_system_context($params['categoryid']);
            $fs->delete_area_files($context->id, 'theme_snap', 'croppedimage');
            $service->savecroppedimage($context, $params['croppedimagedata'], null, $params['originalimageurl']);
            $coverimageurl = local::course_cat_coverimage_url($context->instanceid);
            $coverimageurl = "url($coverimageurl);";
        } else {
            throw new \coding_exception('Error - courseshortname OR categoryid must be provided');
        }

        return ['success' => true, 'imageurl'=> $coverimageurl];
    }


    /**
     * @return array
     */
    public static function deletecoverimage($params) {

        $service = course::service();
        $fs = get_file_storage();
        if (!empty($params['courseshortname'])) {
            $course = $service->coursebyshortname($params['courseshortname'], 'id');
            if ($course->id === SITEID) {
                $context = \context_system::instance();
                $fs->delete_area_files($context->id, 'theme_snap', 'coverimage');
                $fs->delete_area_files($context->id, 'theme_snap', 'poster');
                $fs->delete_area_files($context->id, 'theme_snap', 'croppedimage');
                // Purge course image cache in case if course image has been updated.
                \cache::make('core', 'course_image')->delete($context->instanceid);
            } else {
                $context = \context_course::instance($course->id);
                // Remove any old course summary image files for this context.
                $fs->delete_area_files($context->id, 'theme_snap', 'coverimage');
                $fs->delete_area_files($context->id, 'course', 'overviewfiles');
                $fs->delete_area_files($context->id, 'theme_snap', 'croppedimage');
                \cache::make('core', 'course_image')->delete($context->instanceid);
            }
        } else if (!empty($params['categoryid'])) {
            $context = get_category_or_system_context($params['categoryid']);
            $fs->delete_area_files($context->id, 'theme_snap', 'coverimage');
            $fs->delete_area_files($context->id, 'theme_snap', 'poster');
            $fs->delete_area_files($context->id, 'theme_snap', 'croppedimage');
            \cache::make('core', 'course_image')->delete($context->instanceid);
        } else {
            throw new \coding_exception('Error - courseshortname OR categoryid must be provided');
        }

        return ['success' => true];
    }
}
