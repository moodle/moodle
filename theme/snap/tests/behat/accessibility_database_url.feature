# This file is part of Moodle - http://moodle.org/
#
# Moodle is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# Moodle is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
#
# Tests for toggle course section visibility in non edit mode in snap.
#
# @package    theme_snap
# @author     Rafael Becerra rafael.becerrarodriguez@openlms.net
# @copyright  Copyright (c) 2019 Open LMS.
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_ax
Feature: Check that the correct attributes exists for URL field in a database activity template.

  Background:
    Given the following "users" exist:
      | username  | firstname  | lastname  | email                 |
      | teacher1  | Teacher    | 1         | teacher1@example.com  |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | teacher1  | C1      | editingteacher  |

  @javascript
  Scenario: Url type and Url autocomplete should exists for input Url in the "Add entry" for Database activity.
    And I change window size to "large"
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    # Create database activity and allow editing of
    # approved entries.
    And I add a "data" activity to course "C1" section "1" and I fill the form with:
      | Name              | Test database name |
      | Description       | Test               |
    And I click on "#course-toc .chapters h3:nth-of-type(2)" "css_element"
    And I click on "li.modtype_data a.mod-link" "css_element"
    # To generate the default templates.
    And I click on ".action-menu-trigger" "css_element"
    And I click on "URL" "link"
    And I set the field "Field name" to "Data URL"
    And I click on "Save" "button"
    And I should see "Field added"
    And I am on "Course 1" course homepage
    And I click on "#course-toc .chapters h3:nth-of-type(2)" "css_element"
    And I click on "li.modtype_data a.mod-link" "css_element"
    And I follow "Database"
    And I click on "Add entry" "button"
    And the "type" attribute of "input.mod-data-input.form-control.d-inline" "css_element" should contain "url"
    And the "autocomplete" attribute of "input.mod-data-input.form-control.d-inline" "css_element" should contain "url"
