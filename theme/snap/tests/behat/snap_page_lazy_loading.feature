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
# Test lazy loading for page resources.
#
# @package    theme_snap
# @author     Diego Casas <diego.casas@openlms.net>
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_lazy_loading @snap_page_resource
Feature: When the moodle theme is set to Snap course pages can be rendered using lazy loading.
  Background:
    Given I skip because "to debug Behat test failure in GL Pipeline in INT-19721"
    Given the following "courses" exist:
      | fullname | shortname | initsections |
      | Course 1 |     C1    |      1       |
    Given the following "users" exist:
      | username  | firstname  | lastname  | email                 |
      | teacher1  | Teacher    | 1         | teacher1@example.com  |
      | student1  | Student    | 1         | student1@example.com  |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | student1  | C1      | student         |
      | teacher1  | C1      | editingteacher  |
      | admin     | C1      | editingteacher  |
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I add a page activity to course "C1" section "1" and I fill the form with:
      | Name         | Test Page        |
      | Description  | Test description |
      | Page content | <p>Test Content</p><img src="https://download.moodle.org/unittest/test.jpg" alt="test image" width="200" height="150" class="img-responsive atto_image_button_text-bottom"> |
    And I log out

  @javascript
  Scenario Outline: Check if Page content is being lazy loaded
    Given the following config values are set as admin:
      | lazyload_mod_page | <lazyload>        | theme_snap |
    And I log in as "teacher1"
    And I am on the course "C1"
    And I follow "Section 1"
    And I should see "Test Page"
    And ".pagemod-content" "css_element" should exist
    And ".pagemod-readmore" "css_element" <exist>
    And "<class>" "css_element" should exist
    Examples:
      | lazyload | class                                       | exist            |
      | 0        | .pagemod-content[data-content-loaded=\"1\"] | should exist     |
      | 1        | .pagemod-content[data-content-loaded=\"0\"] | should exist     |

  @javascript
  Scenario: Check the file tree of a folder when lazy load is active
    Given the following config values are set as admin:
      | coursepartialrender | 1 | theme_snap |
    And the following "activities" exist:
      | activity | name               | intro                   | course | idnumber | showexpanded | section | display |
      | folder   | Test folder name 1 | Test folder description | C1     | folder1  | 1            | 0       | 1       |
      | folder   | Test folder name 2 | Test folder description | C1     | folder2  | 1            | 1       | 1       |
    And I log in as "teacher1"
    And I am on the course "C1"
    Then ".modtype_folder table" "css_element" should exist
    And I follow "Section 1"
    Then ".modtype_folder table" "css_element" should exist
    And I follow "Introduction"
    Then ".modtype_folder table" "css_element" should exist
