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
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once( '../../config.php');

// We always require users to be logged in for this page.
require_login();

// Get parameters.
$edit = optional_param( 'edit', null, PARAM_BOOL );
$company = optional_param('company', 0, PARAM_INT);
$showsuspendedcompanies = optional_param('showsuspendedcompanies', false, PARAM_BOOL);
$noticeok = optional_param('noticeok', '', PARAM_CLEAN);
$noticefail = optional_param('noticefail', '', PARAM_CLEAN);

$SESSION->showsuspendedcompanies = $showsuspendedcompanies;

$systemcontext = context_system::instance();

// Set the session to a user if they are editing a company other than their own.
if (!empty($company) && ( iomad::has_capability('block/iomad_company_admin:company_add', $systemcontext)
    || $DB->get_record('company_users', array('managertype' => 1, 'companyid' => $company, 'userid' => $USER->id)))) {
    $SESSION->currenteditingcompany = $company;
}

// Check if there are any companies.
if (!$companycount = $DB->count_records('company')) {

    // If not redirect to create form.
    redirect(new moodle_url('/blocks/iomad_company_admin/company_edit_form.php', ['createnew' => 1]));
}

// If we don't have one selected pick the first of these.
if (empty($SESSION->currenteditingcompany)) {
    if (iomad::is_company_user()) {
        $company = iomad::companyid();
        $SESSION->currenteditingcompany = $company;
    } else {
        // Otherwise, make the first (or only) company the current one
        $companies = $DB->get_records('company');
        $firstcompany = reset($companies);
        $SESSION->currenteditingcompany = $firstcompany->id;
        $company = $firstcompany->id;
    }
}

// Page setup stuff.
$PAGE->set_context($systemcontext);
$PAGE->set_url(new moodle_url('/blocks/iomad_company_admin/index.php'));
$PAGE->set_pagelayout('base');
$PAGE->set_title(get_string('dashboard', 'block_iomad_company_admin'));
$PAGE->requires->js_call_amd('block_iomad_company_admin/admin', 'init');
// Renderer
$renderer = $PAGE->get_renderer('block_iomad_company_admin');

// get output renderer
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Set the page heading.
$PAGE->set_heading(get_string('dashboard', 'block_iomad_company_admin'));

// Set the current tab to stick.
if (!empty($selectedtab)) {
    $SESSION->iomad_company_admin_tab = $selectedtab;
} else if (!empty($SESSION->iomad_company_admin_tab)) {
    $selectedtab = $SESSION->iomad_company_admin_tab;
} else {
    $selectedtab = 1;
}

// If no selected company no point showing tabs.
if (!iomad::get_my_companyid($systemcontext, false)) {
    $this->content->text = '<div class="alert alert-warning">' . get_string('nocompanyselected', 'block_iomad_company_admin') . '</div>';
    return $this->content;
}

// Build tabs.
$tabs = [];
$panes = [];
$url = '/my';
$selected = true;
if (iomad::has_capability('block/iomad_company_admin:companymanagement_view', $systemcontext)) {
    $tabs[] = [
        'category' => 'CompanyAdmin',
        'icon' => 'fa-building',
        'selected' => $selected,
        'label' => get_string('companymanagement', 'block_iomad_company_admin')
    ];
    $panes[1] = ['category' => 'CompanyAdmin', 'items' => [], 'selected' => $selected];
    $selected = false;
}
if (iomad::has_capability('block/iomad_company_admin:usermanagement_view', $systemcontext)) {
    $tabs[] = [
        'category' => 'UserAdmin',
        'icon' => 'fa-user',
        'selected' => $selected,
        'label' => get_string('usermanagement', 'block_iomad_company_admin')
    ];
    $panes[2] = ['category' => 'UserAdmin', 'items' => [], 'selected' => $selected];
    $selected = false;
}
if (iomad::has_capability('block/iomad_company_admin:coursemanagement_view', $systemcontext)) {
    $tabs[] = [
        'category' => 'CourseAdmin',
        'icon' => 'fa-file-text',
        'selected' => $selected,
        'label' => get_string('coursemanagement', 'block_iomad_company_admin')
    ];
    $panes[3] = ['category' => 'CourseAdmin', 'items' => [], 'selected' => $selected];
    $selected = false;
}
if (iomad::has_capability('block/iomad_company_admin:licensemanagement_view', $systemcontext)) {
    $tabs[] = [
        'category' => 'LicenseAdmin',
        'icon' => 'fa-legal',
        'selected' => $selected,
        'label' => get_string('licensemanagement', 'block_iomad_company_admin')
    ];
    $panes[4] = ['category' => 'LicenseAdmin', 'items' => [], 'selected' => $selected];
    $selected = false;
}
if (iomad::has_capability('block/iomad_company_admin:competencymanagement_view', $systemcontext)) {
    $tabs[] = [
        'category' => 'CompetencyAdmin',
        'icon' => 'fa-cubes',
        'selected' => $selected,
        'label' => get_string('competencymanagement', 'block_iomad_company_admin')
    ];
    $panes[5] = ['category' => 'CompetencyAdmin', 'items' => [], 'selected' => $selected];
    $selected = false;
}
if (iomad::has_capability('block/iomad_commerce:admin_view', $systemcontext)) {
    $tabs[] = [
        'category' => 'ECommerceAdmin',
        'icon' => 'fa-truck',
        'selected' => $selected,
        'label' => get_string('blocktitle', 'block_iomad_commerce')
    ];
    $panes[6] = ['category' => 'ECommerceAdmin', 'items' => [], 'selected' => $selected];
    $selected = false;
}
if (iomad::has_capability('block/iomad_microlearning:view', $systemcontext)) {
    $tabs[] = [
        'category' => 'MicrolearningAdmin',
        'icon' => 'fa-microchip',
        'selected' => false,
        'label' => get_string('threads', 'block_iomad_microlearning')
    ];
    $panes[7] = ['category' => 'MicrolearningAdmin', 'items' => [], 'selected' => $selected];
    $selected = false;
}
if (iomad::has_capability('block/iomad_reports:view', $systemcontext)) {
    $tabs[] = [
        'category' => 'Reports',
        'icon' => 'fa-bar-chart-o',
        'selected' => $selected,
        'label' => get_string('reports', 'block_iomad_company_admin')
    ];
    $panes[8] = ['category' => 'Reports', 'items' => [], 'selected' => $selected];
    $selected = false;
}

// Build content for selected tab (from menu array).
$menus = [];
$plugins = get_plugins_with_function('menu', $file = 'db/iomadmenu.php', $include = true);
unset($plugins['block']['iomad_company_admin']);
$plugins['block'] = array('iomad_company_admin' => 'block_iomad_company_admin_menu') + $plugins['block'];
foreach ($plugins as $plugintype) {
    foreach ($plugintype as $plugin => $menufunction) {
        $menus += $menufunction();
    }
}


$somethingtodisplay = false;
foreach ($menus as $key => $menu) {
    $tab = $menu['tab'];

    // If no 'pane' for tab then move on
    if (empty($panes[$tab])) {
        continue;
    }

    // If no capability then move on.
    if (!iomad::has_capability($menu['cap'], $systemcontext)) {
        continue;
    }
    $somethingtodisplay = true;

    // Build correct url.
    if (substr($menu['url'], 0, 1) == '/') {
        $url = new moodle_url($menu['url']);
    } else {
        $url = new moodle_url('/blocks/iomad_company_admin/' . $menu['url']);
    }

    // Get topic image icon
    if (((empty($USER->theme) && (strpos($CFG->theme, 'iomad') !== false)) || (strpos($USER->theme, 'iomad') !== false))  && !empty($menu['icon'])) {
        $icon = $menu['icon'];
    } else if (!empty($menu['icondefault'])) {
        $imgsrc = $OUTPUT->image_url($menu['icondefault'], 'block_iomad_company_admin');
        $icon = '"><img src="'.$imgsrc.'" alt="'.$menu['name'].'" /></br';
    } else {
        $icon = '';
    }

    // Get topic action icon
    if (!empty($menu['iconsmall'])) {
        $iconsmall = $menu['iconsmall'];
    } else {
        $iconsmall = '';
    }

    // Get Action description
    if (!empty($menu['name'])) {
        $action = $menu['name'];
    } else {
        $action = '';
    }

    // Construct tabbed entry
    $menu['action'] = $action;
    $menu['iconsmall'] = $iconsmall;
    $menu['icon'] = $icon;
    $menu['url'] = $url;
    $panes[$tab]['items'][] = $menu;
}

// If there are no menu items to show this user...
if (!$somethingtodisplay) {
    $this->content = new stdClass;
    $this->content->text = '';
    return $this->content;
}

// Logo.
$logourl = $renderer->image_url('iomadlogo', 'block_iomad_company_admin');

// Company companyselect
$companyselect = new \stdClass;

// Only display if you have the correct capability, or you are not in more than one company.
// Just display name of current company if no choice.
if (!iomad::has_capability('block/iomad_company_admin:company_view_all', $systemcontext)) {
    if ($DB->count_records('company_users', array('userid' => $USER->id)) <= 1 ) {
        $companyuser = $DB->get_record('company_users', array('userid' => $USER->id), '*', MUST_EXIST);
        $company = $DB->get_record('company', array('id' => $companyuser->companyid), '*', MUST_EXIST);
        $companyselect->companyname = $company->name;
        $companyselect->onecompany = true;
    } else {

    // Possibly more than one company
    $companyselect->onecompany = false;
    }
}

$content = '';

//  Check users session and profile settings to get the current editing company.
if (!empty($SESSION->currenteditingcompany)) {
    $selectedcompany = $SESSION->currenteditingcompany;
} else if ($usercompany = company::by_userid($USER->id)) {
    $selectedcompany = $usercompany->id;
} else {
    $selectedcompany = "";
}

//  Check users session current show suspended setting.
if (!empty($SESSION->showsuspendedcompanies)) {
    $showsuspendedcompanies = $SESSION->showsuspendedcompanies;
} else {
    $showsuspendedcompanies = false;
}

// Get the company name if set.
if (!empty($selectedcompany)) {
    $companyname = company::get_companyname_byid($selectedcompany);
} else {
    $companyname = "";
}

// Get a list of companies if required.
if (!$companyselect->onecompany) {
    $companylist = company::get_companies_select($showsuspendedcompanies);
    $select = new \block_iomad_company_admin\forms\iomad_company_select_form(new moodle_url('/blocks/iomad_company_admin/index.php'), $companylist, $selectedcompany);
    $select->set_data(array('company' => $selectedcompany, 'showsuspendedcompanies' => $showsuspendedcompanies));
    $companyselect->selectform = $select->render();
    if (!$showsuspendedcompanies) {
        $companyselect->suspended = $OUTPUT->single_button(new moodle_url('/my/index.php',
                                           array('showsuspendedcompanies' => true)),
                                           get_string("show_suspended_companies", 'block_iomad_company_admin'));
    } else {
        $companyselect->suspended = $OUTPUT->single_button(new moodle_url('/my/index.php',
                                           array('showsuspendedcompanies' => false)),
                                           get_string("hide_suspended_companies", 'block_iomad_company_admin'));
    }
}

// Render block.
$adminblock = new block_iomad_company_admin\output\adminblock($logourl, $companyselect, $tabs, $panes);


echo $output->header();
echo $renderer->render($adminblock);
echo $output->footer();

