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
 * Ases
 *
 * @author     Iader E. García Gómez
 * @package    block_generalreports
 * @copyright  2016 Iader E. García <iadergg@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
namespace block_ases\output;

defined('MOODLE_INTERNAL') || die;

use plugin_renderer_base;

class renderer extends plugin_renderer_base {

    public function render_ases_report_page($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/ases_report', $data);
    }

    public function render_upload_files_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/upload_files', $data);
    }

    public function render_student_profile_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/student_profile', $data);
    }

    public function render_user_management_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/user_management', $data);
    }

    public function render_not_assigned_students_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/not_assigned_students', $data);
    }

    public function render_attendance_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/attendance', $data);
    }

    public function render_groupal_tracking_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/groupal_tracking', $data);
    }

    public function render_psicosocial_users_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/psicosocial_users', $data);
    }

    public function render_instance_configuration_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/instance_configuration', $data);
    }

    public function render_grade_categories_page($page){
     $data = $page->export_for_template($this);
     return parent::render_from_template('block_ases/grade_categories', $data);
    }

    public function render_general_reports_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/general_reports', $data);
    }

    public function render_report_trackings_page($page){
    $data = $page->export_for_template($this);
     return parent::render_from_template('block_ases/report_trackings', $data);
    }

    public function render_mass_role_management($page){
     $data = $page->export_for_template($this);
     return parent::render_from_template('block_ases/mass_role_management', $data);
    }

    public function render_permisos_rol_page($page){
     $data = $page->export_for_template($this);
     return parent::render_from_template('block_ases/permisos_rol', $data);
    }

    public function render_create_action_page($page){
     $data = $page->export_for_template($this);
     return parent::render_from_template('block_ases/create_action', $data);
    }

    public function render_tracking_time_control_page($page){
     $data = $page->export_for_template($this);
     return parent::render_from_template('block_ases/tracking_time_control', $data);
    }

    public function render_create_view_page($page){
     $data = $page->export_for_template($this);
     return parent::render_from_template('block_ases/create_view', $data);
    }

    public function render_no_tiene_permisos_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/no_tiene_permisos', $data);
    }

    public function render_global_grade_book_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/global_grade_book', $data);
    }

    public function render_report_grade_book_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/report_grade_book', $data);
    }

    public function render_academic_reports_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/academic_reports', $data);
    }

    public function render_historic_academic_reports_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/historic_academic_reports', $data);
    }

    public function render_index_sistemas_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/index_sistemas', $data);
    }

    public function render_periods_management_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/periods_management', $data);
    }

    public function render_dphpforms_form_builder_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/dphpforms_form_builder', $data);
    }

    public function render_upload_historical_files_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/upload_historical_files', $data);
    }

    public function render_historical_icetex_reports_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/historical_icetex_reports', $data);
    }

    public function render_dphpforms_form_editor_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/dphpforms_form_editor', $data);
    }

    public function render_dphpforms_alias_editor_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/dphpforms_alias_editor', $data);
    }

    public function render_dphpforms_form_editor_preguntas_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/dphpforms_form_editor_preguntas', $data);
    }

    public function render_dphpforms_form_editor_permiso_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/dphpforms_form_editor_permiso', $data);
    }

    public function render_dphpforms_form_editor_pregunta_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/dphpforms_form_editor_pregunta', $data);
    }

    public function render_dphpforms_form_creator_pregunta_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/dphpforms_form_creator_pregunta', $data);
    }

    public function render_dphpforms_form_editor_comportamientos_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/dphpforms_form_editor_comportamientos', $data);
    }

    public function render_dphpforms_reports_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/dphpforms_reports', $data);
    }

    public function render_teachers_reports_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/teachers_reports', $data);
    }

    public function render_students_finalgrade_report_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/students_finalgrade_report', $data);
    }

    public function render_monitor_assignments_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/monitor_assignments', $data);
    }

    public function render_backup_forms_page($page){
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_ases/backup_forms', $data);
    }


}
