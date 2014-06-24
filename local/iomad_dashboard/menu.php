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
            'managecompanies' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('managecompanies', 'block_iomad_company_admin'),
                'url' => 'editcompanies.php',
                'cap' => 'block/iomad_company_admin:company_add',
                'icondefault' => 'editcompany',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-gear'
            ),
            'editcompany' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('editcompany', 'block_iomad_company_admin'),
                'url' => 'company_edit_form.php',
                'cap' => 'block/iomad_company_admin:company_edit',
                'icondefault' => 'editcompany',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-edit'
            ),
            'addcompany' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('createcompany', 'block_iomad_company_admin'),
                'url' => 'company_edit_form.php?createnew=1',
                'cap' => 'block/iomad_company_admin:company_add',
                'icondefault' => 'newcompany',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-plus-square'
            ),
            'editdepartments' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('editdepartment', 'block_iomad_company_admin'),
                'url' => 'company_department_create_form.php',
                'cap' => 'block/iomad_company_admin:edit_departments',
                'icondefault' => 'managedepartment',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-gear'
            ),
            'assignmanagers' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('assignmanagers', 'block_iomad_company_admin'),
                'url' => 'company_managers_form.php',
                'cap' => 'block/iomad_company_admin:company_manager',
                'icondefault' => 'assigndepartmentusers',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-chevron-circle-right'
            ),
            'userprofiles' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('userprofiles', 'block_iomad_company_admin'),
                'url' => 'company_user_profiles.php',
                'cap' => 'block/iomad_company_admin:company_user_profiles',
                'icondefault' => 'optionalprofiles',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-info-circle'
            ),
            'assignusers' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('assignusers', 'block_iomad_company_admin'),
                'url' => 'company_users_form.php',
                'cap' => 'block/iomad_company_admin:company_user',
                'icondefault' => 'assignusers',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-chevron-circle-right'
            ),
            'assigncourses' => array(
                'category' => 'CompanyAdmin',
                'tab' => 1,
                'name' => get_string('assigncourses', 'block_iomad_company_admin'),
                'url' => 'company_courses_form.php',
                'cap' => 'block/iomad_company_admin:company_course',
                'icondefault' => 'assigncourses',
                'icon' => 'fa-file-text',
                'iconsmall' => 'fa-chevron-circle-right'
            ),
            'createuser' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('createuser', 'block_iomad_company_admin'),
                'url' => 'company_user_create_form.php',
                'cap' => 'block/iomad_company_admin:user_create',
                'icondefault' => 'usernew',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-plus-square',
            ),
            'edituser' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('edituser', 'block_iomad_company_admin'),
                'url' => 'editusers.php',
                'cap' => 'block/iomad_company_admin:user_create',
                'icondefault' => 'useredit',
                'icon' => 'fa-user',
                'iconsmall' => 'fa-gear',
            ),
            'assigntocompany' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('assigntocompany', 'block_iomad_company_admin'),
                'url' => 'company_users_form.php',
                'cap' => 'block/iomad_company_admin:company_user',
                'icondefault' => '',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-chevron-circle-left',
            ),
            'enroluser' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('enroluser', 'block_iomad_company_admin'),
                'url' => 'company_course_users_form.php',
                'cap' => 'block/iomad_company_admin:company_course_users',
                'icondefault' => '',
                'icon' => 'fa-file-text',
                'iconsmal' => 'fa-user',
            ),
            'uploadfromfile' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('user_upload_title', 'block_iomad_company_admin'),
                'url' => 'uploaduser.php',
                'cap' => 'block/iomad_company_admin:user_upload',
                'icondefault' => 'up',
                'icon' => 'fa-file',
                'iconsmall' => 'fa-upload',

            ),
            'downloadusers' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('users_download', 'block_iomad_company_admin'),
                'url' => 'user_bulk_download.php',
                'cap' => 'block/iomad_company_admin:user_upload',
                'icondefault' => 'down',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-download',
            ),
            'bulkusers' => array(
                'category' => 'UserAdmin',
                'tab' => 2,
                'name' => get_string('users_bulk', 'block_iomad_company_admin'),
                'url' => '/admin/user/user_bulk.php',
                'cap' => 'block/iomad_company_admin:company_add',
                'icondefault' => 'users',
                'icon' => 'fa-group',
                'iconsmall' => 'fa-reply-all'
            ),
            'createcourse' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('createcourse', 'block_iomad_company_admin'),
                'url' => 'company_course_create_form.php',
                'cap' => 'block/iomad_company_admin:createcourse',
                'icondefault' => 'createcourse',
                'icon' => 'fa-file-text',
                'iconsmall' => 'fa-plus-square',
            ),
            'assigntocompany' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('assigntocompany', 'block_iomad_company_admin'),
                'url' => 'company_courses_form.php',
                'cap' => 'block/iomad_company_admin:company_course',
                'icondefault' => 'assigntocompany',
                'icon' => 'fa-building',
                'iconsmall' => 'fa-chevron-circle-left'
            ),
            'managecourses' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('iomad_courses_title', 'block_iomad_company_admin'),
                'url' => 'iomad_courses_form.php',
                'cap' => 'block/iomad_company_admin:managecourses',
                'icondefault' => 'managecoursesettings',
                'icon' => 'fa-file-text',
                'iconsmall' => 'fa-gear',
            ),
            'enroluser' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('enroluser', 'block_iomad_company_admin'),
                'url' => 'company_course_users_form.php',
                'cap' => 'block/iomad_company_admin:company_course_users',
                'icondefault' => 'userenrolements',
                'icon' => 'fa-file-text',
                'iconsmall' => 'fa-user',
            ),
            'classrooms' => array(
                'category' => 'CourseAdmin',
                'tab' => 3,
                'name' => get_string('classrooms', 'block_iomad_company_admin'),
                'url' => 'classroom_list.php',
                'cap' => 'block/iomad_company_admin:classrooms',
                'icondefault' => 'teachinglocations',
                'icon' => 'fa-map-marker',
                'iconsmall' => 'fa-gear',
            ));
        $returnarray['manageiomadlicenses'] = array(
                'category' => 'LicenseAdmin',
                'tab' => 4,
                'name' => get_string('managelicenses', 'block_iomad_company_admin'),
                'url' => 'company_license_list.php',
                'cap' => 'block/iomad_company_admin:edit_licenses',
                'icondefault' => 'licensemanagement',
                'icon' => 'fa-legal',
                'iconsmall' => 'fa-gear',
            );
        $returnarray['licenseusers'] = array(
                'category' => 'LicenseAdmin',
                'tab' => 4,
                'name' => get_string('licenseusers', 'block_iomad_company_admin'),
                'url' => 'company_license_users_form.php',
                'cap' => 'block/iomad_company_admin:allocate_licenses',
                'icondefault' => 'userlicenseallocations',
                'icon' => 'fa-legal',
                'iconsmall' => 'fa-user'
            );

        $returnarray['EmailTemplates'] = array(
            'category' => 'CompanyAdmin',
            'tab' => 1,
            'name' => get_string('blocktitle', 'local_email'),
            'url' => '/local/email/template_list.php',
            'cap' => 'local/email:list',
            'icondefault' => 'emailtemplates',
            'icon' => 'fa-inbox',
            'iconsmall' => 'fa-gear'
        );

        $returnarray['ShopSettings_list'] = array(
            'category' => 'ECommerceAdmin',
            'tab' => 5,
            'name' => get_string('courses', 'block_iomad_commerce'),
            'url' => '/blocks/iomad_commerce/courselist.php',
            'cap' => 'block/iomad_commerce:admin_view',
            'icondefault' => 'courses',
            'icon' => 'fa-file-text',
            'iconsmall' => 'fa-money'
        );
        $returnarray['Orders'] = array(
            'category' => 'ECommerceAdmin',
            'tab' => 5,
            'name' => get_string('orders', 'block_iomad_commerce'),
            'url' => '/blocks/iomad_commerce/orderlist.php',
            'cap' => 'block/iomad_commerce:admin_view',
            'icondefault' => 'orders',
            'icon' => 'fa-truck',
            'iconsmall' => 'fa-eye'
        );
        return $returnarray;
    }

}
