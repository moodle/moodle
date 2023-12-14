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
 * Configure user factor page
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

use tool_mfa\local\form\setup_factor_form;
use tool_mfa\local\form\revoke_factor_form;

require_login(null, false);
if (isguestuser()) {
    throw new require_login_exception('error:isguestuser', 'tool_mfa');
}

$action = optional_param('action', '', PARAM_ALPHANUMEXT);
$factor = optional_param('factor', '', PARAM_ALPHANUMEXT);
$factorid = optional_param('factorid', '', PARAM_INT);

$params = ['action' => $action, 'factor' => $factor, 'factorid' => $factorid];
$currenturl = new moodle_url('/admin/tool/mfa/action.php', $params);

$returnurl = new moodle_url('/admin/tool/mfa/user_preferences.php');

if (empty($factor) || empty($action)) {
    throw new moodle_exception('error:directaccess', 'tool_mfa', $returnurl);
}

if (!\tool_mfa\plugininfo\factor::factor_exists($factor)) {
    throw new moodle_exception('error:factornotfound', 'tool_mfa', $returnurl, $factor);
}

if (!in_array($action, \tool_mfa\plugininfo\factor::get_factor_actions())) {
    throw new moodle_exception('error:actionnotfound', 'tool_mfa', $returnurl, $action);
}

if (!empty($factorid) && !\tool_mfa\manager::is_factorid_valid($factorid, $USER)) {
    throw new moodle_exception('error:incorrectfactorid', 'tool_mfa', $returnurl, $factorid);
}

$factorobject = \tool_mfa\plugininfo\factor::get_factor($factor);

$context = context_user::instance($USER->id);
$PAGE->set_context($context);
$PAGE->set_url('/admin/tool/mfa/action.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string($action.'factor', 'tool_mfa'));
$PAGE->set_cacheable(false);

if ($node = $PAGE->settingsnav->find('usercurrentsettings', null)) {
    $PAGE->navbar->add($node->get_content(), $node->action());
}
$PAGE->navbar->add(get_string('preferences:header', 'tool_mfa'), new \moodle_url('/admin/tool/mfa/user_preferences.php'));

switch ($action) {
    case 'setup':
        if (!$factorobject || !$factorobject->has_setup()) {
            redirect($returnurl);
        }

        $PAGE->navbar->add(get_string('setupfactor', 'factor_'.$factor));
        $OUTPUT = $PAGE->get_renderer('tool_mfa');
        $form = new setup_factor_form($currenturl, ['factorname' => $factor]);

        if ($form->is_submitted()) {
            $form->is_validated();

            if ($form->is_cancelled()) {
                $factorobject->setup_factor_form_is_cancelled($factorid);
                redirect($returnurl);
            }

            if ($data = $form->get_data()) {
                $record = $factorobject->setup_user_factor($data);
                if (!empty($record)) {
                    $factorobject->set_state(\tool_mfa\plugininfo\factor::STATE_PASS);
                    $finalurl = new moodle_url($returnurl, ['action' => 'setup', 'factorid' => $record->id]);
                    redirect($finalurl);
                }

                throw new moodle_exception('error:setupfactor', 'tool_mfa', $returnurl);
            }
        }

        echo $OUTPUT->header();
        $form->display();

        break;

    case 'revoke':
        // Ensure sesskey is valid.
        require_sesskey();

        if (!$factorobject || !$factorobject->has_revoke()) {
            throw new moodle_exception('error:revoke', 'tool_mfa', $returnurl);
        }

        $PAGE->navbar->add(get_string('action:revoke', 'factor_'.$factor));
        $OUTPUT = $PAGE->get_renderer('tool_mfa');

        $revokeparams = [
            'factorname' => $factorobject->get_display_name(),
            'devicename' => $factorobject->get_label($factorid),
        ];
        $form = new revoke_factor_form($currenturl, $revokeparams);

        if ($form->is_submitted()) {
            $form->is_validated();

            if ($form->is_cancelled()) {
                redirect($returnurl);
            }

            if ($form->get_data()) {
                if ($factorobject->revoke_user_factor($factorid)) {
                    $finalurl = new moodle_url($returnurl, ['action' => 'revoked', 'factorid' => $factorid]);
                    redirect($finalurl);
                }

                throw new moodle_exception('error:revoke', 'tool_mfa', $returnurl);
            }
        }

        echo $OUTPUT->header();
        $form->display();

        break;

    default:
        break;
}

echo $OUTPUT->footer();
