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
 * Web interface for generating plugins.
 *
 * @package    tool_pluginskel
 * @copyright  2016 Alexandru Elisei
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use Monolog\Logger;
use Monolog\Handler\BrowserConsoleHandler;

// @codingStandardsIgnoreStart
if (!empty($_REQUEST['buttondownloadskel'])) {
    // We are going to download a ZIP file via this script. Disable debugging
    // to prevent corruption of the file. This must be done before booting up
    // the Moodle core so we need to use the low-level access to the
    // superlobal $_REQUEST here and make the code checker to ignore this.
    define('NO_DEBUG_DISPLAY', true);
}
// @codingStandardsIgnoreEnd

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/moodlelib.php');
require_once(__DIR__.'/vendor/autoload.php');

admin_externalpage_setup('tool_pluginskel');

$url = new moodle_url('/admin/tool/pluginskel/index.php');
$PAGE->set_url($url);
$PAGE->set_title(get_string('generateskel', 'tool_pluginskel'));
$PAGE->set_heading(get_string('generateskel', 'tool_pluginskel'));

$step = optional_param('step', '0', PARAM_INT);
$component = optional_param('component1', '', PARAM_TEXT);

$returnurl = new moodle_url('/admin/tool/pluginskel/index.php');

if ($step == 0) {

    $mform0 = new tool_pluginskel_step0_form();
    $formdata = $mform0->get_data();
    $PAGE->requires->js_call_amd('tool_pluginskel/showtypeprefix', 'init');

    if (!empty($formdata)) {

        $data = array();
        $recipe = array();
        $componenttype = '';

        if (!empty($formdata->proceedmanually)) {

            if (empty($formdata->componentname)) {
                throw new moodle_exception('emptypluginname', 'tool_pluginskel', $returnurl);
            }

            $recipe['component'] = $formdata->componenttype.'_'.$formdata->componentname;
            $componenttype = $formdata->componenttype;

        } else {

            if (!empty($formdata->proceedrecipefile)) {
                $recipestring = $mform0->get_file_content('recipefile');
            } else if (!empty($formdata->proceedrecipe)) {
                $recipestring = $formdata->recipe;
            }

            if (empty($recipestring)) {
                throw new moodle_exception('emptyrecipecontent', 'tool_pluginskel', $returnurl);
            }

            $recipe = tool_pluginskel\local\util\yaml::decode_string($recipestring);
            list($componenttype, $componentname) = core_component::normalize_component($recipe['component']);

            $generalvars = tool_pluginskel\local\util\manager::get_general_variables();
            $componentvars = tool_pluginskel\local\util\manager::get_component_variables($recipe['component']);
            $featuresvars = tool_pluginskel\local\util\manager::get_features_variables();

            $rootvars = array_merge($generalvars, $featuresvars);
            $rootvarscount = tool_pluginskel\local\util\index_helper::get_array_variable_count_from_recipe($rootvars, $recipe);

            $componentfeatures = $componenttype.'_features';
            $componentvarscount = array();
            if (!empty($recipe[$componentfeatures])) {
                $componentvarscount = tool_pluginskel\local\util\index_helper::get_array_variable_count_from_recipe(
                    $componentvars,
                    $recipe[$componentfeatures],
                    $componentfeatures
                );
            }

            $data = array_merge($rootvarscount, $componentvarscount);
        }

        $data['recipe'] = $recipe;

        $mform1 = new tool_pluginskel_step1_form(null, $data);
        $PAGE->requires->js_call_amd('tool_pluginskel/addmore', 'addMore');

        echo $OUTPUT->header();
        $mform1->display();
        echo $OUTPUT->footer();

    } else {

        echo $OUTPUT->header();
        $mform0->display();
        echo $OUTPUT->footer();

    }

} else if ($step == 1) {

    // Reconstructing the form elements.
    $generalvars = tool_pluginskel\local\util\manager::get_general_variables();
    $componentvars = tool_pluginskel\local\util\manager::get_component_variables($component);
    $featuresvars = tool_pluginskel\local\util\manager::get_features_variables();

    $rootvars = array_merge($generalvars, $featuresvars);
    $rootvarscount = tool_pluginskel\local\util\index_helper::get_array_variable_count_from_form($rootvars);

    list($componenttype, $componentname) = core_component::normalize_component($component);
    $componentfeatures = $componenttype.'_features';
    $componentvarscount = tool_pluginskel\local\util\index_helper::get_array_variable_count_from_form($componentvars,
                                                                                                      $componentfeatures);

    $data = array_merge($rootvarscount, $componentvarscount);
    $data['recipe'] = array('component' => $component);

    $mform1 = new tool_pluginskel_step1_form(null, $data);
    $formdata = (array) $mform1->get_data();

    $recipe = $mform1->get_recipe();

    if (!empty($formdata['buttondownloadskel'])) {

        tool_pluginskel\local\util\index_helper::download_plugin_skeleton($recipe);

    } else if (!empty($formdata['buttondownloadrecipe'])) {

        $recipestring = tool_pluginskel\local\util\yaml::encode($recipe);
        tool_pluginskel\local\util\index_helper::download_recipe($recipestring);

    } else if (!empty($formdata['buttonshowrecipe'])) {

        $data = array('recipe' => $recipe);
        $mform2 = new tool_pluginskel_step2_form(null, $data);

        echo $OUTPUT->header();
        $mform2->display();
        echo $OUTPUT->footer();

    }

} else if ($step == 2) {

    // Reconstruct the form.
    $recipestub = array('component' => $component);
    $data = array('recipe' => $recipestub);
    $mform2 = new tool_pluginskel_step2_form(null, $data);
    $formdata = (array) $mform2->get_data();

    $recipestring = $formdata['recipe'];

    if (!empty($formdata['buttondownloadrecipe'])) {

        tool_pluginskel\local\util\index_helper::download_recipe($recipestring);

    } else if (!empty($formdata['buttondownloadskel'])) {

        $recipe = tool_pluginskel\local\util\yaml::decode_string($recipestring);
        tool_pluginskel\local\util\index_helper::download_plugin_skeleton($recipe);

    } else if (!empty($formdata['buttonback'])) {

        $recipe = tool_pluginskel\local\util\yaml::decode_string($recipestring);

        $generalvars = tool_pluginskel\local\util\manager::get_general_variables();
        $componentvars = tool_pluginskel\local\util\manager::get_component_variables($recipe['component']);
        $featuresvars = tool_pluginskel\local\util\manager::get_features_variables();

        $rootvars = array_merge($generalvars, $featuresvars);
        $rootvarscount = tool_pluginskel\local\util\index_helper::get_array_variable_count_from_recipe($rootvars, $recipe);

        list($componenttype, $componentname) = core_component::normalize_component($component);
        $componentfeatures = $componenttype.'_features';
        $componentvarscount = array();
        if (!empty($recipe[$componentfeatures])) {
            $componentvarscount = tool_pluginskel\local\util\index_helper::get_array_variable_count_from_recipe(
                $componentvars,
                $recipe[$componentfeatures],
                $componentfeatures
            );
        }

        $data = array_merge($rootvarscount, $componentvarscount);
        $data['recipe'] = $recipe;

        $mform1 = new tool_pluginskel_step1_form(null, $data);
        $PAGE->requires->js_call_amd('tool_pluginskel/addmore', 'addMore');

        echo $OUTPUT->header();
        $mform1->display();
        echo $OUTPUT->footer();
    }
}
