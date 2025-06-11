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
# @author     David Castro <david.castro@openlms.net>
# @copyright  Copyright (c) 2019 Open LMS (https://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_course
Feature: When the moodle theme is set to Snap, section titles can be clicked for editing section information.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format | initsections |
      | Course 1 | C1        | 0        | topics |      1       |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |

  @javascript
  Scenario Outline: Untitled section titles direct to section edition when using topics format.
    Given I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <coursepartialrender> | theme_snap |
      | leftnav             | <leftnav>             | theme_snap |
    And I log out
    And I log in as "teacher1"
    And I am on the course main page for "C1"
    And I follow "Section 1"
    And I wait until the page is ready
    Then "#section-1 .content .sectionname .sectionnumber" "css_element" <titlenumber> exist
    Then I should see "<title>" in the "#section-1 .content .sectionname" "css_element"
    And I click on "#section-1 .content .sectionname a" "css_element"
    And I set the section name to "Super section 1"
    And I press "Save changes"
    And I follow "Super section 1"
    Then "#section-1 .content .sectionname a" "css_element" should not exist
    Then I should see "Super section 1" in the "#section-1 .content .sectionname" "css_element"
    Then "#section-1 .content .sectionname .sectionnumber" "css_element" <titlenumber> exist
    Examples:
      | coursepartialrender     | leftnav | title             | titlenumber |
      | 0                       | list    | Untitled Section    | should not  |
      | 1                       | list    | Untitled Section    | should not  |
      | 0                       | top     | Untitled Section    | should      |
      | 1                       | top     | 1.Untitled Section  | should      |
