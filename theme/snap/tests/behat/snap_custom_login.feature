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
# Tests for visibility of activity restriction tags.
#
# @package    theme_snap
# @copyright Copyright (c) 2018 Open LMS
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_login
Feature: When the moodle theme is set to Snap, the custom snap login form should be shown.

  @javascript
  Scenario: The login template must contain the custom snap form.
    Given I am on login page
    And I check element "#login" has class "snap-custom-form"

  @javascript
  Scenario: The login template must change when the Stylish template is selected.
    Given I log in as "admin"
    And the following config values are set as admin:
      | linkadmincategories | 0 |
    And I am on site homepage
    And I go to "Site administration > Appearance > Themes" in snap administration
    And I follow "Edit theme settings 'Snap'"
    And I click on "Login page" "link"
    And I should see "Stylish template"
    And I set the field with xpath "//select[@id='id_s_theme_snap_loginpagetemplate']" to "Stylish"
    And I click on "Save changes" "button"
    And I log out
    And I am on login page
    Then ".page-stylish-login" "css_element" should exist

  @javascript
  Scenario: The login password toggle must be displayed in Snap login page.
    Given the following config values are set as admin:
      | loginpasswordtoggle | 0 |
    And I am on login page
    And ".toggle-sensitive-btn" "css_element" should not exist
    Then the following config values are set as admin:
      | loginpasswordtoggle | 1 |
    And I am on login page
    And ".toggle-sensitive-btn" "css_element" should exist
    And I set the field "password" to "This is a password"
    And ".toggle-sensitive-wrapper input[type='text'] " "css_element" should not exist
    And I click on ".toggle-sensitive-btn" "css_element"
    And ".toggle-sensitive-wrapper input[type='text'] " "css_element" should exist
    And I click on ".toggle-sensitive-btn" "css_element"
    And ".toggle-sensitive-wrapper input[type='text'] " "css_element" should not exist
    Then the following config values are set as admin:
      | loginpasswordtoggle | 2 |
    And I am on login page
    And ".toggle-sensitive-btn" "css_element" should not be visible
    And I change window size to "320x480"
    And ".toggle-sensitive-btn" "css_element" should be visible

  @javascript
  Scenario: A guest user can login using the Snap login page.
    Given I am on login page
    And "#page-mast" "css_element" should not be visible
    And I press "Access as a guest"
    And "#page-mast" "css_element" should be visible
    And I should not see "Invalid login, please try again"