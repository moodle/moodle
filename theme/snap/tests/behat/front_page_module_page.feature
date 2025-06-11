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
# Tests for page module behaviour at front page.
#
# @package    theme_snap
# @author     Guillermo Alvarez
# @copyright  2017 Blackboard Ltd
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @snap_page_resource
Feature: Open page (front page) module inline
  As any user
  I need to view page modules inline at front page.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And completion tracking is "Enabled" for course "Acceptance test site"
    And debugging is turned off
    And the following config values are set as admin:
      | lazyload_mod_page | 0 | theme_snap |

  @javascript
  Scenario: Page mod is created and opened inline at the front page.
    Given the following "activities" exist:
      | activity | course               | idnumber | name       | intro        | content       | completion | completionview | section |
      | page     | Acceptance test site | page1    | Test page1 | Test page 1  | page content1 | 0          | 0              | 1       |
    And I log in as "admin"
    And I am on site homepage
    And I should not see "page content1"
    And I click on "li .contentafterlink .pagemod-readmore" "css_element"
    And I should not see an error dialog
    And I wait until "#pagemod-content-container" "css_element" is visible
    # The above step basically waits for the page content to load up.
    And I should see "page content1"

  @javascript
  Scenario Outline: Page mod completion updates on read more and affects availability for other modules at the front page.
    Given the following "activities" exist:
      | activity | course               | idnumber  | name   | intro      | content      | section |
      | page     | Acceptance test site | pagec     | Page   | Page intro | Page content | 1       |
    Then I log in as "admin"
    And the following config values are set as admin:
      | resourcedisplay | <Option> | theme_snap |
    And I am on site homepage
    And I click on ".modtype_page .snap-edit-asset-more" "css_element"
    And I click on ".modtype_page .snap-edit-asset" "css_element"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "User profile" "button"
    And I set the field "User profile field" to "Email address"
    And I set the field "Value to compare against" to "student2@example.com"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I click on "Save and return to course" "button"
    And I click on "//a[@class='snap-conditional-tag']" "xpath_element"
    And I should see "Not available unless: Your Email address is student2@example.com"
    And I log out
    And I log in as "student1"
    And I am on site homepage
    And I should not see "Page intro"
    And I log out
    And I log in as "student2"
    And I am on site homepage
    And I click on "li .contentafterlink .pagemod-readmore" "css_element"
    And I should not see an error dialog
    And I wait until "#pagemod-content-container" "css_element" is visible
    Then I should see "Page content"
    Examples:
      | Option     |
      | list       |
      | card       |

  @javascript
  Scenario: Page mod should be visible at the front page for users that are not logged in.
    Given the following "activities" exist:
      | activity | course               | idnumber | name       | intro        | content       | completion | completionview | section |
      | page     | Acceptance test site | page1    | Test page1 | Test page 1  | page content1 | 0          | 0              | 1       |
    And I log in as "admin"
    And I am on site homepage
    And I should see "Test page1"
    And I should not see "page content1"
    And I log out
    And I am on site homepage
    And I should not see "page content1"
    And I click on ".readmore-container button" "css_element"
    And I should not see an error dialog
    And I should see "page content1"
