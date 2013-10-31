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

// Define array structure that will provide all the info and links
// for the company admin block and navigation.

class iomad_admin_menu {

    public static function getmenu() {
        global $CFG, $SESSION, $USER;

        $edittitle = '';
        if (empty($SESSION->currenteditingcompany) && empty($USER->company)) {
            $edittitle = get_string('createcompany', 'block_iomad_company_admin');
        } else {
            $edittitle = get_string('editcompany', 'block_iomad_company_admin');
        }

        $returnarray = array(
            'editcompany' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('editcompany', 'block_iomad_company_admin'),
                'url' => 'company_edit_form.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icon' => 'editcompany',
            ),
            'addcompany' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('createcompany', 'block_iomad_company_admin'),
                'url' => 'company_edit_form.php?createnew=1',
                'cap' => 'block/iomad_company_admin:company_add',
                'icon' => 'newcompany',
            ),
            'editdepartments' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('editdepartment', 'block_iomad_company_admin'),
                'url' => 'company_department_create_form.php',
                'cap' => 'block/iomad_company_admin:edit_departments',
                'icon' => 'newcourse',
            ),
            'assignmanagers' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('assignmanagers', 'block_iomad_company_admin'),
                'url' => 'company_managers_form.php',
                'cap' => 'block/iomad_company_admin:company_manager',
                'icon' => 'manager',
            ),
            'userprofiles' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('userprofiles', 'block_iomad_company_admin'),
                'url' => 'company_user_profiles.php',
                'cap' => 'block/iomad_company_admin:company_user_profiles',
                'icon' => 'userprofiles',
            ),
            'assignusers' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('assignusers', 'block_iomad_company_admin'),
                'url' => 'company_users_form.php',
                'cap' => 'block/iomad_company_admin:company_user',
                'icon' => 'users',
            ),
            'assigncourses' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('assigncourses', 'block_iomad_company_admin'),
                'url' => 'company_courses_form.php',
                'cap' => 'block/iomad_company_admin:company_course',
                'icon' => 'courses',
            ),
            'createuser' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('createuser', 'block_iomad_company_admin'),
                'url' => 'company_user_create_form.php',
                'cap' => 'block/iomad_company_admin:user_create',
                'icon' => 'usernew',
            ),
            'edituser' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('edituser', 'block_iomad_company_admin'),
                'url' => 'editusers.php',
                'cap' => 'block/iomad_company_admin:user_create',
                'icon' => 'useredit',
            ),
            'assigntocompany' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('assigntocompany', 'block_iomad_company_admin'),
                'url' => 'company_users_form.php',
                'cap' => 'block/iomad_company_admin:company_user',
                'icon' => '',
            ),
            'enroluser' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('enroluser', 'block_iomad_company_admin'),
                'url' => 'company_course_users_form.php',
                'cap' => 'block/iomad_company_admin:company_course_users',
                'icon' => '',
            ),
            'uploadfromfile' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('user_upload_title', 'block_iomad_company_admin'),
                'url' => 'uploaduser.php',
                'cap' => 'block/iomad_company_admin:user_upload',
                'icon' => 'up',
            ),
            'downloadusers' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('users_download', 'block_iomad_company_admin'),
                'url' => 'user_bulk_download.php',
                'cap' => 'block/iomad_company_admin:user_upload',
                'icon' => 'down',
            ),
            'bulkusers' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('users_bulk', 'block_iomad_company_admin'),
                'url' => '/admin/user/user_bulk.php',
                'cap' => 'block/iomad_company_admin:company_add',
                'icon' => 'users',
            ),
            'createcourse' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('createcourse', 'block_iomad_company_admin'),
                'url' => 'company_course_create_form.php',
                'cap' => 'block/iomad_company_admin:createcourse',
                'icon' => 'newcourse',
            ),
            'assigntocompany' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('assigntocompany', 'block_iomad_company_admin'),
                'url' => 'company_courses_form.php',
                'cap' => 'block/iomad_company_admin:company_course',
                'icon' => 'courses',
            ),
            'managecourses' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('iomad_courses_title', 'block_iomad_company_admin'),
                'url' => 'iomad_courses_form.php',
                'cap' => 'block/iomad_company_admin:managecourses',
                'icon' => 'courses',
            ),
            'enroluser' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('enroluser', 'block_iomad_company_admin'),
                'url' => 'company_course_users_form.php',
                'cap' => 'block/iomad_company_admin:company_course_users',
                'icon' => 'users',
            ),
            'classrooms' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('classrooms', 'block_iomad_company_admin'),
                'url' => 'classroom_list.php',
                'cap' => 'block/iomad_company_admin:classrooms',
                'icon' => 'locations',
            ));
        $returnarray['manageiomadlicenses'] = array(
                'category' => 'LicenseAdmin',
                'tab' => 4,
                'name' => get_string('managelicenses', 'block_iomad_company_admin'),
                'url' => 'company_license_list.php',
                'cap' => 'block/iomad_company_admin:edit_licenses',
                'icon' => 'courses',
            );
        $returnarray['licenseusers'] = array(
                'category' => 'LicenseAdmin',
                'tab' => 4,
                'name' => get_string('licenseusers', 'block_iomad_company_admin'),
                'url' => 'company_license_users_form.php',
                'cap' => 'block/iomad_company_admin:allocate_licenses',
                'icon' => 'users',
            );

        $returnarray['EmailTemplates'] = array(
            'category' => 'CompanyAdmin',
            'tab' => 1,
            'name' => get_string('blocktitle', 'local_email'),
            'url' => '/local/email/template_list.php',
            'cap' => 'local/email:list',
            'icon' => 'emails'
        );

        $returnarray['ShopSettings_list'] = array(
            'category' => 'ECommerceAdmin',
            'tab' => 5,
            'name' => get_string('courses', 'block_iomad_commerce'),
            'url' => '/blocks/iomad_commerce/courselist.php',
            'cap' => 'block/iomad_commerce:admin_view',
            'icon' => 'courses'
        );
        $returnarray['Orders'] = array(
            'category' => 'ECommerceAdmin',
            'tab' => 5,
            'name' => get_string('orders', 'block_iomad_commerce'),
            'url' => '/blocks/iomad_commerce/orderlist.php',
            'cap' => 'block/iomad_commerce:admin_view',
            'icon' => 'money'
        );
        return $returnarray;
    }

}
