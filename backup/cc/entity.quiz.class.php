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
 * @copyright 2009 Mauro Rondinelli (mauro.rondinelli [AT] uvcms.com)
 * @copyright 2011 Darko Miletic (dmiletic@moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');

class cc_quiz extends entities {

    public function generate_node_question_categories () {

        $instances = $this->generate_instances();

        $node_course_question_categories = $this->create_node_course_question_categories($instances);
        $node_course_question_categories = empty($node_course_question_categories) ? '' : $node_course_question_categories;

        return $node_course_question_categories;

    }

    public function generate_node_course_modules_mod () {

        cc2moodle::log_action('Creating Quiz mods');

        $node_course_modules_mod = '';
        $instances = $this->generate_instances();

        if (!empty($instances)) {
            foreach ($instances as $instance) {
                if ($instance['is_question_bank'] == 0) {
                    $node_course_modules_mod .= $this->create_node_course_modules_mod($instance);
                }
            }
        }

        return $node_course_modules_mod;

    }

    private function create_node_course_modules_mod_quiz_feedback () {

        $sheet_question_mod_feedback = cc2moodle::loadsheet(SHEET_COURSE_SECTIONS_SECTION_MODS_MOD_QUIZ_FEEDBACK);

        return $sheet_question_mod_feedback;
    }

    private function generate_instances () {

        $last_instance_id = 0;
        $last_question_id = 0;
        $last_answer_id = 0;

        $instances = '';

        $types = array(MOODLE_TYPE_QUIZ, MOODLE_TYPE_QUESTION_BANK);

        foreach ($types as $type) {

            if (!empty(cc2moodle::$instances['instances'][$type])) {

                foreach (cc2moodle::$instances['instances'][$type] as $instance) {

                    if ($type == MOODLE_TYPE_QUIZ) {
                        $is_question_bank = 0;
                    } else {
                        $is_question_bank = 1;
                    }

                    $assessment_file = $this->get_external_xml($instance['resource_indentifier']);

                    if (!empty($assessment_file)) {

                        $assessment = $this->load_xml_resource(cc2moodle::$path_to_manifest_folder . DIRECTORY_SEPARATOR . $assessment_file);

                        if (!empty($assessment)) {

                            $replace_values = array('unlimited' => 0);

                            $questions = $this->get_questions($assessment, $last_question_id, $last_answer_id, dirname($assessment_file), $is_question_bank);
                            $question_count = count($questions);

                            if (!empty($question_count)) {

                                $last_instance_id++;

                                $instances[$instance['resource_indentifier']]['questions'] = $questions;
                                $instances[$instance['resource_indentifier']]['id'] = $last_instance_id;
                                $instances[$instance['resource_indentifier']]['title'] = $instance['title'];
                                $instances[$instance['resource_indentifier']]['is_question_bank'] = $is_question_bank;
                                $instances[$instance['resource_indentifier']]['options']['timelimit'] = $this->get_global_config($assessment, 'qmd_timelimit', 0);
                                $instances[$instance['resource_indentifier']]['options']['max_attempts'] = $this->get_global_config($assessment, 'cc_maxattempts', 0, $replace_values);
                            }
                        }
                    }
                }
            }
        }

        return $instances;
    }


    private function create_node_course_modules_mod ($instance) {

        $sheet_question_mod = cc2moodle::loadsheet(SHEET_COURSE_SECTIONS_SECTION_MODS_MOD_QUIZ);

        $node_course_modules_quiz_question_instances = $this->create_node_course_modules_mod_quiz_question_instances($instance);
        $node_course_modules_quiz_feedback = $this->create_node_course_modules_mod_quiz_feedback($instance);

        $questions_strings = $this->get_questions_string($instance);
        $quiz_stamp = 'localhost+' . time() . '+' . $this->generate_random_string(6);

        $find_tags = array('[#mod_id#]',
                           '[#mod_name#]',
                           '[#mod_intro#]',
                           '[#mod_stamp#]',
                           '[#question_string#]',
                           '[#date_now#]',
                           '[#mod_max_attempts#]',
                           '[#mod_timelimit#]',
                           '[#node_question_instance#]',
                           '[#node_questions_feedback#]');

        $replace_values = array($instance['id'],
                                self::safexml($instance['title']),
                                self::safexml($instance['title']),
                                self::safexml($quiz_stamp),
                                self::safexml($questions_strings),
                                time(),
                                $instance['options']['max_attempts'],
                                $instance['options']['timelimit'],
                                $node_course_modules_quiz_question_instances,
                                $node_course_modules_quiz_feedback); //this one has tags

        $node_question_mod = str_replace($find_tags, $replace_values, $sheet_question_mod);

        return $node_question_mod;
    }

    private function get_global_config ($assessment, $option, $default_value, $replace_values = '') {

        $xpath = cc2moodle::newx_path($assessment, cc2moodle::getquizns());
        $metadata = $xpath->query('/xmlns:questestinterop/xmlns:assessment/xmlns:qtimetadata/xmlns:qtimetadatafield');

        foreach ($metadata as $field) {
            $field_label = $xpath->query('xmlns:fieldlabel', $field);
            $field_label = !empty($field_label->item(0)->nodeValue) ? $field_label->item(0)->nodeValue : '';

            if (strtolower($field_label) == strtolower($option)) {
                $field_entry = $xpath->query('xmlns:fieldentry', $field);
                $response = !empty($field_entry->item(0)->nodeValue) ? $field_entry->item(0)->nodeValue : '';
            }
        }

        $response = !empty($response) ? trim($response) : '';

        if (!empty($replace_values)) {
            foreach ($replace_values as $key => $value) {
                $response = ($key == $response) ? $value : $response;
            }
        }

        $response = empty($response) ? $default_value : $response;

        return $response;
    }

    private function create_node_course_modules_mod_quiz_question_instances ($instance) {

        $node_course_module_mod_quiz_questions_instances = '';
        $sheet_question_mod_instance = cc2moodle::loadsheet(SHEET_COURSE_SECTIONS_SECTION_MODS_MOD_QUIZ_QUESTION_INSTANCE);

        $find_tags = array('[#question_id#]' , '[#instance_id#]');

        if (!empty($instance['questions'])) {

            foreach ($instance['questions'] as $question) {
                $replace_values = array($question['id'] , $question['id']);
                $node_course_module_mod_quiz_questions_instances .= str_replace($find_tags, $replace_values, $sheet_question_mod_instance);
            }

            $node_course_module_mod_quiz_questions_instances = str_replace($find_tags, $replace_values, $node_course_module_mod_quiz_questions_instances);
        }

        return $node_course_module_mod_quiz_questions_instances;
    }

    private function get_questions_string ($instance) {

        $questions_string = '';

        if (!empty($instance['questions'])) {
            foreach ($instance['questions'] as $question) {
                $questions_string .= $question['id'] . ',';
            }
        }

        $questions_string = !empty($questions_string) ? substr($questions_string, 0, strlen($questions_string) - 1) : '';

        return $questions_string;
    }

    private function create_node_course_question_categories ($instances) {

        $sheet_question_categories = cc2moodle::loadsheet(SHEET_COURSE_QUESTION_CATEGORIES);

        if (!empty($instances)) {

            $node_course_question_categories_question_category = '';

            foreach ($instances as $instance) {
                $node_course_question_categories_question_category .= $this->create_node_course_question_categories_question_category($instance);
            }

            $find_tags = array('[#node_course_question_categories_question_category#]');
            $replace_values = array($node_course_question_categories_question_category);

            $node_course_question_categories = str_replace($find_tags, $replace_values, $sheet_question_categories);
        }

        $node_course_question_categories = empty($node_course_question_categories) ? '' : $node_course_question_categories;

        return $node_course_question_categories;
    }

    private function create_node_course_question_categories_question_category ($instance) {

        $sheet_question_categories_quetion_category = cc2moodle::loadsheet(SHEET_COURSE_QUESTION_CATEGORIES_QUESTION_CATEGORY);

        $find_tags = array('[#quiz_id#]',
                           '[#quiz_name#]',
                           '[#quiz_stamp#]',
                           '[#node_course_question_categories_question_category_questions#]');

        $node_course_question_categories_questions = $this->create_node_course_question_categories_question_category_question($instance);
        $node_course_question_categories_questions = empty($node_course_question_categories_questions) ? '' : $node_course_question_categories_questions;

        $quiz_stamp = 'localhost+' . time() . '+' . $this->generate_random_string(6);

        $replace_values = array($instance['id'],
                                self::safexml($instance['title']),
                                $quiz_stamp,
                                $node_course_question_categories_questions);

        $node_question_categories = str_replace($find_tags, $replace_values, $sheet_question_categories_quetion_category);

        return $node_question_categories;
    }

    private function create_node_course_question_categories_question_category_question ($instance) {

        global $USER;

        $node_course_question_categories_question = '';

        $find_tags = array('[#question_id#]',
                           '[#question_title#]',
                           '[#question_text#]',
                           '[#question_type#]',
                           '[#question_general_feedback#]',
                           '[#question_defaultgrade#]',
                           '[#date_now#]',
                           '[#question_type_nodes#]',
                           '[#question_stamp#]',
                           '[#question_version#]',
                           '[#logged_user#]');

        $sheet_question_categories_question = cc2moodle::loadsheet(SHEET_COURSE_QUESTION_CATEGORIES_QUESTION_CATEGORY_QUESTION);

        $questions = $instance['questions'];

        if (!empty($questions)) {

            foreach ($questions as $question) {

                $quiz_stamp = 'localhost+' . time() . '+' . $this->generate_random_string(6);
                $quiz_version = 'localhost+' . time() . '+' . $this->generate_random_string(6);

                $question_moodle_type = $question['moodle_type'];
                $question_cc_type = $question['cc_type'];

                $question_type_node = '';

                $question_type_node = ($question_moodle_type == MOODLE_QUIZ_MULTIPLE_CHOICE) ? $this->create_node_course_question_categories_question_category_question_multiple_choice($question) : $question_type_node;
                $question_type_node = ($question_moodle_type == MOODLE_QUIZ_TRUE_FALSE) ? $this->create_node_course_question_categories_question_category_question_true_false($question) : $question_type_node;
                $question_type_node = ($question_moodle_type == MOODLE_QUIZ_ESSAY) ? $this->create_node_course_question_categories_question_category_question_eesay($question) : $question_type_node;
                $question_type_node = ($question_moodle_type == MOODLE_QUIZ_SHORTANSWER) ? $this->create_node_course_question_categories_question_category_question_shortanswer($question) : $question_type_node;

                $replace_values = array($question['id'],
                                        self::safexml($this->truncate_text($question['title'], 255, true)),
                                        self::safexml($question['title']),
                                        $question_moodle_type,
                                        self::safexml($question['feedback']),
                                        $question['defaultgrade'], //default grade
                                        time(),
                                        $question_type_node,
                                        $quiz_stamp,
                                        $quiz_version,
                                        $USER->id);

                $node_course_question_categories_question .= str_replace($find_tags, $replace_values, $sheet_question_categories_question);
            }
        }

        $node_course_question_categories_question = empty($node_course_question_categories_question) ? '' : $node_course_question_categories_question;

        return $node_course_question_categories_question;
    }

    private function get_questions ($assessment, &$last_question_id, &$last_answer_id, $root_path, $is_question_bank) {

        $questions = array();

        $xpath = cc2moodle::newx_path($assessment, cc2moodle::getquizns());

        if (!$is_question_bank) {
            $questions_items = $xpath->query('/xmlns:questestinterop/xmlns:assessment/xmlns:section/xmlns:item');
        } else {
            $questions_items = $xpath->query('/xmlns:questestinterop/xmlns:objectbank/xmlns:item');
        }

        foreach ($questions_items as $question_item) {

            $count_questions = $xpath->evaluate('count(xmlns:presentation/xmlns:flow/xmlns:material/xmlns:mattext)', $question_item);

            if ($count_questions == 0) {
                $question_title = $xpath->query('xmlns:presentation/xmlns:material/xmlns:mattext', $question_item);
            } else {
                $question_title = $xpath->query('xmlns:presentation/xmlns:flow/xmlns:material/xmlns:mattext', $question_item);
            }

            $question_title = !empty($question_title->item(0)->nodeValue) ? $question_title->item(0)->nodeValue : '';

            $question_identifier = $xpath->query('@ident', $question_item);
            $question_identifier = !empty($question_identifier->item(0)->nodeValue) ? $question_identifier->item(0)->nodeValue : '';

            if (!empty($question_identifier)) {

                $question_type = $this->get_question_type($question_identifier, $assessment);

                if (!empty($question_type['moodle'])) {

                    $last_question_id++;

                    $questions[$question_identifier]['id'] = $last_question_id;

                    $question_title = $this->update_sources($question_title, $root_path);
                    $question_title = !empty($question_title) ? str_replace("%24", "\$", $this->include_titles($question_title)) : '';

                    $questions[$question_identifier]['title'] = $question_title;
                    $questions[$question_identifier]['identifier'] = $question_identifier;
                    $questions[$question_identifier]['moodle_type'] = $question_type['moodle'];
                    $questions[$question_identifier]['cc_type'] = $question_type['cc'];
                    $questions[$question_identifier]['feedback'] = $this->get_general_feedback($assessment, $question_identifier);
                    $questions[$question_identifier]['defaultgrade'] = $this->get_defaultgrade($assessment, $question_identifier);
                    $questions[$question_identifier]['answers'] = $this->get_answers($question_identifier, $assessment, $last_answer_id);

                }
            }
        }

        $questions = !empty($questions) ? $questions : '';

        return $questions;
    }

    private function str_replace_once ($search, $replace, $subject) {

        $first_char = strpos($subject, $search);

        if ($first_char !== false) {

            $before_str = substr($subject, 0, $first_char);
            $after_str = substr($subject, $first_char + strlen($search));

            return $before_str . $replace . $after_str;

        } else {
            return $subject;
        }
    }

    private function get_defaultgrade($assessment, $question_identifier) {
        $result = 1;
        $xpath = cc2moodle::newx_path($assessment, cc2moodle::getquizns());
        $query = '//xmlns:item[@ident="' . $question_identifier . '"]';
        $query .= '//xmlns:qtimetadatafield[xmlns:fieldlabel="cc_weighting"]/xmlns:fieldentry';
        $defgrade = $xpath->query($query);
        if (!empty($defgrade) && ($defgrade->length > 0)) {
            $resp = (int)$defgrade->item(0)->nodeValue;
            if ($resp >= 0 && $resp <= 99) {
                $result = $resp;
            }
        }
        return $result;
    }

    private function get_general_feedback ($assessment, $question_identifier) {

        $xpath = cc2moodle::newx_path($assessment, cc2moodle::getquizns());

        $respconditions = $xpath->query('//xmlns:item[@ident="' . $question_identifier . '"]/xmlns:resprocessing/xmlns:respcondition');

        if (!empty($respconditions)) {

            foreach ($respconditions as $respcondition) {

                $continue = $respcondition->getAttributeNode('continue');
                $continue = !empty($continue->nodeValue) ? strtolower($continue->nodeValue) : '';

                if ($continue == 'yes') {

                    $display_feedback = $xpath->query('xmlns:displayfeedback', $respcondition);

                    if (!empty($display_feedback)) {
                        foreach ($display_feedback as $feedback) {

                            $feedback_identifier = $feedback->getAttributeNode('linkrefid');
                            $feedback_identifier = !empty($feedback_identifier->nodeValue) ? $feedback_identifier->nodeValue : '';

                            if (!empty($feedback_identifier)) {
                                $feedbacks_identifiers[] = $feedback_identifier;
                            }
                        }
                    }
                }
            }
        }

        $feedback = '';
        $feedbacks_identifiers = empty($feedbacks_identifiers) ? '' : $feedbacks_identifiers;

        if (!empty($feedbacks_identifiers)) {
            foreach ($feedbacks_identifiers as $feedback_identifier) {
                $feedbacks = $xpath->query('//xmlns:item[@ident="' . $question_identifier . '"]/xmlns:itemfeedback[@ident="' . $feedback_identifier . '"]/xmlns:flow_mat/xmlns:material/xmlns:mattext');
                $feedback .= !empty($feedbacks->item(0)->nodeValue) ? $feedbacks->item(0)->nodeValue . ' ' : '';
            }
        }

        return $feedback;
    }

    private function get_feedback ($assessment, $identifier, $item_identifier, $question_type) {

        $xpath = cc2moodle::newx_path($assessment, cc2moodle::getquizns());

        $resource_processing = $xpath->query('//xmlns:item[@ident="' . $item_identifier . '"]/xmlns:resprocessing/xmlns:respcondition');

        if (!empty($resource_processing)) {

            foreach ($resource_processing as $response) {

                $varequal = $xpath->query('xmlns:conditionvar/xmlns:varequal', $response);
                $varequal = !empty($varequal->item(0)->nodeValue) ? $varequal->item(0)->nodeValue : '';

                if (strtolower($varequal) == strtolower($identifier) || ($question_type == CC_QUIZ_ESSAY)) {

                    $display_feedback = $xpath->query('xmlns:displayfeedback', $response);

                    if (!empty($display_feedback)) {
                        foreach ($display_feedback as $feedback) {

                            $feedback_identifier = $feedback->getAttributeNode('linkrefid');
                            $feedback_identifier = !empty($feedback_identifier->nodeValue) ? $feedback_identifier->nodeValue : '';

                            if (!empty($feedback_identifier)) {
                                $feedbacks_identifiers[] = $feedback_identifier;
                            }
                        }
                    }
                }
            }
        }

        $feedback = '';
        $feedbacks_identifiers = empty($feedbacks_identifiers) ? '' : $feedbacks_identifiers;

        if (!empty($feedbacks_identifiers)) {
            foreach ($feedbacks_identifiers as $feedback_identifier) {
                $feedbacks = $xpath->query('//xmlns:item[@ident="' . $item_identifier . '"]/xmlns:itemfeedback[@ident="' . $feedback_identifier . '"]/xmlns:flow_mat/xmlns:material/xmlns:mattext');
                $feedback .= !empty($feedbacks->item(0)->nodeValue) ? $feedbacks->item(0)->nodeValue . ' ' : '';
            }
        }

        return $feedback;
    }

    private function get_answers_fib ($question_identifier, $identifier, $assessment, &$last_answer_id) {

        $xpath = cc2moodle::newx_path($assessment, cc2moodle::getquizns());

        $answers_fib = array();

        $response_items = $xpath->query('//xmlns:item[@ident="' . $question_identifier . '"]/xmlns:resprocessing/xmlns:respcondition');

        foreach ($response_items as $response_item) {

            $setvar = $xpath->query('xmlns:setvar', $response_item);
            $setvar = is_object($setvar->item(0)) ? $setvar->item(0)->nodeValue : '';

            if ($setvar != '') {

                $last_answer_id++;

                $answer_title = $xpath->query('xmlns:conditionvar/xmlns:varequal[@respident="' . $identifier . '"]', $response_item);
                $answer_title = !empty($answer_title->item(0)->nodeValue) ? $answer_title->item(0)->nodeValue : '';

            $case = $xpath->query('xmlns:conditionvar/xmlns:varequal/@case', $response_item);
            $case = is_object($case->item(0)) ? $case->item(0)->nodeValue : 'no'
                                    ;
            $case = strtolower($case) == 'yes' ? 1 :
                            0;

                $display_feedback = $xpath->query('xmlns:displayfeedback', $response_item);

                unset($feedbacks_identifiers);

                if (!empty($display_feedback)) {

                    foreach ($display_feedback as $feedback) {

                        $feedback_identifier = $feedback->getAttributeNode('linkrefid');
                        $feedback_identifier = !empty($feedback_identifier->nodeValue) ? $feedback_identifier->nodeValue : '';

                        if (!empty($feedback_identifier)) {
                            $feedbacks_identifiers[] = $feedback_identifier;
                        }
                    }
                }

                $feedback = '';
                $feedbacks_identifiers = empty($feedbacks_identifiers) ? '' : $feedbacks_identifiers;

                if (!empty($feedbacks_identifiers)) {
                    foreach ($feedbacks_identifiers as $feedback_identifier) {
                        $feedbacks = $xpath->query('//xmlns:item[@ident="' . $question_identifier . '"]/xmlns:itemfeedback[@ident="' . $feedback_identifier . '"]/xmlns:flow_mat/xmlns:material/xmlns:mattext');
                        $feedback .= !empty($feedbacks->item(0)->nodeValue) ? $feedbacks->item(0)->nodeValue . ' ' : '';
                    }
                }

                $answers_fib[] = array('id' => $last_answer_id,
                                       'title' => $answer_title,
                                       'score' => $setvar,
                                       'feedback' => $feedback,
                                       'case' => $case);
            }
        }

        $answers_fib = empty($answers_fib) ? '' : $answers_fib;

        return $answers_fib;
    }

    private function get_answers_pattern_match ($question_identifier, $identifier, $assessment, &$last_answer_id) {

        $xpath = cc2moodle::newx_path($assessment, cc2moodle::getquizns());

        $answers_fib = array();

        $response_items = $xpath->query('//xmlns:item[@ident="' . $question_identifier . '"]/xmlns:resprocessing/xmlns:respcondition');

        foreach ($response_items as $response_item) {

            $setvar = $xpath->query('xmlns:setvar', $response_item);
            $setvar = is_object($setvar->item(0)) ? $setvar->item(0)->nodeValue : '';

            if ($setvar != '') {

                $last_answer_id++;

                $answer_title = $xpath->query('xmlns:conditionvar/xmlns:varequal[@respident="' . $identifier . '"]', $response_item);
                $answer_title = !empty($answer_title->item(0)->nodeValue) ? $answer_title->item(0)->nodeValue : '';

                if (empty($answer_title)) {
                    $answer_title = $xpath->query('xmlns:conditionvar/xmlns:varsubstring[@respident="' . $identifier . '"]', $response_item);
                    $answer_title = !empty($answer_title->item(0)->nodeValue) ? '*' . $answer_title->item(0)->nodeValue . '*' : '';
                }

                if (empty($answer_title)) {
                    $answer_title = '*';
                }

            $case = $xpath->query('xmlns:conditionvar/xmlns:varequal/@case', $response_item);
            $case = is_object($case->item(0)) ? $case->item(0)->nodeValue : 'no'
                                    ;
            $case = strtolower($case) == 'yes' ? 1 :
                            0;

                $display_feedback = $xpath->query('xmlns:displayfeedback', $response_item);

                unset($feedbacks_identifiers);

                if (!empty($display_feedback)) {

                    foreach ($display_feedback as $feedback) {

                        $feedback_identifier = $feedback->getAttributeNode('linkrefid');
                        $feedback_identifier = !empty($feedback_identifier->nodeValue) ? $feedback_identifier->nodeValue : '';

                        if (!empty($feedback_identifier)) {
                            $feedbacks_identifiers[] = $feedback_identifier;
                        }
                    }
                }

                $feedback = '';
                $feedbacks_identifiers = empty($feedbacks_identifiers) ? '' : $feedbacks_identifiers;

                if (!empty($feedbacks_identifiers)) {
                    foreach ($feedbacks_identifiers as $feedback_identifier) {
                        $feedbacks = $xpath->query('//xmlns:item[@ident="' . $question_identifier . '"]/xmlns:itemfeedback[@ident="' . $feedback_identifier . '"]/xmlns:flow_mat/xmlns:material/xmlns:mattext');
                        $feedback .= !empty($feedbacks->item(0)->nodeValue) ? $feedbacks->item(0)->nodeValue . ' ' : '';
                    }
                }

                $answers_fib[] = array('id' => $last_answer_id,
                                       'title' => $answer_title,
                                       'score' => $setvar,
                                       'feedback' => $feedback,
                                       'case' => $case);
            }
        }

        $answers_fib = empty($answers_fib) ? '' : $answers_fib;

        return $answers_fib;
    }


    private function get_answers ($identifier, $assessment, &$last_answer_id) {

        $xpath = cc2moodle::newx_path($assessment, cc2moodle::getquizns());

        $answers = array();

        $question_cc_type = $this->get_question_type($identifier, $assessment);
        $question_cc_type = $question_cc_type['cc'];

        if ($question_cc_type == CC_QUIZ_MULTIPLE_CHOICE || $question_cc_type == CC_QUIZ_MULTIPLE_RESPONSE || $question_cc_type == CC_QUIZ_TRUE_FALSE) {

            $query_answers = '//xmlns:item[@ident="' . $identifier . '"]/xmlns:presentation/xmlns:response_lid/xmlns:render_choice/xmlns:response_label';
            $query_answers_with_flow = '//xmlns:item[@ident="' . $identifier . '"]/xmlns:presentation/xmlns:flow/xmlns:response_lid/xmlns:render_choice/xmlns:response_label';

            $query_indentifer = '@ident';
            $query_title = 'xmlns:material/xmlns:mattext';
        }

        if ($question_cc_type == CC_QUIZ_ESSAY) {

            $query_answers = '//xmlns:item[@ident="' . $identifier . '"]/xmlns:presentation/xmlns:response_str';
            $query_answers_with_flow = '//xmlns:item[@ident="' . $identifier . '"]/xmlns:presentation/xmlns:flow/xmlns:response_str';

            $query_indentifer = '@ident';
            $query_title = 'xmlns:render_fib';
        }

        if ($question_cc_type == CC_QUIZ_FIB || $question_cc_type == CC_QUIZ_PATTERN_MACHT) {

            $xpath_query = '//xmlns:item[@ident="' . $identifier . '"]/xmlns:presentation/xmlns:response_str/@ident';
            $xpath_query_with_flow = '//xmlns:item[@ident="' . $identifier . '"]/xmlns:presentation/xmlns:flow/xmlns:response_str/@ident';

            $count_response = $xpath->evaluate('count(' . $xpath_query_with_flow . ')');

            if ($count_response == 0) {
                $answer_identifier = $xpath->query($xpath_query);
            } else {
                $answer_identifier = $xpath->query($xpath_query_with_flow);
            }

            $answer_identifier = !empty($answer_identifier->item(0)->nodeValue) ? $answer_identifier->item(0)->nodeValue : '';

            if ($question_cc_type == CC_QUIZ_FIB) {
                $answers = $this->get_answers_fib ($identifier, $answer_identifier, $assessment, $last_answer_id);
            } else {
                $answers = $this->get_answers_pattern_match ($identifier, $answer_identifier, $assessment, $last_answer_id);
            }

        } else {

            $count_response = $xpath->evaluate('count(' . $query_answers_with_flow . ')');

            if ($count_response == 0) {
                $response_items = $xpath->query($query_answers);
            } else {
                $response_items = $xpath->query($query_answers_with_flow);
            }

            if (!empty($response_items)) {

                foreach ($response_items as $response_item) {

                    $last_answer_id++;

                    $answer_identifier = $xpath->query($query_indentifer, $response_item);
                    $answer_identifier = !empty($answer_identifier->item(0)->nodeValue) ? $answer_identifier->item(0)->nodeValue : '';

                    $answer_title = $xpath->query($query_title, $response_item);
                    $answer_title = !empty($answer_title->item(0)->nodeValue) ? $answer_title->item(0)->nodeValue : '';

                    $answer_feedback = $this->get_feedback($assessment, $answer_identifier, $identifier, $question_cc_type);

                    $answer_score = $this->get_score($assessment, $answer_identifier, $identifier);

                    $answers[] = array('id' => $last_answer_id,
                                       'title' => $answer_title,
                                       'score' => $answer_score,
                                       'identifier' => $answer_identifier,
                                       'feedback' => $answer_feedback);
                }
            }
        }

        $answers = empty($answers) ? '' : $answers;

        return $answers;

    }

    private function get_score ($assessment, $identifier, $question_identifier) {

        $xpath = cc2moodle::newx_path($assessment, cc2moodle::getquizns());

        $resource_processing = $xpath->query('//xmlns:item[@ident="' . $question_identifier . '"]/xmlns:resprocessing/xmlns:respcondition');

        if (!empty($resource_processing)) {

            foreach ($resource_processing as $response) {

                $question_cc_type = $this->get_question_type($question_identifier, $assessment);
                $question_cc_type = $question_cc_type['cc'];

                $varequal = $xpath->query('xmlns:conditionvar/xmlns:varequal', $response);
                $varequal = !empty($varequal->item(0)->nodeValue) ? $varequal->item(0)->nodeValue : '';

                if (strtolower($varequal) == strtolower($identifier)) {
                    $score = $xpath->query('xmlns:setvar', $response);
                    $score = !empty($score->item(0)->nodeValue) ? $score->item(0)->nodeValue : '';
                }
            }
        }

        $score = empty($score) ? 0 : $score;

        return $score;
    }

    private function create_node_course_question_categories_question_category_question_multiple_choice ($question) {

        $node_course_question_categories_question_answer = '';
        $sheet_question_categories_question = cc2moodle::loadsheet(SHEET_COURSE_QUESTION_CATEGORIES_QUESTION_CATEGORY_QUESTION_MULTIPLE_CHOICE);

        if (!empty($question['answers'])) {
            foreach ($question['answers'] as $answer) {
                $node_course_question_categories_question_answer .= $this->create_node_course_question_categories_question_category_question_answer($answer);
            }
        }

        $answer_string = $this->get_answers_string($question['answers']);

        $is_single = ($question['cc_type'] == CC_QUIZ_MULTIPLE_CHOICE) ? 1 : 0;

        $find_tags = array('[#node_course_question_categories_question_category_question_answer#]',
                           '[#answer_string#]',
                           '[#is_single#]');

        $replace_values = array($node_course_question_categories_question_answer,
                                self::safexml($answer_string),
                                $is_single);

        $node_question_categories_question = str_replace($find_tags, $replace_values, $sheet_question_categories_question);

        return $node_question_categories_question;
    }

    private function create_node_course_question_categories_question_category_question_eesay ($question) {

        $node_course_question_categories_question_answer = '';

        $sheet_question_categories_question = cc2moodle::loadsheet(SHEET_COURSE_QUESTION_CATEGORIES_QUESTION_CATEGORY_QUESTION_EESAY);

        if (!empty($question['answers'])) {
            foreach ($question['answers'] as $answer) {
                $node_course_question_categories_question_answer .= $this->create_node_course_question_categories_question_category_question_answer($answer);
            }
        }

        $find_tags = array('[#node_course_question_categories_question_category_question_answer#]');
        $replace_values = array($node_course_question_categories_question_answer);

        $node_question_categories_question = str_replace($find_tags, $replace_values, $sheet_question_categories_question);

        return $node_question_categories_question;
    }

    private function create_node_course_question_categories_question_category_question_shortanswer ($question) { //, &$fib_questions) {

        $sheet_question_categories_question = cc2moodle::loadsheet(SHEET_COURSE_QUESTION_CATEGORIES_QUESTION_CATEGORY_QUESTION_SHORTANSWER);
        $node_course_question_categories_question_answer = '';

        if (!empty($question['answers'])) {
            foreach ($question['answers'] as $answer) {
                $node_course_question_categories_question_answer .= $this->create_node_course_question_categories_question_category_question_answer($answer);
            }
        }

        $answers_string = $this->get_answers_string($question['answers']);

        $use_case = 0;

        foreach ($question['answers'] as $answer) {

            if ($answer['case'] == 1) {
                $use_case = 1;
            }

        }

        $find_tags = array('[#answers_string#]',
                           '[#use_case#]',
                           '[#node_course_question_categories_question_category_question_answer#]');

        $replace_values = array(self::safexml($answers_string),
                                self::safexml($use_case),
                                $node_course_question_categories_question_answer);



        $node_question_categories_question = str_replace($find_tags, $replace_values, $sheet_question_categories_question);

        return $node_question_categories_question;

    }

    private function create_node_course_question_categories_question_category_question_true_false ($question) {

        $node_course_question_categories_question_answer = '';

        $sheet_question_categories_question = cc2moodle::loadsheet(SHEET_COURSE_QUESTION_CATEGORIES_QUESTION_CATEGORY_QUESTION_TRUE_FALSE);

        $max_score = 0;
        $true_answer_id = 0;
        $false_answer_id = 0;

        if (!empty($question['answers'])) {

            foreach ($question['answers'] as $answer) {
                if ($answer['score'] > $max_score) {
                    $max_score = $answer['score'];
                    $true_answer_id = $answer['id'];
                }

                $node_course_question_categories_question_answer .= $this->create_node_course_question_categories_question_category_question_answer($answer);
            }

            foreach ($question['answers'] as $answer) {

                if ($answer['id'] != $true_answer_id) {
                    $max_score = $answer['score'];
                    $false_answer_id = $answer['id'];
                }
            }
        }

        $find_tags = array('[#node_course_question_categories_question_category_question_answer#]',
                           '[#true_answer_id#]',
                           '[#false_answer_id#]');

        $replace_values = array($node_course_question_categories_question_answer,
                                $true_answer_id,
                                $false_answer_id);

        $node_question_categories_question = str_replace($find_tags, $replace_values, $sheet_question_categories_question);

        return $node_question_categories_question;
    }

    private function get_answers_string ($answers) {

        $answer_string = '';

        if (!empty($answers)) {
            foreach ($answers as $answer) {
                $answer_string .= $answer['id'] . ',';
            }
        }

        $answer_string = !empty($answer_string) ? substr($answer_string, 0, strlen($answer_string) - 1) : '';

        return $answer_string;

    }

    private function create_node_course_question_categories_question_category_question_answer ($answer) {

        $sheet_question_categories_question_answer = cc2moodle::loadsheet(SHEET_COURSE_QUESTION_CATEGORIES_QUESTION_CATEGORY_QUESTION_ANSWER);

        $find_tags = array('[#answer_id#]',
                           '[#answer_text#]',
                           '[#answer_score#]',
                           '[#answer_feedback#]');

        $replace_values = array($answer['id'],
                                self::safexml($answer['title']),
                                $answer['score'],
                                self::safexml($answer['feedback']));

        $node_question_categories_question_answer = str_replace($find_tags, $replace_values, $sheet_question_categories_question_answer);

        return $node_question_categories_question_answer;
    }

    private function get_question_type ($identifier, $assessment) {

        $xpath = cc2moodle::newx_path($assessment, cc2moodle::getquizns());

        $metadata = $xpath->query('//xmlns:item[@ident="' . $identifier . '"]/xmlns:itemmetadata/xmlns:qtimetadata/xmlns:qtimetadatafield');

        foreach ($metadata as $field) {

            $field_label = $xpath->query('xmlns:fieldlabel', $field);
            $field_label = !empty($field_label->item(0)->nodeValue) ? $field_label->item(0)->nodeValue : '';

            if ($field_label == 'cc_profile') {
                $field_entry = $xpath->query('xmlns:fieldentry', $field);
                $type = !empty($field_entry->item(0)->nodeValue) ? $field_entry->item(0)->nodeValue : '';
            }
        }

        $return_type = array();

        $return_type['moodle'] = '';
        $return_type['cc'] = $type;

        if ($type == CC_QUIZ_MULTIPLE_CHOICE) {
            $return_type['moodle'] = MOODLE_QUIZ_MULTIPLE_CHOICE;
        }
        if ($type == CC_QUIZ_MULTIPLE_RESPONSE) {
            $return_type['moodle'] = MOODLE_QUIZ_MULTIPLE_CHOICE;
        }
        if ($type == CC_QUIZ_TRUE_FALSE) {
            $return_type['moodle'] = MOODLE_QUIZ_TRUE_FALSE;
        }
        if ($type == CC_QUIZ_ESSAY) {
            $return_type['moodle'] = MOODLE_QUIZ_ESSAY;
        }
        if ($type == CC_QUIZ_FIB) {
            $return_type['moodle'] = MOODLE_QUIZ_SHORTANSWER;
        }
        if ($type == CC_QUIZ_PATTERN_MACHT) {
            $return_type['moodle'] = MOODLE_QUIZ_SHORTANSWER;
        }

        return $return_type;

    }
}
