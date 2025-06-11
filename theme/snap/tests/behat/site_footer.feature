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
# Tests for site policy redirects.
#
# @package    theme_snap
# @copyright  Copyright (c) 2018 Open LMS
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_footer
Feature: As an admin, I should be able to set a site's footer on Snap theme.

  Background:
    Given the following config values are set as admin:
      | theme | snap |
      | linkadmincategories | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | user1    | User1     | 1        | user1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | initsections |
      | Course 1 | C1        | topics |      1       |

  @javascript
  Scenario: Admin sets a footer and it should be visible in the platform for other users.
    Given I log in as "admin"
    And I am on site homepage
    And "iframe" "css_element" should not be visible
    And I should not see "New footer"
    And I go to "Site administration > Appearance > Themes" in snap administration
    And I follow "Edit theme settings 'Snap'"
    Then I click on "Snap footer" "link"
    Then I should see "Site footer"
    And I set the following fields to these values:
      | Site footer | <iframe></iframe> <p>New footer</p>|
    And I click on "Save changes" "button"
    And I wait until the page is ready
    Then I click on "Snap footer" "link"
    Then I should see "New footer"
    And "iframe" "css_element" should be visible
    And I log out
    And I should see "New footer"
    Then "iframe" "css_element" should be visible
    And I log in as "user1"
    And I am on site homepage
    Then I should see "New footer"
    And "iframe" "css_element" should be visible
    And I log out

  @javascript
  Scenario: To top button renderer on the footer must appear when user scroll to the bottom.
    Given I log in as "admin"
    And I am on site homepage
    And "#goto-top-link" "css_element" should exist
    And "#goto-top-link" "css_element" should not be visible
    And I scroll to the bottom
    And "#goto-top-link" "css_element" should be visible
    And I click on "#goto-top-link > a" "css_element"
    And I wait until "#goto-top-link" "css_element" is not visible
    And "#goto-top-link" "css_element" should not be visible

  @javascript
  Scenario: To top button renderer on the footer must appear when user scroll to the bottom on a course and stay on the same section.
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And "#goto-top-link" "css_element" should exist
    And "#goto-top-link" "css_element" should not be visible
    And I should see "Welcome to your new course"
    And I follow "Section 1"
    And I should see "Untitled Section"
    And I should not see "Welcome to your new course"
    And I scroll to the bottom
    And "#goto-top-link" "css_element" should be visible
    And I click on "#goto-top-link > a" "css_element"
    And I wait until "#goto-top-link" "css_element" is not visible
    And I should see "Untitled Section"
    And I should not see "Welcome to your new course"

  @javascript
  Scenario: Go to Snap footer settings page, set colors for footer background color and footer text color, and see contrast message.
    Given I log in as "admin"
    And the following config values are set as admin:
      | linkadmincategories | 0 |
    And I am on site homepage
    And I go to "Site administration > Appearance > Themes" in snap administration
    And I follow "Edit theme settings 'Snap'"
    Then I click on "Snap footer" "link"
    And I should see "Snap footer"
    And I should see "Footer customization"
    And I should see "Footer background color"
    And I should see "Footer text color"
    And I should not see "This color combination doesn't comply"
    And I set the following fields to these values:
      | s_theme_snap_footerbg  | #000000 |
      | s_theme_snap_footertxt   | #000000 |
    And I click on "Save changes" "button"
    And I wait until the page is ready
    And I should see "Changes saved"
    Then I click on "Snap footer" "link"
    And I should see "This color combination doesn't comply"