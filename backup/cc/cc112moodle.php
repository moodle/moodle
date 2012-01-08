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
 * @package   moodlecore
 * @subpackage backup-imscc
 * @copyright 2011 Darko Miletic (dmiletic@moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');

require_once($CFG->dirroot . '/backup/cc/cc2moodle.php');
require_once($CFG->dirroot . '/backup/cc/entities11.class.php');
require_once($CFG->dirroot . '/backup/cc/entity11.resource.class.php');
require_once($CFG->dirroot . '/backup/cc/entity11.forum.class.php');
require_once($CFG->dirroot . '/backup/cc/entity11.quiz.class.php');
require_once($CFG->dirroot . '/backup/cc/entity11.lti.class.php');

class cc112moodle extends cc2moodle {
    const CC_TYPE_FORUM              = 'imsdt_xmlv1p1';
    const CC_TYPE_QUIZ               = 'imsqti_xmlv1p2/imscc_xmlv1p1/assessment';
    const CC_TYPE_QUESTION_BANK      = 'imsqti_xmlv1p2/imscc_xmlv1p1/question-bank';
    const CC_TYPE_WEBLINK            = 'imswl_xmlv1p1';
    const CC_TYPE_ASSOCIATED_CONTENT = 'associatedcontent/imscc_xmlv1p1/learning-application-resource';
    const CC_TYPE_BASICLTI           = 'imsbasiclti_xmlv1p0';

    public static $namespaces = array('imscc'    => 'http://www.imsglobal.org/xsd/imsccv1p1/imscp_v1p1',
                                      'lomimscc' => 'http://ltsc.ieee.org/xsd/imsccv1p1/LOM/manifest',
                                      'lom'      => 'http://ltsc.ieee.org/xsd/imsccv1p1/LOM/resource',
                                      'xsi'      => 'http://www.w3.org/2001/XMLSchema-instance',
                                      'cc'       => 'http://www.imsglobal.org/xsd/imsccv1p1/imsccauth_v1p1');

    public static $restypes = array('associatedcontent/imscc_xmlv1p1/learning-application-resource', 'webcontent');
    public static $forumns  = array('dt' => 'http://www.imsglobal.org/xsd/imsccv1p1/imsdt_v1p1');
    public static $quizns   = array('xmlns' => 'http://www.imsglobal.org/xsd/ims_qtiasiv1p2');
    public static $resourcens = array('wl' => 'http://www.imsglobal.org/xsd/imsccv1p1/imswl_v1p1');
    public static $basicltins = array(
                                       'xmlns' => 'http://www.imsglobal.org/xsd/imslticc_v1p0',
                                       'blti'  => 'http://www.imsglobal.org/xsd/imsbasiclti_v1p0',
                                       'lticm' => 'http://www.imsglobal.org/xsd/imslticm_v1p0',
                                       'lticp' => 'http://www.imsglobal.org/xsd/imslticp_v1p0'
                                      );


    public function __construct($path_to_manifest) {
        parent::__construct($path_to_manifest);
    }

    public function generate_moodle_xml () {

        global $CFG;
        $cdir = static::$path_to_manifest_folder . DIRECTORY_SEPARATOR . 'course_files';

        if (!file_exists($cdir)) {
            mkdir($cdir);
        }

        $sheet_base = static::loadsheet(SHEET_BASE);

        // MOODLE_BACKUP / INFO / DETAILS / MOD
        $node_info_details_mod = $this->create_code_info_details_mod();

        // MOODLE_BACKUP / BLOCKS / BLOCK
        $node_course_blocks_block = $this->create_node_course_blocks_block();

        // MOODLE_BACKUP / COURSES / SECTIONS / SECTION
        $node_course_sections_section = $this->create_node_course_sections_section();

        // MOODLE_BACKUP / COURSES / QUESTION_CATEGORIES
        $node_course_question_categories = $this->create_node_question_categories();

        // MOODLE_BACKUP / COURSES / MODULES / MOD
        $node_course_modules_mod = $this->create_node_course_modules_mod();

        // MOODLE_BACKUP / COURSE / HEADER
        $node_course_header = $this->create_node_course_header();

        // GENERAL INFO
        $filename = optional_param('file', 'not_available.zip', PARAM_RAW);
        $filename = basename($filename);

        $www_root = $CFG->wwwroot;

        $find_tags = array('[#zip_filename#]',
                               '[#www_root#]',
                               '[#node_course_header#]',
                               '[#node_info_details_mod#]',
                               '[#node_course_blocks_block#]',
                               '[#node_course_sections_section#]',
                               '[#node_course_question_categories#]',
                               '[#node_course_modules#]');

        $replace_values = array($filename,
        $www_root,
        $node_course_header,
        $node_info_details_mod,
        $node_course_blocks_block,
        $node_course_sections_section,
        $node_course_question_categories,
        $node_course_modules_mod);

        $result_xml = str_replace($find_tags, $replace_values, $sheet_base);

        // COPY RESOURSE FILES
        $entities = new entities11();

        $entities->move_all_files();

        if (array_key_exists("index", self::$instances)) {

            if (!file_put_contents(static::$path_to_manifest_folder . DIRECTORY_SEPARATOR . 'moodle.xml', $result_xml)) {
                static::log_action('Cannot save the moodle manifest file: ' . static::$path_to_tmp_folder . DIRECTORY_SEPARATOR . 'moodle.xml', true);
            } else {
                $status = true;
            }

        } else {
            $status = false;
            static::log_action('The course is empty', false);
        }

        return $status;

    }

    public function convert_to_moodle_type ($cc_type) {
        $type = parent::convert_to_moodle_type($cc_type);

        if ($type == TYPE_UNKNOWN) {
            if ($cc_type == static::CC_TYPE_BASICLTI) {
                $type = MOODLE_TYPE_LTI;
            }
        }

        return $type;
    }

    protected function create_node_question_categories () {

        $quiz = new cc11_quiz();

        static::log_action('Creating node: QUESTION_CATEGORIES');

        $node_course_question_categories = $quiz->generate_node_question_categories();

        return $node_course_question_categories;
    }

    protected function create_code_info_details_mod () {
        $result = parent::create_code_info_details_mod();

        $count_blti = $this->count_instances(MOODLE_TYPE_LTI);

        $sheet_info_details_mod_instances_instance = static::loadsheet(SHEET_INFO_DETAILS_MOD_INSTANCE);

        $blti_mod = '';

        if ($count_blti > 0) {
            $blti_instance = $this->create_mod_info_details_mod_instances_instance($sheet_info_details_mod_instances_instance, $count_blti, static::$instances['instances'][MOODLE_TYPE_LTI]);
            $blti_mod = $blti_instance ? $this->create_mod_info_details_mod(MOODLE_TYPE_LTI, $blti_instance) : '';
        }

        return $result . $blti_mod;
    }

    /**
    * (non-PHPdoc)
    * @see cc2moodle::get_module_visible()
    */
    protected function get_module_visible($identifier) {
        //Should item be hidden or not
        $mod_visible = 1;
        if (!empty($identifier)) {
            $xpath = static::newx_path(static::$manifest, static::$namespaces);
            $query  = '/imscc:manifest/imscc:resources/imscc:resource[@identifier="' . $identifier . '"]';
            $query .= '//lom:intendedEndUserRole/lom:value';
            $intendeduserrole = $xpath->query($query);
            if (!empty($intendeduserrole) && ($intendeduserrole->length > 0)) {
                $role = trim($intendeduserrole->item(0)->nodeValue);
                if ((strcasecmp('Instructor', $role) === 0) || (strcasecmp('Mentor', $role) === 0)) {
                    $mod_visible = 0;
                }
            }
        }
        return $mod_visible;
    }

    protected function create_node_course_modules_mod () {
        $labels = new cc_label();
        $resources = new cc11_resource();
        $forums = new cc11_forum();
        $quiz = new cc11_quiz();
        $basiclti = new cc11_lti();

        static::log_action('Creating node: COURSE/MODULES/MOD');

        // LABELS
        $node_course_modules_mod_label = $labels->generate_node();

        // RESOURCES (WEB CONTENT AND WEB LINK)
        $node_course_modules_mod_resource = $resources->generate_node();

        // FORUMS
        $node_course_modules_mod_forum = $forums->generate_node();

        // QUIZ
        $node_course_modules_mod_quiz = $quiz->generate_node_course_modules_mod();

        //BasicLTI
        $node_course_modules_mod_basiclti = $basiclti->generate_node();

        $node_course_modules = $node_course_modules_mod_label.
                               $node_course_modules_mod_resource .
                               $node_course_modules_mod_forum .
                               $node_course_modules_mod_quiz .
                               $node_course_modules_mod_basiclti;

        return $node_course_modules;
    }

}
