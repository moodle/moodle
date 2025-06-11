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
# Test the Category color setting for snap.
#
# @package   theme_snap
# @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net)
# @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_color_check @theme_snap_course
Feature: When the moodle theme is set to Snap, admins can change the color for a given category.

  Background:

    Given I create the following course categories:
      | id | name   | category | idnumber | description |
      |  5 | Cat  5 |     0    |   CAT5   |   Test      |
      | 10 | Cat 10 |   CAT5   |   CAT10  |   Test      |
      | 20 | Cat 20 |   CAT20  |   CAT20  |   Test      |
    And the following config values are set as admin:
      | linkadmincategories | 0 |

  @javascript
  Scenario: Go to Snap settings page and put a wrong JSON text in it.
    Given I log in as "admin"
    And I am on site homepage
    And I go to "Site administration > Appearance > Themes" in snap administration
    And I follow "Edit theme settings 'Snap'"
    And I should see "Category color"
    And I click on "Category color" "link"
    And I should see "JSON Text"
    And I set the following fields to these values:
      |  Color palette | #FFAAFF                                           |
      |    JSON Text   | This is more than 10 words. 1 2 3 4 5 6 7 8 9 10. |
    And I click on "Save changes" "button"
    And I wait until the page is ready
    And I should see "Some settings were not changed due to an error."
    And I click on "Category color" "link"
    And I should see "Incorrect JSON format for course categories"

  @javascript
  Scenario: Go to Snap settings page and put a valid JSON text in it.
    Given I log in as "admin"
    And I am on site homepage
    And I go to "Site administration > Appearance > Themes" in snap administration
    And I follow "Edit theme settings 'Snap'"
    And I should see "Category color"
    And I click on "Category color" "link"
    And I should see "JSON Text"
    And I set the following fields to these values:
      |  Color palette |      #FFAAFF                   |
      |    JSON Text   | {"5":"#FAAFFF","10":"#FABCF0"} |
    And I click on "Save changes" "button"
    And I wait until the page is ready
    And I should not see "Some settings were not changed due to an error."
    And the following fields match these values:
      |  Color palette |      #FFAAFF                   |
      |    JSON Text   | {"5":"#FAAFFF","10":"#FABCF0"} |

  @javascript
  Scenario: Go to Snap settings page and put a valid JSON text in it but with no existing categories.
    Given I log in as "admin"
    And I am on site homepage
    And I go to "Site administration > Appearance > Themes" in snap administration
    And I follow "Edit theme settings 'Snap'"
    And I should see "Category color"
    And I click on "Category color" "link"
    And I should see "JSON Text"
    And I set the following fields to these values:
      |  Color palette |      #FFAAFF     |
      |    JSON Text   | {"70":"#FAAFFF"} |
    And I click on "Save changes" "button"
    And I wait until the page is ready
    And I should see "Some settings were not changed due to an error."
    And I click on "Category color" "link"
    And I should see "The category record with id \"70\" hasn't been found"

  @javascript
  Scenario: Go to Snap settings page and put a not valid color in the JSON text.
    Given I log in as "admin"
    And I am on site homepage
    And I go to "Site administration > Appearance > Themes" in snap administration
    And I follow "Edit theme settings 'Snap'"
    And I should see "Category color"
    And I click on "Category color" "link"
    And I should see "JSON Text"
    And I set the following fields to these values:
      |  Color palette |    #FFAAFF   |
      |    JSON Text   | {"20":"#FA"} |
    And I click on "Save changes" "button"
    And I wait until the page is ready
    And I should see "Some settings were not changed due to an error."
    And I click on "Category color" "link"
    And I should see "Record id or color value for category \"20\" aren't valid"

  @javascript
  Scenario: Go to Snap settings page and put a wrong JSON text with duplicated IDs.
    Given I log in as "admin"
    And I am on site homepage
    And I go to "Site administration > Appearance > Themes" in snap administration
    And I follow "Edit theme settings 'Snap'"
    And I should see "Category color"
    And I click on "Category color" "link"
    And I should see "JSON Text"
    And I set the following fields to these values:
      |  Color palette | #FFAAFF                          |
      |    JSON Text   | {"10":"#FAAFFF", "10":"#0DAA00"} |
    And I click on "Save changes" "button"
    And I wait until the page is ready
    And I should see "Some settings were not changed due to an error."
    And I click on "Category color" "link"
    And I should see "Incorrect JSON format, some IDs are duplicated"
